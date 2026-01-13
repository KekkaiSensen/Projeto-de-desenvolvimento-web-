<?php
require __DIR__ . '/../Banco de dados/conexao.php';

try {
    echo "Checking PEDIDOS table...\n";
    $stmt = $pdo->query("DESCRIBE PEDIDOS");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "Columns: " . implode(", ", $columns) . "\n";

    if (!in_array('cupom_id', $columns)) {
        echo "cupom_id missing. Adding...\n";

        // Determinar nome da tabela de cupons (CUPONS ou cupons)
        $cuponsTable = 'CUPONS';
        $stmt_tables = $pdo->query("SHOW TABLES LIKE 'CUPONS'");
        if ($stmt_tables->rowCount() == 0) {
            $stmt_tables = $pdo->query("SHOW TABLES LIKE 'cupons'");
            if ($stmt_tables->rowCount() > 0) {
                $cuponsTable = 'cupons';
            } else {
                echo "Warning: Tables CUPONS/cupons not found. Creating cupom_id without FK.\n";
                $pdo->exec("ALTER TABLE PEDIDOS ADD COLUMN cupom_id INT NULL");
                $cuponsTable = null;
            }
        }

        if ($cuponsTable) {
            try {
                $pdo->exec("ALTER TABLE PEDIDOS ADD COLUMN cupom_id INT NULL");
                // Tenta adicionar FK separadamente para evitar erro se falhar apenas a constraint
                $pdo->exec("ALTER TABLE PEDIDOS ADD CONSTRAINT fk_pedidos_cupom FOREIGN KEY (cupom_id) REFERENCES $cuponsTable(id)");
                echo "cupom_id added with FK to $cuponsTable.\n";
            } catch (Exception $e) {
                echo "Added cupom_id but failed to add FK: " . $e->getMessage() . "\n";
            }
        }
    } else {
        echo "cupom_id already exists.\n";
    }

    if (!in_array('valor_desconto', $columns)) {
        echo "valor_desconto missing. Adding...\n";
        $pdo->exec("ALTER TABLE PEDIDOS ADD COLUMN valor_desconto DECIMAL(10, 2) DEFAULT 0.00");
        echo "valor_desconto added.\n";
    } else {
        echo "valor_desconto already exists.\n";
    }

    echo "Done verifying PEDIDOS schema.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
