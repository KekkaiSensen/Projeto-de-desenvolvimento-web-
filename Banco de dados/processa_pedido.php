<?php
session_start();
ob_start(); // Previne saída de erros/whitespace antes do JSON
ini_set('display_errors', 0);
define('API_MODE', true);
header('Content-Type: application/json');

try {
    // Requires inside try/catch to handle file not found errors
    require __DIR__ . '/conexao.php';
    require __DIR__ . '/../src/Services/OrderEventService.php';
    require __DIR__ . '/../src/Services/OrderService.php';
    require __DIR__ . '/../src/Services/CupomService.php';
    require __DIR__ . '/../src/Services/NotificationService.php';

    // Helper function for sending JSON response and exiting
    function sendResponse($data)
    {
        ob_clean();
        echo json_encode($data);
        exit();
    }

    if (!isset($_SESSION['usuario_id'])) {
        sendResponse(['sucesso' => false, 'mensagem' => 'Usuário não logado.']);
    }
    $usuario_id = $_SESSION['usuario_id'];

    $dados = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Erro ao decodificar JSON: " . json_last_error_msg());
    }

    $cart = $dados['cart'] ?? [];
    $endereco_id = $dados['endereco_id'] ?? null;

    if (empty($cart)) {
        sendResponse(['sucesso' => false, 'mensagem' => 'O carrinho está vazio.']);
    }

    $pdo->beginTransaction();

    $eventService = new Services\OrderEventService($pdo);
    $orderService = new Services\OrderService($pdo, $eventService);
    $notificationService = new Services\NotificationService($pdo);

    // 1. Fetch product details
    $ordersBySupplier = [];
    $productIds = array_column($cart, 'id');

    if (empty($productIds)) {
        throw new Exception("Carrinho inválido (sem IDs de produtos).");
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
        $supplierId = $productDb['usuario_id'];

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

    // 2. Process Cupom
    $cupom_id = null;
    $global_discount = 0.0;

    if (isset($_SESSION['checkout_cupom']['codigo'])) {
        $codigoCupom = $_SESSION['checkout_cupom']['codigo'];
        $globalTotal = 0;
        foreach ($ordersBySupplier as $ord) {
            $globalTotal += $ord['total_itens'];
        }

        try {
            $cupomService = new Services\CupomService($pdo);
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

        $myDiscount = 0.0;
        if ($global_discount > 0 && $totalGlobalNoDesconto > 0) {
            $ratio = $subTotal / $totalGlobalNoDesconto;
            $myDiscount = round($global_discount * $ratio, 2);
        }

        $finalTotal = max(0, $subTotal - $myDiscount);

        $stmtInsert = $pdo->prepare("
            INSERT INTO pedidos 
            (usuario_id, endereco_id, supplier_id, valor_total, status, cupom_id, valor_desconto, data_pedido) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");

        $initialStatus = Services\OrderService::STATUS_CREATED;

        $stmtInsert->execute([
            $usuario_id,
            $endereco_id,
            $sId,
            $finalTotal,
            $initialStatus,
            $cupom_id,
            $myDiscount
        ]);

        $pedidoId = $pdo->lastInsertId();
        $createdOrderIds[] = $pedidoId;

        $eventService->logEvent($pedidoId, null, $initialStatus, 'client', $usuario_id, 'Pedido criado');

        // NOTIFY SUPPLIER
        try {
            if ($sId != $usuario_id) {
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

        $stmtItem = $pdo->prepare("INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");
        $stmtStock = $pdo->prepare("UPDATE produtos SET estoque = estoque - ? WHERE id = ? AND estoque >= ?");

        foreach ($orderData['items'] as $item) {
            $stmtItem->execute([$pedidoId, $item['product_id'], $item['quantity'], $item['price']]);

            $stmtStock->execute([$item['quantity'], $item['product_id'], $item['quantity']]);
            if ($stmtStock->rowCount() == 0) {
                throw new Exception("Estoque insuficiente para: " . $item['title']);
            }
        }
    }

    // Register Coupon Usage
    if ($cupom_id && !empty($createdOrderIds)) {
        foreach ($createdOrderIds as $oid) {
            try {
                $cupomService->registrarUso($cupom_id, $usuario_id, $oid);
            } catch (Exception $ex) {
                // Ignore duplicate
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

    sendResponse([
        'sucesso' => true,
        'pedido_id' => $createdOrderIds[0],
        'pedidos_ids' => $createdOrderIds,
        'mensagem' => 'Pedido(s) realizado(s) com sucesso!'
    ]);
} catch (Throwable $e) { // Catch Exception and Error (PHP 7+)
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // Log error securely
    $logMessage = date('Y-m-d H:i:s') . " - Erro: " . $e->getMessage() . "\nStack: " . $e->getTraceAsString() . "\n\n";
    file_put_contents('log_erros_pedido.txt', $logMessage, FILE_APPEND);

    // Send JSON error response
    ob_clean(); // MUST clear buffer before sending JSON error
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno: ' . $e->getMessage()]);
    exit();
}
