<?php
require 'conexao.php'; // Ensure this path is correct relative to execution

try {
    // Create cupons table
    $sqlCupons = "CREATE TABLE IF NOT EXISTS cupons (
        id INT AUTO_INCREMENT PRIMARY KEY,
        codigo VARCHAR(50) NOT NULL UNIQUE,
        descricao VARCHAR(255),
        tipo_desconto ENUM('porcentagem', 'fixo') NOT NULL,
        valor_desconto DECIMAL(10,2) NOT NULL,
        valor_minimo DECIMAL(10,2) DEFAULT 0.00,
        data_inicio DATETIME DEFAULT CURRENT_TIMESTAMP,
        data_fim DATETIME NULL,
        limite_uso INT NULL,
        ativo TINYINT(1) DEFAULT 1,
        usuario_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $pdo->exec($sqlCupons);
    echo "Table 'cupons' created or already exists.\n";

    // Create cupom_uso table
    $sqlCupomUso = "CREATE TABLE IF NOT EXISTS cupom_uso (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cupom_id INT NOT NULL,
        usuario_id INT NOT NULL,
        pedido_id INT,
        data_uso DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (cupom_id) REFERENCES cupons(id) ON DELETE CASCADE,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
        -- Add FK for pedido_id if pedidos table structure is known and stable
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $pdo->exec($sqlCupomUso);
    echo "Table 'cupom_uso' created or already exists.\n";
} catch (PDOException $e) {
    echo "Error creating tables: " . $e->getMessage() . "\n";
}
