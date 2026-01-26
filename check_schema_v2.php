<?php
require 'Banco de dados/conexao.php';

try {
    echo "--- DESCRIBE PRODUTOS ---\n";
    $stmt = $pdo->query("DESCRIBE produtos");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
