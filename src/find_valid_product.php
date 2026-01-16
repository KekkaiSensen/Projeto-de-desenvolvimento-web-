<?php
// Tenta incluir com tratamento de erro e caminhos absolutos se necessário
$path = __DIR__ . '/../Banco de dados/conexao.php';
if (!file_exists($path)) {
    die("Erro: Arquivo conexao.php nao encontrado em: $path\n");
}
require $path;

try {
    // Busca qualquer produto ativo
    $stmt = $pdo->query("SELECT id, nome FROM PRODUTOS WHERE status = 'ativo' LIMIT 1");
    $prod = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($prod) {
        echo "Valid Product Found: ID=" . $prod['id'] . " Name=" . $prod['nome'] . "\n";
    } else {
        echo "No active products found.\n";
    }
} catch (Exception $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
}
