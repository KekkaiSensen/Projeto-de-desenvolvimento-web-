<?php
// Configurações do banco de dados

// Tenta carregar o autoload se ainda não foi carregado
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoloadPath) && !class_exists('Dotenv\Dotenv')) {
    require_once $autoloadPath;
}

// Carrega as variáveis de ambiente se a classe Dotenv existir
if (class_exists('Dotenv\Dotenv') && file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->safeLoad();
}

// Configurações do banco de dados com fallback
// Configurações do banco de dados com fallback
$driver = $_ENV['DB_CONNECTION'] ?? 'mysql'; // 'mysql' ou 'pgsql'
$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$dbname = $_ENV['DB_NAME'] ?? 'bancodadosteste';
$dbusername = $_ENV['DB_USER'] ?? 'root';
$dbpassword = $_ENV['DB_PASS'] ?? '1234';
$port = $_ENV['DB_PORT'] ?? ($driver === 'pgsql' ? '5432' : '3306');

$dsn = "$driver:host=$host;port=$port;dbname=$dbname";
if ($driver === 'mysql') {
    $dsn .= ";charset=utf8mb4";
}

try {
    // Cria a conexão PDO
    $pdo = new PDO($dsn, $dbusername, $dbpassword);

    // Define o modo de erro para exceções, para podermos ver os erros
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Define o charset
    if ($driver === 'pgsql') {
        $pdo->exec("SET NAMES 'UTF8'");
    } else {
        $pdo->exec("SET NAMES 'utf8mb4'");
    }
} catch (PDOException $e) {
    // Em caso de falha, exibe o erro. 
    // Em um site em produção, você deve logar este erro, não exibi-lo.
    die('Conexão falhou: ' . $e->getMessage());
}
