<?php
session_start(); // Inicia a sessão para persistir o cupom
header('Content-Type: application/json');

// Include dependencies
require_once __DIR__ . '/../../Banco de dados/conexao.php';
require_once __DIR__ . '/../Services/CupomService.php';

use Services\CupomService;

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['codigo']) || !isset($data['total'])) {
    http_response_code(400);
    echo json_encode(['valid' => false, 'message' => 'Dados incompletos.']);
    exit;
}

$codigo = trim($data['codigo']);
$cartTotal = floatval($data['total']);
$usuarioId = isset($data['usuario_id']) ? intval($data['usuario_id']) : null;

try {
    $cupomService = new CupomService($pdo);
    $resultado = $cupomService->validarCupom($codigo, $cartTotal, $usuarioId);

    if ($resultado['valid']) {
        // Armazena na sessão para o processa_pedido.php usar
        $_SESSION['checkout_cupom'] = [
            'codigo' => $codigo,
            'desconto' => $resultado['desconto_calculado'],
            'cupom_id' => $resultado['cupom']->id
        ];
    } else {
        // Se inválido, remove qualquer cupom prévio da sessão
        unset($_SESSION['checkout_cupom']);
    }

    echo json_encode($resultado);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['valid' => false, 'message' => 'Erro interno ao validar cupom.']);
}
