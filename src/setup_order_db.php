<?php
require __DIR__ . '/../Banco de dados/conexao.php';

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "--- 1. Updating PEDIDOS table ---\n";

    // Check existing columns in PEDIDOS
    $stmt = $pdo->query("DESCRIBE pedidos");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Add supplier_id
    if (!in_array('supplier_id', $columns)) {
        echo "Adding supplier_id...\n";
        // Assuming 'usuarios' is the table for suppliers. If it's 'fornecedores', we need to check.
        // Based on previous reads, suppliers are users with type 'fornecedor', likely in 'usuarios' table.
        $pdo->exec("ALTER TABLE pedidos ADD COLUMN supplier_id INT NULL AFTER usuario_id");
        // Optional: Add FK if we are sure about the table. Let's assume 'usuarios' for now or just add index.
        // $pdo->exec("ALTER TABLE pedidos ADD CONSTRAINT fk_pedidos_supplier FOREIGN KEY (supplier_id) REFERENCES usuarios(id)"); 
    } else {
        echo "supplier_id already exists.\n";
    }

    // Add timestamps
    $timestamps = ['shipped_at', 'delivered_at', 'canceled_at', 'created_at'];
    foreach ($timestamps as $col) {
        if (!in_array($col, $columns)) {
            echo "Adding $col...\n";
            $pdo->exec("ALTER TABLE pedidos ADD COLUMN $col DATETIME NULL");
        } else {
            echo "$col already exists.\n";
        }
    }

    // Make sure 'status' column exists and is VARCHAR (it likely is)
    // We update it to make sure it's long enough if needed, but usually it is.

    echo "\n--- 2. Creating ORDER_EVENTS table ---\n";
    $sql_events = "CREATE TABLE IF NOT EXISTS order_events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        old_status VARCHAR(50),
        new_status VARCHAR(50) NOT NULL,
        description TEXT,
        actor_type VARCHAR(20) NOT NULL, -- client, supplier, system
        actor_id INT, -- user_id if known
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES pedidos(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql_events);
    echo "order_events table checks out.\n";

    echo "\n--- 3. Creating ORDER_ISSUES table ---\n";
    $sql_issues = "CREATE TABLE IF NOT EXISTS order_issues (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        user_id INT NOT NULL,
        type VARCHAR(50),
        description TEXT,
        status VARCHAR(20) DEFAULT 'open', -- open, resolved
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES pedidos(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql_issues);
    echo "order_issues table checks out.\n";

    echo "\nDatabase updates completed successfully.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
