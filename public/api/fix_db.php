<?php
define('API_MODE', true);
require __DIR__ . '/../../Banco de dados/conexao.php';

echo "<h1>Diagnóstico e Correção de Banco de Dados (Completo)</h1>";

try {
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    echo "<p>Conectado ao banco: <strong>$driver</strong></p>";

    if ($driver === 'pgsql') {
        echo "<h2>PostgreSQL Detectado - Verificando Todas as Sequências...</h2>";

        // Lista de tabelas para verificar
        $tables = [
            'pedidos',
            'order_events',
            'pedido_itens',
            'carrinho',
            'carrinho_itens',
            'enderecos',
            'avaliacoes',
            'usuarios',
            'produtos',
            'cupons'
        ];

        // Consulta para listar todas as sequências do banco
        $stmtSeq = $pdo->query("SELECT c.relname FROM pg_class c WHERE c.relkind = 'S'");
        $allSequences = $stmtSeq->fetchAll(PDO::FETCH_COLUMN);

        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li><strong>Tabela: $table</strong>";

            // Tenta adivinhar o nome da sequência
            $possibleNames = ["{$table}_id_seq", "{$table}_seq"];
            $seqName = null;

            foreach ($possibleNames as $name) {
                if (in_array($name, $allSequences)) {
                    $seqName = $name;
                    break;
                }
            }

            if ($seqName) {
                echo " -> Sequência encontrada: <span style='color:blue'>$seqName</span>";

                // Get Max ID
                $stmtMax = $pdo->query("SELECT MAX(id) FROM $table");
                $maxId = $stmtMax->fetchColumn();
                $actualMax = $maxId ? $maxId : 0;

                echo " -> Max ID: <strong>$actualMax</strong>";

                // Fix Sequence
                // Se max=0, next=1. Se max=10, next=11.
                if ($actualMax > 0) {
                    $pdo->query("SELECT setval('$seqName', $actualMax, true)");
                    echo " -> <span style='color:green'>Sincronizado (Próximo: " . ($actualMax + 1) . ")</span>";
                } else {
                    $pdo->query("SELECT setval('$seqName', 1, false)");
                    echo " -> <span style='color:green'>Resetado para 1</span>";
                }
            } else {
                echo " -> <span style='color:orange'>Sequência não encontrada automaticamente (pode não usar auto-increment ou ter nome diferente)</span>";
            }
            echo "</li>";
        }
        echo "</ul>";

        echo "<h2>Concluído! Tente realizar a compra novamente.</h2>";
    } else {
        echo "<p>Este script é específico para correções em PostgreSQL. Seu driver é $driver.</p>";
    }
} catch (Exception $e) {
    echo "<h3 style='color:red'>ERRO FATAL: " . $e->getMessage() . "</h3>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
