<?php
require __DIR__ . '/../Banco de dados/conexao.php';

try {
    $stmt = $pdo->query("SELECT * FROM cupons");
    $cupons = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Coupons found: " . count($cupons) . "\n";
    print_r($cupons);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
