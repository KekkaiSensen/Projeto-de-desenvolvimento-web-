<?php

use PHPUnit\Framework\TestCase;
use Services\NotificationService;

class NotificationServiceTest extends TestCase
{
    private $pdoMock;
    private $notificationService;

    protected function setUp(): void
    {
        $this->pdoMock = $this->createMock(\PDO::class);
        $this->notificationService = new NotificationService($this->pdoMock);
    }

    public function testCriarNotificacao()
    {
        $stmtMock = $this->createMock(\PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);

        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('INSERT INTO notificacoes'))
            ->willReturn($stmtMock);

        $result = $this->notificationService->create(1, 'Teste msg', 'info', '#');
        $this->assertTrue($result);
    }

    public function testLerNotificacoesUsuario()
    {
        $stmtMock = $this->createMock(\PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);
        $stmtMock->method('fetchAll')->willReturn([
            ['id' => 1, 'mensagem' => 'Teste 1'],
            ['id' => 2, 'mensagem' => 'Teste 2']
        ]);

        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('SELECT * FROM notificacoes'))
            ->willReturn($stmtMock);

        $notificacoes = $this->notificationService->getByUser(1);
        $this->assertCount(2, $notificacoes);
    }

    public function testContarNaoLidas()
    {
        $stmtMock = $this->createMock(\PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);
        $stmtMock->method('fetchColumn')->willReturn(5);

        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('SELECT COUNT(*)'))
            ->willReturn($stmtMock);

        $count = $this->notificationService->getUnreadCount(1);
        $this->assertEquals(5, $count);
    }

    public function testMarcarComoLida()
    {
        $stmtMock = $this->createMock(\PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);

        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('UPDATE notificacoes SET lida = 1'))
            ->willReturn($stmtMock);

        $result = $this->notificationService->markAsRead(10, 1);
        $this->assertTrue($result);
    }
}
