<?php

namespace Services;

use PDO;
use Exception;

class OrderService
{
    private $pdo;
    private $eventService;
    private $notificationService;

    // Valid states
    const STATUS_CREATED = 'CREATED';
    const STATUS_PAID = 'PAID';
    const STATUS_PROCESSING = 'PROCESSING';
    const STATUS_SHIPPED = 'SHIPPED';
    const STATUS_DELIVERED = 'DELIVERED';
    const STATUS_CANCELED = 'CANCELED';

    public function __construct(PDO $pdo, OrderEventService $eventService, ?NotificationService $notificationService = null)
    {
        $this->pdo = $pdo;
        $this->eventService = $eventService;
        $this->notificationService = $notificationService;
    }

    public function getOrder($orderId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM pedidos WHERE id = ?");
        $stmt->execute([$orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function transitionTo($orderId, $newStatus, $actorType, $actorId, $reason = '')
    {
        $order = $this->getOrder($orderId);
        if (!$order) {
            throw new Exception("Order not found.");
        }

        $currentStatus = $order['status'];

        // Normalize status to uppercase for comparison if needed, currently assumes DB has correct case or we enforcing it.
        // In processa_pedido.php it inserts 'processando'. We should standardize.
        // Let's assume we mapping 'processando' -> 'CREATED' or 'PAID' or just standardizing on new constants from now on.
        // But legacy data might be 'processando'. Let's handle 'processando' as equivalent to PAID for now if needed?
        // Actually, let's treat 'processando' as 'PAID' (waiting for processing) or stick to the PLAN.

        // Allowed transitions logic
        if (!$this->canTransition($currentStatus, $newStatus)) {
            throw new Exception("Invalid status transition from $currentStatus to $newStatus.");
        }

        // Apply update
        $updateSql = "UPDATE pedidos SET status = ?";
        $params = [$newStatus];

        // Update timestamps based on new status
        if ($newStatus === self::STATUS_SHIPPED) {
            $updateSql .= ", shipped_at = NOW()";
        } elseif ($newStatus === self::STATUS_DELIVERED) {
            $updateSql .= ", delivered_at = NOW()";
        } elseif ($newStatus === self::STATUS_CANCELED) {
            $updateSql .= ", canceled_at = NOW()";
        }

        $updateSql .= " WHERE id = ?";
        $params[] = $orderId;

        $stmt = $this->pdo->prepare($updateSql);
        $stmt->execute($params);

        // Log event
        $this->eventService->logEvent($orderId, $currentStatus, $newStatus, $actorType, $actorId, $reason);

        // Notify Client (Owner)
        if ($this->notificationService && $order['usuario_id']) {
            try {
                // If the actor is NOT the client, notify the client.
                // Or simply always notify client of status changes (except maybe if they did it themselves?)
                // Usually good to notify even if self-triggered for confirmation, but let's avoid noise.
                // If actorType is 'supplier' or 'system', notify client.
                if ($actorType !== 'client') {
                    $msg = "Seu pedido #{$orderId} mudou de status para: " . $newStatus; // Translate status?
                    $this->notificationService->create(
                        $order['usuario_id'],
                        $msg,
                        'info',
                        'tela_minha_conta.php?tab=painel-compras' // Link to their orders
                    );
                }
            } catch (\Exception $e) {
                // Ignore notification failure
            }
        }

        return true;
    }

    private function canTransition($current, $target)
    {
        // Map legacy 'processando' to 'PAID' for logic check if needed, 
        // OR assume we update processa_pedido to insert CREATED/PAID.
        // Let's be strict and fix processa_pedido later.

        // Cancellation is almost always allowed except if already delivered (usually)
        if ($target === self::STATUS_CANCELED) {
            return in_array($current, [self::STATUS_CREATED, self::STATUS_PAID, self::STATUS_PROCESSING]);
        }

        $transitions = [
            self::STATUS_CREATED => [self::STATUS_PAID, self::STATUS_CANCELED, self::STATUS_PROCESSING], // Allowing PROCESSING directly for manual flow
            self::STATUS_PAID => [self::STATUS_PROCESSING, self::STATUS_CANCELED],
            self::STATUS_PROCESSING => [self::STATUS_SHIPPED, self::STATUS_CANCELED],
            self::STATUS_SHIPPED => [self::STATUS_DELIVERED],
            self::STATUS_DELIVERED => [], // End state
            self::STATUS_CANCELED => [] // End state
        ];

        // Handle legacy 'processando' -> treat as PAID? Or CREATED? 
        // If DB has 'processando', we should probably migrate it or mapping it.
        // For now, let's allow 'processando' -> 'PROCESSING' or 'SHIPPED' to be safe?
        if ($current == 'processando') {
            // Let's assume 'processando' is equivalent to 'PAID' in the new flow
            if ($target == self::STATUS_PROCESSING || $target == self::STATUS_CANCELED) return true;
        }

        return isset($transitions[$current]) && in_array($target, $transitions[$current]);
    }
}
