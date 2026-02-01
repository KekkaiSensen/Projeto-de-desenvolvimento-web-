<?php
session_start();
require '../Banco de dados/conexao.php'; // Inclui a conexão

// 1. Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: tela_login.html'); // Redireciona para o login se não estiver logado
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// 1.1 Verifica se o usuário é um fornecedor (definido no login)
$is_fornecedor = (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] == 'fornecedor');


// 2. Busca os dados pessoais do usuário
try {
    $stmt_user = $pdo->prepare("SELECT nome, email, cpf, telefone FROM usuarios WHERE id = ?");
    $stmt_user->execute([$usuario_id]);
    $usuario = $stmt_user->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar dados do usuário: " . $e->getMessage());
}

// 3. Busca os endereços do usuário
try {
    $stmt_enderecos = $pdo->prepare("SELECT * FROM enderecos WHERE usuario_id = ? ORDER BY id DESC");
    $stmt_enderecos->execute([$usuario_id]);
    $enderecos = $stmt_enderecos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $enderecos = [];
    error_log("Erro ao buscar endereços: " . $e->getMessage());
}

// ==========================================================
// --- HISTÓRICO DE COMPRAS ---
// ==========================================================
try {
    // 4. Busca o histórico de pedidos e seus itens
    $sql_pedidos = "
        SELECT 
            p.id as pedido_id,
            p.data_pedido,
            p.status as pedido_status,
            pi.quantidade,
            prod.nome as produto_nome,
            prod.imagem_url as produto_imagem
        FROM pedidos p
        JOIN pedido_itens pi ON p.id = pi.pedido_id
        JOIN produtos prod ON pi.produto_id = prod.id
        WHERE p.usuario_id = ?
        ORDER BY p.data_pedido DESC, p.id DESC, prod.nome ASC
    ";
    $stmt_pedidos = $pdo->prepare($sql_pedidos);
    $stmt_pedidos->execute([$usuario_id]);
    $itens_de_pedidos = $stmt_pedidos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $itens_de_pedidos = [];
    error_log("Erro ao buscar histórico de pedidos: " . $e->getMessage());
}

// ==========================================================
// --- RASCUNHOS (Se for fornecedor) ---
// ==========================================================
$rascunhos = [];
if ($is_fornecedor) {
    try {
        $stmt_rascunhos = $pdo->prepare("SELECT * FROM produtos WHERE usuario_id = ? AND status != 'inativo' ORDER BY id DESC");
        $stmt_rascunhos->execute([$usuario_id]);
        $rascunhos = $stmt_rascunhos->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erro ao buscar produtos: " . $e->getMessage());
    }

    // --- BUSCA CUPONS DO FORNECEDOR ---
    $meus_cupons = [];
    try {
        // Query modificada para contar os usos e trazer apenas os que ainda não atingiram o limite E estão ativos
        $sql_cupons = "
            SELECT c.*, (SELECT COUNT(id) FROM cupom_uso WHERE cupom_id = c.id) as qtd_usos 
            FROM cupons c 
            WHERE c.usuario_id = ? AND c.ativo = 1
            ORDER BY c.id DESC
        ";
        $stmt_cupons = $pdo->prepare($sql_cupons);
        $stmt_cupons->execute([$usuario_id]);
        $todos_cupons = $stmt_cupons->fetchAll(PDO::FETCH_ASSOC);

        // Filtra os cupons esgotados
        foreach ($todos_cupons as $cupom) {
            if ($cupom['limite_uso'] > 0 && $cupom['qtd_usos'] >= $cupom['limite_uso']) {
                continue; // Pula este cupom pois atingiu o limite
            }
            $meus_cupons[] = $cupom;
        }
    } catch (PDOException $e) {
        error_log("Erro ao buscar cupons: " . $e->getMessage());
    }
    // --- BUSCA PEDIDOS RECEBIDOS (VENDAS) ---
    $vendas_recebidas = [];
    try {
        $sql_vendas = "
            SELECT 
                p.id as pedido_id,
                p.data_pedido,
                p.status,
                p.valor_total,
                u.nome as nome_cliente,
                e.cidade,
                e.estado,
                pi.produto_id,
                pi.quantidade,
                prod.nome as produto_nome,
                prod.imagem_url as produto_imagem
            FROM pedidos p
            JOIN usuarios u ON p.usuario_id = u.id
            LEFT JOIN enderecos e ON p.endereco_id = e.id
            JOIN pedido_itens pi ON p.pedido_id
            JOIN pedido_itens pi ON p.id = pi.pedido_id
            JOIN produtos prod ON pi.produto_id = prod.id
            WHERE p.supplier_id = ?
            ORDER BY p.data_pedido DESC
        ";
        // Correction: Double join on pedido_itens above. Fixed below.
        $sql_vendas = "
            SELECT 
                p.id as pedido_id,
                p.data_pedido,
                p.status,
                p.valor_total,
                u.nome as nome_cliente,
                u.email as email_cliente,
                pi.produto_id,
                pi.quantidade,
                prod.nome as produto_nome,
                prod.imagem_url as produto_imagem
            FROM pedidos p
            JOIN usuarios u ON p.usuario_id = u.id
            JOIN pedido_itens pi ON p.id = pi.pedido_id
            JOIN produtos prod ON pi.produto_id = prod.id
            WHERE p.supplier_id = ?
            ORDER BY p.data_pedido DESC, p.id DESC
        ";

        $stmt_vendas = $pdo->prepare($sql_vendas);
        $stmt_vendas->execute([$usuario_id]);
        $rows_vendas = $stmt_vendas->fetchAll(PDO::FETCH_ASSOC);

        // Group by Order ID
        foreach ($rows_vendas as $row) {
            $pid = $row['pedido_id'];
            if (!isset($vendas_recebidas[$pid])) {
                $vendas_recebidas[$pid] = [
                    'id' => $pid,
                    'data' => $row['data_pedido'],
                    'status' => $row['status'],
                    'total' => $row['valor_total'],
                    'cliente' => $row['nome_cliente'],
                    'email' => $row['email_cliente'],
                    'itens' => []
                ];
            }
            $vendas_recebidas[$pid]['itens'][] = $row;
        }
    } catch (PDOException $e) {
        error_log("Erro ao buscar vendas: " . $e->getMessage());
    }
}
// ==========================================================
// --- FIM DA LÓGICA ---
// ==========================================================


