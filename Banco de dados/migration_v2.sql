-- Add missing columns to 'pedidos' table
ALTER TABLE pedidos ADD COLUMN supplier_id INT DEFAULT NULL;
-- 'data_pedido' already exists, so we don't need 'created_at'.

-- Create 'order_events' table
CREATE TABLE IF NOT EXISTS order_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    old_status VARCHAR(50),
    new_status VARCHAR(50),
    actor_type VARCHAR(20), -- 'client', 'supplier', 'system'
    actor_id INT,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP, -- Kept as created_at to match OrderEventService
    FOREIGN KEY (order_id) REFERENCES pedidos(id) ON DELETE CASCADE
);

-- Create 'order_issues' table
CREATE TABLE IF NOT EXISTS order_issues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    user_id INT NOT NULL,
    type VARCHAR(50),
    description TEXT,
    status VARCHAR(20) DEFAULT 'open',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES usuarios(id)
);
