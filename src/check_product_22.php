<?php
require 'Banco de dados/conexao.php'; // Adjust path if needed
try {
    $stmt = $pdo->prepare("SELECT * FROM PRODUTOS WHERE id = 22");
    $stmt->execute();
    $prod = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($prod) {
        echo "Product 22 found. Status: " . $prod['status'] . "\n";
    } else {
        echo "Product 22 NOT found.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