// Pega o primeiro nome para o header
$nome_usuario = explode(' ', $usuario['nome'])[0];

// Configura o fuso horário e local para formatar datas em português
date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');
?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Minha Conta - Loja Ponto Com</title>

    <link rel="stylesheet" href="assets/estilos/style.css">
    <link rel="stylesheet" href="assets/estilos/notifications.css">

    <style>
        /* Estilos copiados de tela_gerenciar_produtos.html */
        /* Estilos copiados de tela_gerenciar_produtos.html */
        /* REMOVIDO: #lista-produtos usava flex, agora usa a classe .grid do style.css para 4 colunas */


        /* --- INÍCIO DA MODIFICAÇÃO --- */
        /* Estilo para o novo botão de adicionar produto */

        /* Switch CSS */
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #2968C8;
        }

        input:checked+.slider:before {
            transform: translateX(26px);
        }
    </style>
</head>

<body>

    <header class="topbar">
        <nav class="actions">
            <div class="logo-container">
                <a href="index.php" style="display: flex; align-items: center;">
                    <img src="assets/imagens/exemplo-logo.png" alt="" style="width: 40px; height: 40px;">
                </a>
            </div>

            <form action="buscar.php" method="GET" style="position: relative; width: 600px; max-width: 100%;">
                <input type="search" id="pesquisa" name="q" placeholder="Digite sua pesquisa..." style="font-size: 16px; width: 100%; height: 40px; padding-left: 15px; padding-right: 45px; border-radius: 6px; border: none; box-sizing: border-box;">
                <button type="submit" style="position: absolute; right: 0; top: 0; height: 40px; width: 45px; border: none; background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                    <img src="assets/imagens/lupa.png" alt="lupa" style="width: 28px; height: 28px; opacity: 0.6;">
                </button>
            </form>

            <div style="display: flex; gap: 30px; align-items: center;">
                <a href="tela_minha_conta.php">Olá, <?php echo htmlspecialchars($nome_usuario); ?></a>
                <a href="../Banco de dados/logout.php">Sair</a>
                <a href="tela_carrinho.php" style="display: flex; align-items: center; gap: 5px;">
                    Carrinho
                    <img src="assets/imagens/carrinho invertido.png" alt="" style="width: 20px; height: 20px;">
                </a>

                <?php if (isset($usuario_id)): // Já verificado no início do arquivo 
                ?>
                    <!-- Notification System -->
                    <div id="notification-bell" class="notification-container">
                        <!-- Icone SVG desenhado -->
                        <svg class="notification-bell-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <span id="notification-badge" class="notification-badge"></span>
                        <div id="notification-dropdown" class="notification-dropdown">
                            <div class="notification-header">
                                <span>Notificações</span>
                                <span id="mark-all-read" class="mark-all-read">Marcar todas como lidas</span>
                            </div>
                            <div id="notification-list"></div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main class="container">
        <h1>Minha Conta</h1>

        <?php if ($is_fornecedor): ?>
            <div class="editor-mode-container" style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                <label class="switch">
                    <input type="checkbox" id="modeEditorSwitch" <?php echo (isset($_SESSION['modo_editor']) && $_SESSION['modo_editor']) ? 'checked' : ''; ?>>
                    <span class="slider round"></span>
                </label>
                <span style="font-size: 1.1rem; font-weight: bold;">Modo editor</span>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const switchBtn = document.getElementById('modeEditorSwitch');
                    if (switchBtn) {
                        switchBtn.addEventListener('change', function() {
                            const isActive = this.checked;
                            fetch('api/toggle_editor.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        active: isActive
                                    })
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        console.log('Modo editor atualizado: ' + isActive);
                                    } else {
                                        console.error('Erro ao atualizar modo editor');
                                    }
                                })
                                .catch(err => console.error('Erro:', err));
                        });
                    }
                });
            </script>
        <?php endif; ?>


        <div class="tabs-container">
            <button class="tab-button active" data-tab="painel-conta">Minha Conta</button>
            <button class="tab-button" data-tab="painel-compras">Compras feitas</button>

            <?php if ($is_fornecedor): ?>
                <button class="tab-button" data-tab="painel-produtos">Meus produtos</button>
                <button class="tab-button" data-tab="painel-pedidos-recebidos">Pedidos Recebidos</button>
                <button class="tab-button" data-tab="painel-cupons">Meus Cupons</button>
                <button class="tab-button" data-tab="painel-relatorio">Relatório de Vendas</button>
            <?php endif; ?>
        </div>

        <div id="painel-conta" class="tab-painel active">
            <section class="conta-secao">
                <h2>Dados Pessoais</h2>
                <table class="dados-pessoais">
                    <tr>
                        <td>Nome:</td>
                        <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                    </tr>
                    <tr>
                        <td>Email:</td>
                        <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                    </tr>
                    <tr>
                        <td>CPF:</td>
                        <td><?php echo htmlspecialchars(substr($usuario['cpf'], 0, 3) . '.***.***-' . substr($usuario['cpf'], -2)); ?></td>
                    </tr>
                    <tr>
                        <td>Telefone:</td>
                        <td><?php echo htmlspecialchars($usuario['telefone']); ?></td>
                    </tr>
                </table>
            </section>

            <section class="conta-secao">
                <h2>Endereços</h2>

                <?php if (empty($enderecos)): ?>
                    <p>Nenhum endereço cadastrado.</p>
                <?php endif; ?>

                <?php foreach ($enderecos as $endereco): ?>
                    <div class="endereco-card">
                        <div class="endereco-card-opcoes">
                            <a href="tela_editar_endereco.php?id=<?php echo $endereco['id']; ?>">Editar</a>
                            <a href="../Banco de dados/processa_excluir_endereco.php?id=<?php echo $endereco['id']; ?>"
                                onclick="return confirm('Tem certeza que deseja excluir este endereço?');">Excluir</a>
                        </div>

                        <p class="rua-principal">
                            <?php
                            echo htmlspecialchars($endereco['rua']) . ', ' . htmlspecialchars($endereco['numero']);
                            if (!empty($endereco['complemento'])) {
                                echo ' - ' . htmlspecialchars($endereco['complemento']);
                            }
                            ?>
                        </p>
                        <p class="cep-cidade">
                            CEP <?php echo htmlspecialchars($endereco['cep']); ?> -
                            <?php echo htmlspecialchars($endereco['cidade']); ?> -
                            <?php echo htmlspecialchars($endereco['estado']); ?>
                        </p>
                        <p><?php echo htmlspecialchars($usuario['nome']); ?></p>
                    </div>
                <?php endforeach; ?>

                <a href="tela_novo_endereco.php" class="btn-adicionar-endereco">
                    <span>+</span> Adicionar novo endereço
                </a>
            </section>
        </div>

        <div id="painel-compras" class="tab-painel">
            <?php
            // 4. Busca o histórico de pesdidos (Agrupado por Pedido)
            try {
                $sql_pedidos = "
                    SELECT 
                        p.id as pedido_id,
                        p.data_pedido,
                        p.status,
                        p.valor_total,
                        p.supplier_id,
                        (SELECT nome FROM usuarios WHERE id = p.supplier_id) as nome_loja,
                        pi.produto_id,
                        pi.quantidade,
                        pi.preco_unitario,
                        prod.nome as produto_nome,
                        prod.imagem_url as produto_imagem
                    FROM pedidos p
                    JOIN pedido_itens pi ON p.id = pi.pedido_id
                    JOIN produtos prod ON pi.produto_id = prod.id
                    WHERE p.usuario_id = ?
                    ORDER BY p.data_pedido DESC, p.id DESC
                ";
                $stmt_pedidos = $pdo->prepare($sql_pedidos);
                $stmt_pedidos->execute([$usuario_id]);
                $rows = $stmt_pedidos->fetchAll(PDO::FETCH_ASSOC);

                // Group by Order ID
                $pedidos_agrupados = [];
                foreach ($rows as $row) {
                    $pid = $row['pedido_id'];
                    if (!isset($pedidos_agrupados[$pid])) {
                        $pedidos_agrupados[$pid] = [
                            'id' => $pid,
                            'data' => $row['data_pedido'],
                            'status' => $row['status'],
                            'total' => $row['valor_total'],
                            'loja' => $row['nome_loja'] ?? 'Loja Ponto Com',
                            'itens' => []
                        ];
                    }
                    $pedidos_agrupados[$pid]['itens'][] = $row;
                }
            } catch (PDOException $e) {
                $pedidos_agrupados = [];
                error_log("Erro ao buscar histórico: " . $e->getMessage());
            }
            ?>

            <?php if (empty($pedidos_agrupados)): ?>
                <section class="conta-secao">
                    <h2>Minhas Compras</h2>
                    <p>Você ainda não fez nenhuma compra.</p>
                </section>
            <?php else: ?>
                <section class="conta-secao">
                    <h2>Meus Pedidos</h2>
                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <?php foreach ($pedidos_agrupados as $pedido):
                            $status = strtoupper($pedido['status']); // Ensure uppercase for logic
                            // Visual Status Map
                            $statusLabel = [
                                'CREATED' => 'Criado',
                                'PAID' => 'Pago',
                                'PROCESSING' => 'Em Processamento',
                                'SHIPPED' => 'Enviado',
                                'DELIVERED' => 'Entregue',
                                'CANCELED' => 'Cancelado',
                                'PROCESSANDO' => 'Em Análise', // Legacy
                                'ENTREGUE' => 'Entregue' // Legacy fallback
                            ][$status] ?? 'Em Análise'; // Default fallback

                            // Normalize for checks
                            if ($status === 'ENTREGUE') $status = 'DELIVERED';
                            if ($status === 'PROCESSANDO') $status = 'PROCESSING'; // Map legacy processando to processing visual

                            $statusColor = match ($status) {
                                'DELIVERED' => '#4caf50', // Green
                                'CANCELED' => '#f44336', // Red
                                'SHIPPED' => '#2196f3', // Blue
                                default => '#ff8c00' // Orange
                            };
                        ?>
                            <div class="pedido-card" style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; background: #fff;">
                                <div class="pedido-header" style="display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px;">
                                    <div>
                                        <strong>Pedido #<?php echo $pedido['id']; ?></strong>
                                        <span style="color: #666; font-size: 0.9em;"> • <?php echo date('d/m/Y', strtotime($pedido['data'])); ?></span>
                                        <div style="font-size: 0.9em; color: #555;">Vendido por: <strong><?php echo htmlspecialchars($pedido['loja']); ?></strong></div>
                                    </div>
                                    <div style="text-align: right;">
                                        <div style="font-weight: bold; color: <?php echo $statusColor; ?>;"><?php echo $statusLabel; ?></div>
                                        <div style="font-size: 1.1em; font-weight: bold;">R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></div>
                                    </div>
                                </div>

                                <div class="pedido-itens">
                                    <?php foreach ($pedido['itens'] as $item): ?>
                                        <div style="display: flex; gap: 10px; margin-bottom: 10px; align-items: center;">
                                            <img src="<?php echo htmlspecialchars($item['produto_imagem'] ?? 'assets/imagens/placeholder.png'); ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                            <div>
                                                <div><?php echo htmlspecialchars($item['produto_nome']); ?></div>
                                                <div style="font-size: 0.85em; color: #777;">
                                                    <?php echo $item['quantidade']; ?>x R$ <?php echo number_format($item['preco_unitario'], 2, ',', '.'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Timeline Visual (Simple CSS) -->
                                <?php if ($status !== 'CANCELED'): ?>
                                    <div class="timeline-status" style="display: flex; justify-content: space-between; margin: 15px 0; position: relative;">
                                        <!-- Line background -->
                                        <div style="position: absolute; top: 12px; left: 0; right: 0; height: 4px; background: #eee; z-index: 0;"></div>
                                        <!-- Active Line (approximate based on status) -->
                                        <?php
                                        // Calculate progress Width
                                        $progress = 0;
                                        if ($status == 'PAID') $progress = 25;
                                        if ($status == 'PROCESSING') $progress = 50;
                                        if ($status == 'SHIPPED') $progress = 75;
                                        if ($status == 'DELIVERED') $progress = 100;
                                        ?>
                                        <div style="position: absolute; top: 12px; left: 0; width: <?php echo $progress; ?>%; height: 4px; background: #4caf50; z-index: 0; transition: width 0.3s;"></div>

                                        <?php
                                        $steps = [
                                            'CREATED' => 'Criado',
                                            'PAID' => 'Pago',
                                            'PROCESSING' => 'Prep.',
                                            'SHIPPED' => 'Enviado',
                                            'DELIVERED' => 'Entregue'
                                        ];
                                        $passed = true;
                                        foreach ($steps as $key => $label):
                                            $isActive = ($key == $status);
                                            // Simple logic: if we found the status, subsequent are passed=false (except if it is the current one, it is passed).
                                            // Wait, usually timeline lights up steps UP TO current.
                                            // Let's rely on array order.
                                            $color = ($passed) ? '#4caf50' : '#ccc';
                                            if ($key == $status) $passed = false; // Next ones are grey
                                        ?>
                                            <div style="z-index: 1; text-align: center; background: #fff; padding: 0 5px;">
                                                <div style="width: 12px; height: 12px; border-radius: 50%; background: <?php echo $color; ?>; margin: 0 auto 5px;"></div>
                                                <span style="font-size: 0.75em; color: <?php echo $color; ?>;"><?php echo $label; ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="pedido-actions" style="display: flex; gap: 10px; margin-top: 15px;">
                                    <!-- Ver Rastreio (Modal) -->
                                    <button class="btn-action" onclick="openTrackingModal(<?php echo $pedido['id']; ?>)" style="padding: 8px 15px; border: 1px solid #ccc; background: white; border-radius: 4px; cursor: pointer;">
                                        Rastrear / Detalhes
                                    </button>

                                    <!-- Actions based on status -->
                                    <?php if ($status == 'CREATED' || $status == 'PAID' || $status == 'PROCESSING'): ?>
                                        <button class="btn-action" onclick="confirmAction('cancel', <?php echo $pedido['id']; ?>)" style="padding: 8px 15px; background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; border-radius: 4px; cursor: pointer;">
                                            Cancelar Pedido
                                        </button>
                                    <?php endif; ?>

                                    <?php if ($status == 'SHIPPED'): ?>
                                        <button class="btn-action" onclick="confirmAction('confirm-delivery', <?php echo $pedido['id']; ?>)" style="padding: 8px 15px; background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; border-radius: 4px; cursor: pointer;">
                                            Confirmar Recebimento
                                        </button>
                                    <?php endif; ?>

                                    <?php if ($status == 'DELIVERED'): ?>
                                        <button class="btn-action" onclick="openReportModal(<?php echo $pedido['id']; ?>)" style="padding: 8px 15px; border: 1px solid #ccc; background: white; border-radius: 4px; cursor: pointer;">
                                            Reportar Problema
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Modal de Rastreio -->
            <div id="trackingModal" style="display: none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
                <div style="background: white; padding: 20px; border-radius: 8px; width: 90%; max-width: 500px; max-height: 80vh; overflow-y: auto;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h3>Histórico do Pedido</h3>
                        <button onclick="closeModal('trackingModal')" style="background: none; border: none; font-size: 1.5em; cursor: pointer;">&times;</button>
                    </div>
                    <div id="trackingContent">Carregando...</div>
                </div>
            </div>

            <!-- Modal Reportar -->
            <div id="reportModal" style="display: none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
                <div style="background: white; padding: 20px; border-radius: 8px; width: 90%; max-width: 500px;">
                    <h3>Reportar Problema</h3>
                    <textarea id="reportReason" placeholder="Descreva o problema..." style="width: 100%; height: 100px; margin: 10px 0; padding: 10px; border: 1px solid #ccc; border-radius: 4px;"></textarea>
                    <div style="text-align: right; gap: 10px;">
                        <button onclick="closeModal('reportModal')" style="padding: 8px 15px; margin-right: 10px;">Cancelar</button>
                        <button onclick="submitReport()" style="padding: 8px 15px; background: #ff8c00; color: white; border: none; border-radius: 4px;">Enviar</button>
                    </div>
                </div>
            </div>

            <script>
                // Front Functions
                let currentOrderId = null;

                function confirmAction(action, id) {
                    let msg = '';
                    if (action === 'cancel') msg = 'Tem certeza que deseja cancelar este pedido?';
                    if (action === 'confirm-delivery') msg = 'Confirma que recebeu o produto em bom estado?';

                    if (confirm(msg)) {
                        callApi(action, id);
                    }
                }

                function openTrackingModal(id) {
                    document.getElementById('trackingModal').style.display = 'flex';
                    const content = document.getElementById('trackingContent');
                    content.innerHTML = 'Carregando...';

                    fetch(`api/orders.php?id=${id}&action=events`)
                        .then(r => r.json())
                        .then(events => {
                            if (!events || events.length === 0) {
                                content.innerHTML = '<p>Nenhum evento registrado.</p>';
                                return;
                            }
                            let html = '<ul style="list-style: none; padding: 0;">';
                            events.forEach(e => {
                                const date = new Date(e.created_at).toLocaleString('pt-BR');
                                html += `
                                    <li style="border-left: 2px solid #ddd; padding-left: 15px; margin-bottom: 20px; position: relative;">
                                        <div style="position: absolute; left: -6px; top: 0; width: 10px; height: 10px; border-radius: 50%; background: #ff8c00;"></div>
                                        <div style="font-weight: bold;">${e.new_status}</div>
                                        <div style="font-size: 0.9em; color: #666;">${date}</div>
                                        <div style="margin-top: 5px;">${e.description || ''}</div>
                                    </li>
                                `;
                            });
                            html += '</ul>';
                            content.innerHTML = html;
                        })
                        .catch(err => {
                            console.error(err);
                            content.innerHTML = '<p>Erro ao carregar eventos.</p>';
                        });
                }

                function openReportModal(id) {
                    currentOrderId = id;
                    document.getElementById('reportModal').style.display = 'flex';
                }

                function submitReport() {
                    const reason = document.getElementById('reportReason').value;
                    if (!reason) {
                        alert('Por favor, descreva o problema.');
                        return;
                    }
                    callApi('report-issue', currentOrderId, {
                        description: reason
                    });
                    closeModal('reportModal');
                }

                function closeModal(id) {
                    document.getElementById(id).style.display = 'none';
                }

                function callApi(action, id, body = {}) {
                    fetch(`api/orders.php?id=${id}&action=${action}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(body)
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                alert('Ação realizada com sucesso!');
                                location.reload();
                            } else {
                                alert('Erro: ' + (data.error || 'Desconhecido'));
                            }
                        })
                        .catch(err => alert('Erro de conexão.'));
                }
            </script>
        </div>

        <?php if ($is_fornecedor): ?>
            <div id="painel-produtos" class="tab-painel">

                <section class="conta-secao">
                    <h2>Meus Produtos</h2>

                    <div class="controls" style="margin-bottom: 20px;">
                        <label for="sort-produtos">Ordenar por</label>
                        <select id="sort-produtos" aria-label="Ordenar por">
                            <option>Mais relevantes</option>
                            <option>Menor preço</option>
                            <option>Maior preço</option>
                        </select>
                    </div>

                    <?php if (empty($rascunhos)): ?>
                        <p id="sem-produtos-aviso">Você ainda não adicionou nenhum rascunho.</p>
                    <?php else: ?>
                        <section class="grid" id="lista-produtos" style="padding: 0; border: none;">
                            <?php foreach ($rascunhos as $p):
                                $preco = $p['preco'] ?? 0;
                                $desconto = $p['desconto'] ?? 0;
                                $precoFinal = $preco * (1 - $desconto / 100);
                                $img = !empty($p['imagem_url']) ? $p['imagem_url'] : 'assets/imagens/placeholder.png';
                            ?>
                                <article class="card" data-price="<?php echo $precoFinal; ?>">
                                    <div class="thumb" style="background-image:url('<?php echo htmlspecialchars($img); ?>')"></div>
                                    <div class="title"><?php echo htmlspecialchars($p['nome'] ?? 'Sem título'); ?></div>
                                    <div>
                                        <?php if ($desconto > 0): ?>
                                            <span class="old">R$ <?php echo number_format($preco, 2, ',', '.'); ?></span>
                                        <?php endif; ?>
                                        <span class="price">R$ <?php echo number_format($precoFinal, 2, ',', '.'); ?></span>
                                        <?php if ($desconto > 0): ?>
                                            <span class="badge"><?php echo $desconto; ?>% OFF</span>
                                        <?php endif; ?>
                                    </div>
                                    <a href="tela_produto_do_fornecedor.php?id=<?php echo $p['id']; ?>" class="editar-btn" style="text-decoration: none; text-align: center; display: inline-block; padding: 5px;">✏️ Editar</a>
                                    <a href="../Banco de dados/excluir_produto.php?id=<?php echo $p['id']; ?>" class="excluir-btn" style="text-decoration: none; text-align: center; display: inline-block; padding: 5px;" onclick="return confirm('Excluir este rascunho?');">🗑 Excluir</a>
                                </article>
                            <?php endforeach; ?>
                        </section>
                    <?php endif; ?>

                    <a href="tela_produto_do_fornecedor.php"
                        class="btn-adicionar-endereco">
                        <span>+</span> Adicionar novo produto
                    </a>
                </section>

            </div>
        <?php endif; ?>

        <?php if ($is_fornecedor): ?>
            <div id="painel-cupons" class="tab-painel">
                <section class="conta-secao">
                    <h2>Meus Cupons de Desconto</h2>

                    <?php if (empty($meus_cupons)): ?>
                        <p>Você ainda não criou nenhum cupom.</p>
                    <?php else: ?>
                        <div style="overflow-x: auto;">
                            <table class="dados-pessoais" style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="text-align: left; background: #f5f5f5;">
                                        <th style="padding: 10px;">Código</th>
                                        <th style="padding: 10px;">Desconto</th>
                                        <th style="padding: 10px;">Validade</th>
                                        <th style="padding: 10px;">Usos</th>
                                        <th style="padding: 10px;">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($meus_cupons as $cupom):
                                        $valor = $cupom['tipo_desconto'] == 'porcentagem' ? number_format($cupom['valor_desconto'], 0) . '%' : 'R$ ' . number_format($cupom['valor_desconto'], 2, ',', '.');
                                        $data_fim = $cupom['data_fim'] ? date('d/m/Y', strtotime($cupom['data_fim'])) : 'Indeterminado';
                                        $ativo = $cupom['ativo'] ? 'Ativo' : 'Inativo';
                                    ?>
                                        <tr style="border-bottom: 1px solid #eee;">
                                            <td style="padding: 10px;"><strong><?php echo htmlspecialchars($cupom['codigo']); ?></strong></td>
                                            <td style="padding: 10px;"><?php echo $valor; ?></td>
                                            <td style="padding: 10px;"><?php echo $data_fim; ?></td>
                                            <td style="padding: 10px;"><?php echo $cupom['limite_uso'] ? "{$cupom['qtd_usos']} / {$cupom['limite_uso']}" : "{$cupom['qtd_usos']} (Ilimitado)"; ?></td>
                                            <td style="padding: 10px;">
                                                <a href="../Banco de dados/excluir_cupom.php?id=<?php echo $cupom['id']; ?>" onclick="return confirm('Excluir cupom?');" style="color: red;">Excluir</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                    <a href="tela_cupom_form.php" class="btn-adicionar-endereco" style="margin-top: 20px;">
                        <span>+</span> Criar Novo Cupom
                    </a>
                </section>
            </div>
        <?php endif; ?>

        <?php if ($is_fornecedor): ?>
            <div id="painel-pedidos-recebidos" class="tab-painel">
                <section class="conta-secao">
                    <h2>Gestão de Pedidos</h2>

                    <?php if (empty($vendas_recebidas)): ?>
                        <p>Você não possui pedidos recebidos.</p>
                    <?php else: ?>
                        <div style="display: flex; flex-direction: column; gap: 20px;">
                            <?php foreach ($vendas_recebidas as $pedido):
                                $status = strtoupper($pedido['status']);
                                // Status Map
                                $statusLabel = [
                                    'CREATED' => 'Aguardando Pagamento/Confirmação',
                                    'PROCESSANDO' => 'Aguardando Processamento', // Legacy
                                    'PAID' => 'Pronto para Processar',
                                    'PROCESSING' => 'Em Separação',
                                    'SHIPPED' => 'Enviado',
                                    'DELIVERED' => 'Concluído',
                                    'CANCELED' => 'Cancelado',
                                    'ENTREGUE' => 'Concluído' // Legacy
                                ][$status] ?? $status;

                                // Normalize logic vars
                                if ($status === 'ENTREGUE') $status = 'DELIVERED';
                                if ($status === 'PROCESSANDO') $status = 'PROCESSING'; // Legacy processando -> treating as PROCESSING for actions? Or PAID?
                                // User complains "Process processing" fails from CREATED.
                                // If legacy is PROCESSANDO, it usually means paid/processing. 
                                // Let's treat PROCESSANDO as PAID (ready to ship/process) or PROCESSING?
                                // Legacy 'processando' was the default after purchase. So it equals CREATED/PAID.
                                // Let's map PROCESSANDO to PAID for logic so they can "Start Processing" (even though name is confusing).
                                if ($pedido['status'] === 'processando') $status = 'PAID';
                            ?>
                                <div class="pedido-card" style="border: 1px solid #ccc; border-radius: 8px; padding: 15px; background: #fff;">
                                    <div class="pedido-header" style="display: flex; justify-content: space-between; margin-bottom: 10px; border-bottom: 1px dashed #eee; padding-bottom: 10px;">
                                        <div>
                                            <strong>Pedido #<?php echo $pedido['id']; ?></strong>
                                            <div style="color: #555;">Cliente: <?php echo htmlspecialchars($pedido['cliente']); ?> (<?php echo htmlspecialchars($pedido['email']); ?>)</div>
                                            <div style="font-size: 0.9em; color: #888;"><?php echo date('d/m/Y H:i', strtotime($pedido['data'])); ?></div>
                                        </div>
                                        <div style="text-align: right;">
                                            <span class="badge" style="background: #333; color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 0.85em;"><?php echo $statusLabel; ?></span>
                                            <div style="margin-top: 5px; font-weight: bold;">Total: R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></div>
                                        </div>
                                    </div>

                                    <div class="pedido-itens" style="margin-bottom: 15px;">
                                        <?php foreach ($pedido['itens'] as $item): ?>
                                            <div style="display: flex; justify-content: space-between; font-size: 0.95em; border-bottom: 1px solid #f9f9f9; padding: 5px 0;">
                                                <span><?php echo $item['quantidade']; ?>x <?php echo htmlspecialchars($item['produto_nome']); ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="pedido-acoes" style="display: flex; gap: 10px; justify-content: flex-end;">
                                        <?php if ($status == 'PAID' || $status == 'PROCESSANDO' || $status == 'CREATED'): // Allowing created/processando to start processing 
                                        ?>
                                            <button class="btn-action" onclick="confirmAction('start-processing', <?php echo $pedido['id']; ?>)" style="background: #2196f3; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer;">
                                                Iniciar Processamento
                                            </button>
                                            <button class="btn-action" onclick="confirmAction('cancel', <?php echo $pedido['id']; ?>)" style="background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; padding: 8px 15px; border-radius: 4px; cursor: pointer;">
                                                Cancelar
                                            </button>
                                        <?php endif; ?>

                                        <?php if ($status == 'PROCESSING'): ?>
                                            <button class="btn-action" onclick="confirmAction('ship', <?php echo $pedido['id']; ?>)" style="background: #ff9800; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer;">
                                                Marcar como Enviado
                                            </button>
                                            <button class="btn-action" onclick="confirmAction('cancel', <?php echo $pedido['id']; ?>)" style="background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; padding: 8px 15px; border-radius: 4px; cursor: pointer;">
                                                Cancelar
                                            </button>
                                        <?php endif; ?>

                                        <button class="btn-action" onclick="openTrackingModal(<?php echo $pedido['id']; ?>)" style="background: #eee; color: #333; border: 1px solid #ccc; padding: 8px 15px; border-radius: 4px; cursor: pointer;">
                                            Ver Histórico
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
            </div>

            <div id="painel-relatorio" class="tab-painel">
                <section class="conta-secao">
                    <h2>Desempenho de Vendas</h2>
                    <div class="controls" style="margin-bottom: 20px;">
                        <label for="salesRange">Período:</label>
                        <select id="salesRange" style="padding: 5px; border-radius: 4px; border: 1px solid #ccc;">
                            <option value="7days">Últimos 7 dias</option>
                            <option value="30days">Últimos 30 dias</option>
                            <option value="all">Desde o início</option>
                        </select>
                    </div>
                    <div style="max-width: 800px; margin: 0 auto;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </section>
            </div>
        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Lógica original das abas
            const tabs = document.querySelectorAll('.tab-button');
            const panels = document.querySelectorAll('.tab-painel');

            function switchTab(targetId) {
                // Remove active class from all
                tabs.forEach(t => t.classList.remove('active'));
                panels.forEach(p => p.classList.remove('active'));

                // Find and activate requested tab
                const activeTabBtn = document.querySelector(`.tab-button[data-tab="${targetId}"]`);
                const activePanel = document.getElementById(targetId);

                if (activeTabBtn && activePanel) {
                    activeTabBtn.classList.add('active');
                    activePanel.classList.add('active');

                    // Se for a aba de relatório, carrega o gráfico
                    if (targetId === 'painel-relatorio' && window.mySalesChart === undefined) {
                        if (typeof loadSalesChart === 'function') {
                            loadSalesChart();
                        }
                    }
                }
            }

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const targetId = tab.getAttribute('data-tab');
                    switchTab(targetId);
                });
            });

            // Verificar se há uma aba específica na URL para abrir
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');
            if (tabParam) {
                switchTab(tabParam);
            }

            // Lógica do Gráfico de Vendas
            const ctx = document.getElementById('salesChart');
            if (ctx) { // Só executa se o elemento existir (se for fornecedor)
                let salesChart;

                window.loadSalesChart = function() {
                    const range = document.getElementById('salesRange').value;
                    fetchDataAndRender(range);
                };

                document.getElementById('salesRange').addEventListener('change', function() {
                    const range = this.value;
                    fetchDataAndRender(range);
                });

                function fetchDataAndRender(range) {
                    fetch(`relatorio_vendas.php?range=${range}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.error) {
                                console.error(data.error);
                                return;
                            }
                            renderChart(data);
                        })
                        .catch(error => console.error('Erro ao buscar dados:', error));
                }

                function renderChart(data) {
                    const labels = data.map(item => {
                        const date = new Date(item.date);
                        // Ajuste para o fuso horário local se necessário, ou apenas formatação simples
                        return date.toLocaleDateString('pt-BR', {
                            day: '2-digit',
                            month: '2-digit'
                        });
                    });
                    const values = data.map(item => item.total);

                    if (salesChart) {
                        salesChart.destroy();
                    }

                    salesChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Vendas (R$)',
                                data: values,
                                borderColor: '#ff8c00', // Cor laranja do tema
                                backgroundColor: 'rgba(255, 140, 0, 0.2)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.3 // Suaviza a linha
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: true
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.dataset.label || '';
                                            if (label) {
                                                label += ': ';
                                            }
                                            if (context.parsed.y !== null) {
                                                label += new Intl.NumberFormat('pt-BR', {
                                                    style: 'currency',
                                                    currency: 'BRL'
                                                }).format(context.parsed.y);
                                            }
                                            return label;
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value, index, values) {
                                            return 'R$ ' + value;
                                        }
                                    }
                                }
                            }
                        }
                    });
                    window.mySalesChart = salesChart; // Marca como carregado
                }
            }
        });
    </script>

    <script src="assets/js/notifications.js"></script>
</body>

</html>