<?php
define('API_MODE', true);
require __DIR__ . '/../../Banco de dados/conexao.php';

echo "<h1>Diagnóstico e Correção de Banco de Dados</h1>";

try {
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    echo "<p>Conectado ao banco: <strong>$driver</strong></p>";

    if ($driver === 'pgsql') {
        echo "<h2>PostgreSQL Detectado - Verificando Sequência...</h2>";

        // 1. Tentar descobrir o nome da sequencia
        // Consulta para listar sequências
        $stmtSeq = $pdo->query("SELECT c.relname FROM pg_class c WHERE c.relkind = 'S'");
        $sequences = $stmtSeq->fetchAll(PDO::FETCH_COLUMN);

        $possibleNames = ['pedidos_id_seq', 'pedidos_seq', 'pedidos_id_seq1'];
        $found = false;

        echo "<ul>";
        foreach ($sequences as $s) echo "<li>Encontrada: $s</li>";
        echo "</ul>";

        foreach ($possibleNames as $name) {
            if (in_array($name, $sequences)) {
                $found = $name;
                break;
            }
        }

        // Se nao achar nos nomes padrao, tenta inferir pelo padrao do postgres explicito
        if (!$found) {
            // Tenta pegar o default value da coluna id
            // Mas vamos tentar 'pedidos_id_seq' mesmo se não listou, vai que...
            $found = 'pedidos_id_seq';
        }

        if ($found) {
            echo "<p>Tentando corrigir sequência: <strong>$found</strong></p>";

            // Pega o maior ID atual
            $stmtMax = $pdo->query("SELECT MAX(id) FROM pedidos");
            $maxId = $stmtMax->fetchColumn();
            $nextId = ($maxId) ? $maxId + 1 : 1;

            echo "<p>Maior ID atual na tabela 'pedidos': <strong>$maxId</strong></p>";
            echo "<p>Próximo ID deve ser: <strong>$nextId</strong></p>";

            // Atualiza a sequência
            // setval('seq', valor, false) -> proximo nextval sera 'valor'
            // setval('seq', valor, true) -> proximo nextval sera 'valor' + 1. 
            // Se usarmos MAX(id) e is_called=true, o proximo sera MAX+1. Correto.

            // NOTA: Se maxId for null (tabela vazia), nextId é 1.
            // setval(..., 1, false) faz o proximo ser 1.

            if ($maxId) {
                // Se tem dados, setval(seq, maxId, true)
                $pdo->query("SELECT setval('$found', $maxId, true)");
            } else {
                // Se vazia, setval(seq, 1, false)
                $pdo->query("SELECT setval('$found', 1, false)");
            }

            echo "<h3 style='color:green'>SUCESSO! Sequência sincronizada.</h3>";
            echo "<p>Tente fazer a compra novamente agora.</p>";
        } else {
            echo "<h3 style='color:red'>ERRO: Não consegui identificar a sequência da tabela pedidos.</h3>";
        }
    } else {
        echo "<p>Este script é específico para correções em PostgreSQL. Seu driver é $driver.</p>";
        echo "<p>Se você está vendo isso localmente e usa MySQL, o erro de 'Unique violation' não deveria acontecer da mesma forma.</p>";
    }
} catch (Exception $e) {
    echo "<h3 style='color:red'>ERRO FATAL: " . $e->getMessage() . "</h3>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
