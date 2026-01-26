<?php

namespace Services;

use PDO;

class OrderEventService
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function logEvent($orderId, $oldStatus, $newStatus, $actorType, $actorId, $description = '')
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO order_events (order_id, old_status, new_status, actor_type, actor_id, description)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $orderId,
            $oldStatus,
            $newStatus,
            $actorType,
            $actorId,
            $description
        ]);
    }

    public function getEvents($orderId)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM order_events 
            WHERE order_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
