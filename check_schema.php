<?php
require 'Banco de dados/conexao.php'; // Path relative to where I'll run it, or I'll adjust

try {
    echo "--- DESCRIBE PEDIDOS ---\n";
    $stmt = $pdo->query("DESCRIBE PEDIDOS");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }

    echo "\n--- DESCRIBE PRODUTOS ---\n";
    $stmt = $pdo->query("DESCRIBE PRODUTOS");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
