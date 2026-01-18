<?php
require __DIR__ . '/../Banco de dados/conexao.php';
try {
    $stmt = $pdo->query("DESCRIBE pedidos");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Columns: " . implode(", ", $columns) . "\n";
} catch (Exception $e) {
    echo $e->getMessage();
}
