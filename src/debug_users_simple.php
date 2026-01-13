<?php
require __DIR__ . '/../Banco de dados/conexao.php';

echo "Querying usuarios...\n";
try {
    $stmt = $pdo->query("SELECT * FROM usuarios LIMIT 1");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        echo "Valid User ID: " . $user['id'] . "\n";
    } else {
        echo "No users found.\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
