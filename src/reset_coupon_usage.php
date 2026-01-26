<?php
require __DIR__ . '/../Banco de dados/conexao.php';

$email = 'joao.teste+qa@example.com';
$cupomC = 'DESCONTO10';

try {
    // Get User ID
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $userId = $stmt->fetchColumn();

    if (!$userId) {
        die("User not found\n");
    }

    // Get Cupom ID
    $stmt = $pdo->prepare("SELECT id FROM cupons WHERE codigo = ?");
    $stmt->execute([$cupomC]);
    $cupomId = $stmt->fetchColumn();

    if (!$cupomId) {
        die("Coupon not found\n");
    }

    // Delete Usage
    $stmt = $pdo->prepare("DELETE FROM cupom_uso WHERE usuario_id = ? AND cupom_id = ?");
    $stmt->execute([$userId, $cupomId]);

    echo "Usage reset for User $userId and Coupon $cupomId\n";
} catch (PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}
