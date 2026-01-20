<?php
session_start();
header('Content-Type: application/json');
require 'conexao.php';
require __DIR__ . '/../src/Services/OrderEventService.php';
require __DIR__ . '/../src/Services/OrderService.php';
require __DIR__ . '/../src/Services/CupomService.php';
require __DIR__ . '/../src/Services/NotificationService.php';

use Services\OrderEventService;
use Services\OrderService;
use Services\CupomService;
use Services\NotificationService;

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não logado.']);
    exit();
}
$usuario_id = $_SESSION['usuario_id'];

$dados = json_decode(file_get_contents('php://input'), true);
$cart = $dados['cart'] ?? [];
$endereco_id = $dados['endereco_id'] ?? null;
// $valor_total_frontend = (float)($dados['valor_total'] ?? 0); // Not used directly anymore, we recalculate per order

if (empty($cart)) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'O carrinho está vazio.']);
    exit();
}

try {
    $pdo->beginTransaction();

    $eventService = new OrderEventService($pdo);
    $orderService = new OrderService($pdo, $eventService);
    $notificationService = new NotificationService($pdo);

    // 1. Fetch product details to get supplier_id and real price
    // Group items by supplier_id
    $ordersBySupplier = [];

    // Get all product IDs
    $productIds = array_column($cart, 'id');
    if (empty($productIds)) {
        throw new Exception("Carrinho inválido.");
    }

    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $stmtProducts = $pdo->prepare("SELECT id, usuario_id, preco, estoque, nome FROM produtos WHERE id IN ($placeholders)");
    $stmtProducts->execute($productIds);
    $productsDb = $stmtProducts->fetchAll(PDO::FETCH_ASSOC);
    $productsMap = [];
    foreach ($productsDb as $p) {
        $productsMap[$p['id']] = $p;
    }

    foreach ($cart as $item) {
        $pid = $item['id'];
        $qtd = $item['quantidade'];

        if (!isset($productsMap[$pid])) {
            throw new Exception("Produto ID $pid não encontrado.");
        }

        $productDb = $productsMap[$pid];
        $supplierId = $productDb['usuario_id']; // This is the supplier

        if (!isset($ordersBySupplier[$supplierId])) {
            $ordersBySupplier[$supplierId] = [
                'items' => [],
                'total_itens' => 0.0,
                'supplier_id' => $supplierId
            ];
        }

        $ordersBySupplier[$supplierId]['items'][] = [
            'product_id' => $pid,
            'quantity' => $qtd,
            'price' => $productDb['preco'],
            'title' => $productDb['nome']
        ];
        $ordersBySupplier[$supplierId]['total_itens'] += ($productDb['preco'] * $qtd);
    }

    // 2. Process Cupom (if any)
    // IMPORTANT: Simplification - Check if coupon applies to total or split?
    // Usually coupons are 'Global' or 'Seller specific'. 
    // For this context, let's assume Global Coupon applies proportionally or simply to the first order?
    // Or better: Apply to entire cart sum, but we are splitting orders... 
    // Complexity: High. 
    // Decision: If a coupon exists, we will verify it against the GLOBAL total, 
    // then distribute the discount proportionally among orders.

    $cupom_id = null;
    $global_discount = 0.0;

    if (isset($_SESSION['checkout_cupom']['codigo'])) {
        $codigoCupom = $_SESSION['checkout_cupom']['codigo'];
        // Calculate global total
        $globalTotal = 0;
        foreach ($ordersBySupplier as $ord) {
            $globalTotal += $ord['total_itens'];
        }

        try {
            $cupomService = new CupomService($pdo);
            $validacao = $cupomService->validarCupom($codigoCupom, $globalTotal, $usuario_id);
            if ($validacao['valid']) {
                $cupom = $validacao['cupom'];
                $global_discount = (float)$validacao['desconto_calculado'];
                $cupom_id = $cupom->id;
            }
        } catch (Exception $e) {
            error_log("Erro cupom: " . $e->getMessage());
        }
    }

    $createdOrderIds = [];
    $totalGlobalNoDesconto = 0;
    foreach ($ordersBySupplier as $sId => $orderData) {
        $totalGlobalNoDesconto += $orderData['total_itens'];
    }

    // 3. Create Orders
    foreach ($ordersBySupplier as $sId => $orderData) {
        $subTotal = $orderData['total_itens'];

        // Proportional discount
        $myDiscount = 0.0;
        if ($global_discount > 0 && $totalGlobalNoDesconto > 0) {
            $ratio = $subTotal / $totalGlobalNoDesconto;
            $myDiscount = round($global_discount * $ratio, 2);
        }

        $finalTotal = max(0, $subTotal - $myDiscount);

        // Insert Pedido
        $stmtInsert = $pdo->prepare("
            INSERT INTO pedidos 
            (usuario_id, endereco_id, supplier_id, valor_total, status, cupom_id, valor_desconto, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");

        // Status initial: CREATED
        $initialStatus = OrderService::STATUS_CREATED;

        $stmtInsert->execute([
            $usuario_id,
            $endereco_id, // null if agency? Logic preserved from original
            $sId,
            $finalTotal,
            $initialStatus,
            $cupom_id, // We log the same coupon ID for all orders it seems reasonable
            $myDiscount
        ]);

        $pedidoId = $pdo->lastInsertId();
        $createdOrderIds[] = $pedidoId;

        // Log event logic via OrderService? 
        // OrderService::transitionTo is for changes. Here it's creation.
        // Let's manually log usage of event service for creation.
        $eventService->logEvent($pedidoId, null, $initialStatus, 'client', $usuario_id, 'Pedido criado');

        // NOTIFY SUPPLIER
        try {
            if ($sId != $usuario_id) {
                // Determine supplier name or just generic message
                $notificationService->create(
                    $sId,
                    "Novo pedido #$pedidoId recebido! Valor: R$ " . number_format($finalTotal, 2, ',', '.'),
                    'success',
                    'tela_minha_conta.php?tab=painel-pedidos-recebidos'
                );
            }
        } catch (Exception $exN) {
            error_log("Erro ao criar notificacao: " . $exN->getMessage());
        }

        // Insert Items
        $stmtItem = $pdo->prepare("INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");
        $stmtStock = $pdo->prepare("UPDATE produtos SET estoque = estoque - ? WHERE id = ? AND estoque >= ?");

        foreach ($orderData['items'] as $item) {
            $stmtItem->execute([$pedidoId, $item['product_id'], $item['quantity'], $item['price']]);

            // Dec estoques
            $stmtStock->execute([$item['quantity'], $item['product_id'], $item['quantity']]);
            if ($stmtStock->rowCount() == 0) {
                throw new Exception("Estoque insuficiente para: " . $item['title']);
            }
        }
    }

    // Register Coupon Usage (Once per global usage? Or once per order? existing service registers (cupom_id, user_id, order_id))
    // If table `cupom_uso` has UNIQUE(cupom_id, user_id), we might fail if we register multiple times.
    // Let's register only for the first order or handle logic.
    // If schema allows multiple uses, okay. If not, only register for first.
    // Assuming we want to link coupon to all orders.
    if ($cupom_id && !empty($createdOrderIds)) {
        // Register for the first order to 'mark' it as used by this user in this transaction block
        // If we want to track it per order, we loop.
        // Let's loop but catch dup errors if unique constraint exists.
        foreach ($createdOrderIds as $oid) {
            try {
                $cupomService->registrarUso($cupom_id, $usuario_id, $oid);
            } catch (Exception $ex) {
                // Ignore duplicate entry errors if likely
            }
        }
    }

    // Clear Cart
    $stmtCart = $pdo->prepare("SELECT id FROM carrinho WHERE usuario_id = ?");
    $stmtCart->execute([$usuario_id]);
    $cCart = $stmtCart->fetch();
    if ($cCart) {
        $pdo->prepare("DELETE FROM carrinho_itens WHERE carrinho_id = ?")->execute([$cCart['id']]);
    }

    unset($_SESSION['checkout_cupom']);

    $pdo->commit();
    echo json_encode(['sucesso' => true, 'pedido_id' => $createdOrderIds[0], 'pedidos_ids' => $createdOrderIds, 'mensagem' => 'Pedido(s) realizado(s) com sucesso!']);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro: ' . $e->getMessage()]);
}
