<?php

use PHPUnit\Framework\TestCase;
use Services\OrderService;
use Services\OrderEventService;
use Services\NotificationService;

class OrderServiceTest extends TestCase
{
    private $pdoMock;
    private $orderService;
    private $eventServiceMock;
    private $notificationServiceMock;

    protected function setUp(): void
    {
        $this->pdoMock = $this->createMock(\PDO::class);
        $this->eventServiceMock = $this->createMock(Services\OrderEventService::class);
        $this->notificationServiceMock = $this->createMock(Services\NotificationService::class);

        $this->orderService = new OrderService($this->pdoMock, $this->eventServiceMock, $this->notificationServiceMock);
    }

    public function testStatusTransitionSuccess()
    {
        // Mock getOrder
        $stmtGet = $this->createMock(\PDOStatement::class);
        $stmtGet->method('fetch')->willReturn([
            'id' => 1,
            'status' => 'CREATED',
            'usuario_id' => 1,
            'supplier_id' => 2
        ]);

        // Mock Update
        $stmtUpdate = $this->createMock(\PDOStatement::class);
        $stmtUpdate->method('execute')->willReturn(true);

        $this->pdoMock->method('prepare')->willReturnOnConsecutiveCalls($stmtGet, $stmtUpdate);

        // Expect Event Log
        $this->eventServiceMock->expects($this->once())
            ->method('logEvent')
            ->with(1, 'CREATED', 'PROCESSING', 'supplier', 2, '');

        $result = $this->orderService->transitionTo(1, 'PROCESSING', 'supplier', 2);
        $this->assertTrue($result);
    }
}
