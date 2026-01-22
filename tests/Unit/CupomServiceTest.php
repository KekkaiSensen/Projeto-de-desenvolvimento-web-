<?php

use PHPUnit\Framework\TestCase;
use Services\CupomService;
use Model\Cupom;

class CupomServiceTest extends TestCase
{
    private $pdoMock;
    private $cupomService;

    protected function setUp(): void
    {
        $this->pdoMock = $this->createMock(\PDO::class);
        $this->cupomService = new CupomService($this->pdoMock);
    }

    public function testValidarCupomValido()
    {
        // Setup Cupom Object to return
        $cupom = new Cupom($this->pdoMock);
        $cupom->id = 1;
        $cupom->codigo = 'TESTE10';
        $cupom->tipo_desconto = 'porcentagem'; // field name in Cupom.php is tipo_desconto
        $cupom->valor_desconto = 10; // field name in Cupom.php is valor_desconto
        $cupom->valor_minimo = 50;
        $cupom->data_fim = '2099-12-31';
        $cupom->limite_uso = 100;
        $cupom->ativo = 1;

        $stmtMock = $this->createMock(\PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);
        $stmtMock->method('fetchObject')->willReturn($cupom); // Use fetchObject

        $this->pdoMock->method('prepare')->willReturn($stmtMock);

        // ACT
        $result = $this->cupomService->validarCupom('TESTE10', 100.00);

        // ASSERT
        $this->assertTrue($result['valid']);
        $this->assertEquals(10, $result['desconto_calculado']);
    }

    public function testValidarCupomInvalido()
    {
        $stmtMock = $this->createMock(\PDOStatement::class);
        $stmtMock->method('fetchObject')->willReturn(false); // Not found
        $this->pdoMock->method('prepare')->willReturn($stmtMock);

        $result = $this->cupomService->validarCupom('INVALIDO', 100.00);

        $this->assertFalse($result['valid']);
    }

    public function testValidarCupomValorMinimo()
    {
        $cupom = new Cupom($this->pdoMock);
        $cupom->id = 1;
        $cupom->codigo = 'MIN100';
        $cupom->tipo_desconto = 'fixo';
        $cupom->valor_desconto = 20;
        $cupom->valor_minimo = 100;
        $cupom->data_fim = '2099-12-31';

        $stmtMock = $this->createMock(\PDOStatement::class);
        $stmtMock->method('fetchObject')->willReturn($cupom);
        $this->pdoMock->method('prepare')->willReturn($stmtMock);

        // Cart total only 50
        $result = $this->cupomService->validarCupom('MIN100', 50.00);

        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('mínimo', $result['message']);
    }
}
