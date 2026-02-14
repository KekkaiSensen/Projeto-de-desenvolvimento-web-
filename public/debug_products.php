<?php
// public/debug_products.php
require __DIR__ . '/../Banco de dados/conexao.php';
require_once '../vendor/autoload.php';

echo "<h1>Debug de Produtos</h1>";

try {
    // 1. Listar tabelas
    echo "<h2>Tabelas no Banco</h2>";
    $tables = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'")->fetchAll(PDO::FETCH_COLUMN);
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>$table</li>";
    }
    echo "</ul>";

    // 2. Verificar Tabela de Produtos
    echo "<h2>Conteúdo da tabela 'produtos'</h2>";
    if (in_array('produtos', $tables)) {
        $count = $pdo->query("SELECT COUNT(*) FROM produtos")->fetchColumn();
        echo "Total de produtos: <strong>$count</strong><br>";

        if ($count > 0) {
            $stmt = $pdo->query("SELECT * FROM produtos LIMIT 5");
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<pre>" . print_r($products, true) . "</pre>";
        } else {
            echo "A tabela 'produtos' está vazia.<br>";
        }
    } else {
        echo "<strong style='color:red'>ERRO: Tabela 'produtos' não encontrada!</strong><br>";
    }

    // 3. Testar Query da Home
    echo "<h2>Teste da Query da Home</h2>";
    $sql = "SELECT
                p.*,
                AVG(a.nota) as media_avaliacoes,
                COUNT(a.nota) as total_avaliacoes
            FROM
                produtos p
            LEFT JOIN
                avaliacoes a ON p.id = a.produto_id
            WHERE
                p.status = 'ativo'
            GROUP BY
                p.id
            ORDER BY
                p.ordem_destaque ASC, p.id DESC
            LIMIT 20";

    echo "<pre>$sql</pre>";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Resultados retornados: <strong>" . count($results) . "</strong><br>";
    if (count($results) > 0) {
        echo "<pre>" . print_r($results, true) . "</pre>";
    } else {
        echo "Nenhum produto retornado pela query (verifique se estão 'ativos').";
    }
} catch (PDOException $e) {
    echo "<h2>ERRO PDO</h2>";
    echo $e->getMessage();
}
