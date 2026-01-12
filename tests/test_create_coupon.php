<?php
require __DIR__ . '/../Banco de dados/conexao.php';

// Hardcoded values for test
$usuario_id = 3;
$codigo = 'TEST-' . time();
$tipo = 'porcentagem';
$valor = 10;
$minimo = 50;
$limite = 100;
$data_fim = date('Y-m-d', strtotime('+30 days')) . ' 23:59:59';
$ativo = 1;

echo "Attempting to create coupon: $codigo\n";

try {
    echo "Using User ID: $usuario_id\n";

    echo "Preparing coupon insert...\n";
    $sql = "INSERT INTO cupons (codigo, descricao, tipo_desconto, valor_desconto, valor_minimo, data_inicio, data_fim, limite_uso, ativo, usuario_id) VALUES (?, 'Cupom de Teste', ?, ?, ?, NOW(), ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    echo "Executing coupon insert...\n";
    $params = [$codigo, $tipo, $valor, $minimo, $data_fim, $limite, $ativo, $usuario_id];
    // print_r($params);

    $result = $stmt->execute($params);

    if ($result) {
        echo "SUCCESS: Coupon created.\n";
        echo "New Coupon ID: " . $pdo->lastInsertId() . "\n";
    } else {
        echo "FAILURE: " . implode(" ", $stmt->errorInfo()) . "\n";
    }
} catch (PDOException $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
}
