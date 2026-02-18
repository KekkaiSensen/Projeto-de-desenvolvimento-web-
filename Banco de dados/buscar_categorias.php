<?php
// Salve este arquivo como: Banco de dados/buscar_categorias.php

define('API_MODE', true); // Define que estamos em modo API para o conexao.php não dar die()

header('Content-Type: application/json');

try {
    require __DIR__ . '/../vendor/autoload.php'; // Autoload do Composer
    require __DIR__ . '/conexao.php'; // Assume que conexao.php está no mesmo diretório

    $cache = new Services\CacheService();

    // Tenta buscar do cache
    $categorias = $cache->remember('categorias_lista', 600, function () use ($pdo) {
        // Busca o ID e o Nome da tabela categorias, ordenando por nome
        $stmt = $pdo->prepare("SELECT id, nome FROM categorias ORDER BY nome ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    });

    // Retorna um JSON com sucesso e a lista de categorias
    echo json_encode(['sucesso' => true, 'categorias' => $categorias]);
} catch (Throwable $e) {
    // Em caso de erro, envia uma resposta JSON de falha
    http_response_code(500); // Internal Server Error
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro: ' . $e->getMessage()]);
}
