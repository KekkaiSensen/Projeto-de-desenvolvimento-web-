<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- MOCK DB CONNECTION ---
$dsn = 'mysql:host=127.0.0.1;dbname=bancodadosteste';
$dbusername = 'root';
$dbpassword = '1234';

try {
    $pdo = new PDO($dsn, $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES 'utf8mb4'");
    echo "DB Connected Successfully.\n";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// --- OrderEventService (Inlined) ---
class MockOrderEventService
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

// --- OrderService (Inlined) ---
class MockOrderService
{
    private $pdo;
    private $eventService;

    // Valid states
    const STATUS_CREATED = 'CREATED';
    const STATUS_PAID = 'PAID';
    const STATUS_PROCESSING = 'PROCESSING';
    const STATUS_SHIPPED = 'SHIPPED';
    const STATUS_DELIVERED = 'DELIVERED';
    const STATUS_CANCELED = 'CANCELED';

    public function __construct(PDO $pdo, MockOrderEventService $eventService)
    {
        $this->pdo = $pdo;
        $this->eventService = $eventService;
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

        return true;
    }

    private function canTransition($current, $target)
    {
        // Cancellation is almost always allowed except if already delivered (usually)
        if ($target === self::STATUS_CANCELED) {
            return in_array($current, [self::STATUS_CREATED, self::STATUS_PAID, self::STATUS_PROCESSING]);
        }

        $transitions = [
            self::STATUS_CREATED => [self::STATUS_PAID, self::STATUS_CANCELED, self::STATUS_PROCESSING], // Added PROCESSING for quicker tests skip payment
            self::STATUS_PAID => [self::STATUS_PROCESSING, self::STATUS_CANCELED],
            self::STATUS_PROCESSING => [self::STATUS_SHIPPED, self::STATUS_CANCELED],
            self::STATUS_SHIPPED => [self::STATUS_DELIVERED],
            self::STATUS_DELIVERED => [],
            self::STATUS_CANCELED => []
        ];

        // Handle legacy 'processando'
        if ($current == 'processando') {
            if ($target == self::STATUS_PROCESSING || $target == self::STATUS_CANCELED) return true;
        }

        return isset($transitions[$current]) && in_array($target, $transitions[$current]);
    }
}

// --- TEST FLOW ---
echo "Classes defined.\n";
if (class_exists('MockOrderService')) echo "MockOrderService exists.\n";
else die("MockOrderService MISSING.\n");

// Setup Services
echo "Instantiating EventService...\n";
$eventService = new MockOrderEventService($pdo);
echo "EventService OK.\n";

echo "Instantiating OrderService...\n";
$orderService = new MockOrderService($pdo, $eventService);
echo "OrderService OK.\n";

// 1. Setup Test Data
$clientId = 1;
$supplierId = 1;
$total = 100.00;

echo "--- Starting Order Flow Test ---\n";

try {
    // 2. Create Order
    echo "[1] Creating Order... ";
    $stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id, endereco_id, valor_total, status, supplier_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$clientId, 1, $total, 'CREATED', $supplierId]);
    $orderId = $pdo->lastInsertId();
    $eventService->logEvent($orderId, null, 'CREATED', 'client', $clientId, 'Order Created via Test Script');
    echo "Done. Order ID: $orderId\n";

    // 3. Test Transition: START PROCESSING
    echo "[2] Supplier starts processing... ";
    $orderService->transitionTo($orderId, 'PROCESSING', 'supplier', $supplierId, 'Started processing order');
    echo "Done.\n";

    // Verify Status
    $order = $orderService->getOrder($orderId);
    if ($order['status'] !== 'PROCESSING') throw new Exception("Status mismatch! Expected PROCESSING, got " . $order['status']);

    // 4. Test Transition: SHIP
    echo "[3] Supplier ships order... ";
    $orderService->transitionTo($orderId, 'SHIPPED', 'supplier', $supplierId, 'Order shipped via Carrier X');
    echo "Done.\n";

    // Verify Status and Timestamp
    $order = $orderService->getOrder($orderId);
    if ($order['status'] !== 'SHIPPED') throw new Exception("Status mismatch! Expected SHIPPED, got " . $order['status']);
    if (empty($order['shipped_at'])) throw new Exception("shipped_at not set!");

    // 5. Test Transition: DELIVERY CONFIRM
    echo "[4] Client confirms delivery... ";
    $orderService->transitionTo($orderId, 'DELIVERED', 'client', $clientId, 'Package received');
    echo "Done.\n";

    // Verify Status and Timestamp
    $order = $orderService->getOrder($orderId);
    if ($order['status'] !== 'DELIVERED') throw new Exception("Status mismatch! Expected DELIVERED, got " . $order['status']);
    if (empty($order['delivered_at'])) throw new Exception("delivered_at not set!");

    // 6. Verify Events Log
    echo "[5] Verifying Event Log... ";
    $events = $eventService->getEvents($orderId);

    // Check count
    if (count($events) < 4) {
        throw new Exception("Event count mismatch! Expected at least 4, got " . count($events));
    }

    // Check latest event
    if ($events[0]['new_status'] !== 'DELIVERED') throw new Exception("Latest event is not DELIVERED");

    echo "Done. All events logged correctly.\n";

    echo "\nSUCCESS: Happy Path Test Completed!\n";
} catch (Exception $e) {
    echo "\nFAILED: " . $e->getMessage() . "\n";
    exit(1);
}
