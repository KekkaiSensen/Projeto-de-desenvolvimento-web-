<?php
session_start();
header('Content-Type: application/json');
require 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não logado.']);
    exit();
}
$usuario_id = $_SESSION['usuario_id'];

$uploadDir = '../assets/imagens/Produtos/';
// Diretorio local ainda pode ser usado para fallback ou removido se tudo for p/ nuvem
// if (!file_exists($uploadDir) && !mkdir($uploadDir, 0777, true)) { ... }

// Autoload composer
require_once __DIR__ . '/../vendor/autoload.php';

use Services\CloudinaryService;
use Services\CacheService;

try {
    $cloudinary = new CloudinaryService();
} catch (\Throwable $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro config CDN: ' . $e->getMessage()]);
    exit();
}

try {
    $cache = new CacheService();
} catch (\Throwable $e) {
    // Falha silenciosa ou log
}

try {
    $titulo = $_POST['titulo'] ?? 'Produto sem nome';
    // ... (rest of the code) ...
    $pdo->commit();

    // Limpa o cache
    if (isset($cache)) {
        try {
            $cache->forget('home_produtos_destaque');
            $cache->forget('home_produtos_carousel');
        } catch (\Throwable $e) {
            // Ignore cache errors
        }
    }

    echo json_encode(['sucesso' => true, 'mensagem' => 'Produto publicado com sucesso!']);
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    file_put_contents(__DIR__ . '/../debug_product_error.log', date('Y-m-d H:i:s') . " DB Error: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n", FILE_APPEND);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro DB: ' . $e->getMessage()]);
} catch (\Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    file_put_contents(__DIR__ . '/../debug_product_error.log', date('Y-m-d H:i:s') . " General Error: " . $e->getMessage() . "\n", FILE_APPEND);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro Geral: ' . $e->getMessage()]);
}
