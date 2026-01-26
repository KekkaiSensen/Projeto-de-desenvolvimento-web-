<?php
require __DIR__ . '/../Banco de dados/conexao.php';

try {
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables in DB:\n" . implode(", ", $tables) . "\n\n";

    if (in_array('usuarios', $tables)) {
        $stmt = $pdo->query("DESCRIBE usuarios");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Columns in usuarios:\n";
        foreach ($columns as $col) {
            echo $col['Field'] . "\n";
        }
    } else {
        echo "Table 'usuarios' not found.\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
