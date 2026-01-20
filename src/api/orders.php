<?php
session_start();
header('Content-Type: application/json');

// Autoload logic or require services
require __DIR__ . '/../../Banco de dados/conexao.php';
require __DIR__ . '/../Services/OrderEventService.php';
require __DIR__ . '/../Services/OrderService.php';
require __DIR__ . '/../Services/NotificationService.php'; // Ensure included

use Services\OrderEventService;
use Services\OrderService;
use Services\NotificationService; // Use

try {
    $eventService = new OrderEventService($pdo);
    $notificationService = new NotificationService($pdo);
    $orderService = new OrderService($pdo, $eventService, $notificationService); // Pass notification service

    // Simple routing based on Query Params for compatibility without rewrite rules
    // Usage: orders.php?id=1&action=events (GET)
    // Usage: orders.php?id=1 (GET)
    // Usage: orders.php?id=1&action=ship (POST)

    $method = $_SERVER['REQUEST_METHOD'];
    $id = $_GET['id'] ?? null;
    $action = $_GET['action'] ?? null;

    if (!$id) {
        throw new Exception("ID do pedido é obrigatório.");
    }

    // Auth check - Basic
    if (!isset($_SESSION['usuario_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Não autorizado']);
        exit;
    }
    $userId = $_SESSION['usuario_id'];
    $userType = $_SESSION['usuario_tipo'] ?? 'client'; // 'client' or 'fornecedor'/supplier
    // Note: session variable might be 'tipo', check checking 'processa_login.php' or similar if needed.
    // Assuming 'usuario_tipo' based on update_order.php (line 8: $_SESSION['usuario_tipo'])

    $actorType = ($userType === 'fornecedor') ? 'supplier' : 'client'; // Map to 'supplier'/'client'

    if ($method === 'GET') {
        if ($action === 'events') {
            // GET /orders/{id}/events
            $events = $eventService->getEvents($id);
            echo json_encode($events);
        } else {
            // GET /orders/{id}
            $order = $orderService->getOrder($id);
            if (!$order) {
                http_response_code(404);
                echo json_encode(['error' => 'Pedido não encontrado']);
                exit;
            }
            // Permission check: Client can only see their orders. Supplier can only see their orders (supplier_id).
            if ($userType !== 'admin') {
                if ($userType === 'fornecedor' && $order['supplier_id'] != $userId) {
                    http_response_code(403);
                    echo json_encode(['error' => 'Acesso negado.']);
                    exit;
                }
                if ($userType !== 'fornecedor' && $order['usuario_id'] != $userId) {
                    http_response_code(403);
                    echo json_encode(['error' => 'Acesso negado.']);
                    exit;
                }
            }
            echo json_encode($order);
        }
    } elseif ($method === 'POST') {
        // Actions
        // Read input JSON if needed, or just params
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $reason = $input['reason'] ?? '';

        // Load order to verify permissions always
        $order = $orderService->getOrder($id);
        if (!$order) {
            http_response_code(404);
            echo json_encode(['error' => 'Pedido não encontrado']);
            exit;
        }

        $success = false;

        switch ($action) {
            case 'start-processing':
                // Supplier only
                if ($order['supplier_id'] != $userId) throw new Exception("Somente o fornecedor deste pedido pode iniciar processamento.");
                $success = $orderService->transitionTo($id, OrderService::STATUS_PROCESSING, 'supplier', $userId);
                break;

            case 'ship':
                // Supplier only
                if ($order['supplier_id'] != $userId) throw new Exception("Somente o fornecedor deste pedido pode enviar o pedido.");
                $success = $orderService->transitionTo($id, OrderService::STATUS_SHIPPED, 'supplier', $userId);
                break;

            case 'confirm-delivery':
                // Client only
                if ($order['usuario_id'] != $userId) throw new Exception("Somente o comprador pode confirmar recebimento.");
                $success = $orderService->transitionTo($id, OrderService::STATUS_DELIVERED, 'client', $userId);
                break;

            case 'cancel':
                // Client or Supplier
                $actorType = 'system';
                if ($order['usuario_id'] == $userId) $actorType = 'client';
                elseif ($order['supplier_id'] == $userId) $actorType = 'supplier';
                else throw new Exception("Permissão negada para cancelar.");

                if (empty($reason)) $reason = "Cancelado pelo usuário";
                $success = $orderService->transitionTo($id, OrderService::STATUS_CANCELED, $actorType, $userId, $reason);
                break;

            case 'report-issue':
                // Implementation for reporting issue
                // Insert into order_issues
                $description = $input['description'] ?? 'Problema reportado';
                $stmt = $pdo->prepare("INSERT INTO order_issues (order_id, user_id, type, description, status) VALUES (?, ?, 'generic', ?, 'open')");
                $stmt->execute([$id, $userId, $description]);

                // Also log event
                $eventService->logEvent($id, null, 'ISSUE_OPENED', $actorType, $userId, $description);
                $success = true;
                break;

            default:
                throw new Exception("Ação desconhecida.");
        }

        if ($success) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erro ao processar ação']); // Generic fallback
        }
    } else {
        throw new Exception("Método não suportado.");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
