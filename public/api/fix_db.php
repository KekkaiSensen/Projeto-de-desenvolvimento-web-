<?php
define('API_MODE', true);
require __DIR__ . '/../../Banco de dados/conexao.php';

echo "<h1>Diagnóstico de Sequências (Debug Mode)</h1>";

try {
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    echo "<p>Conectado ao banco: <strong>$driver</strong></p>";

    if ($driver === 'pgsql') {

        // 1. Listar TODAS as sequências para eu ver o nome correto
        $stmtSeq = $pdo->query("SELECT c.relname FROM pg_class c WHERE c.relkind = 'S' ORDER BY c.relname");
        $allSequences = $stmtSeq->fetchAll(PDO::FETCH_COLUMN);

        echo "<h3>Listagem de TODAS as sequências no banco:</h3>";
        echo "<ul>";
        foreach ($allSequences as $seq) {
            echo "<li>$seq</li>";
        }
        echo "</ul>";
        echo "<hr>";

        // 2. Tentar corrigir (Lógica Melhorada)
        $tablesMap = [
            'pedidos' => ['pedidos_id_seq', 'pedidos_seq'],
            'order_events' => ['order_events_id_seq', 'order_events_seq'],
            'pedido_itens' => ['pedido_itens_id_seq', 'pedido_itens_seq', 'pedidos_itens_id_seq', 'pedidos_itens_seq'], // Tentando plural
            'carrinho' => ['carrinho_id_seq'],
            'carrinho_itens' => ['carrinho_itens_id_seq', 'carrinho_itens_seq'],
            'enderecos' => ['enderecos_id_seq', 'enderecos_seq'],
            'avaliacoes' => ['avaliacoes_id_seq'],
            'usuarios' => ['usuarios_id_seq'],
            'produtos' => ['produtos_id_seq'],
            'cupons' => ['cupons_id_seq']
        ];

        echo "<h3>Tentativa de Correção:</h3>";

        foreach ($tablesMap as $table => $possibleNames) {
            echo "<p><strong>Tabela: $table</strong>";

            $seqName = null;
            // Tenta achar nas possibilidades explicitas
            foreach ($possibleNames as $name) {
                if (in_array($name, $allSequences)) {
                    $seqName = $name;
                    break;
                }
            }

            // Se não achou, tenta 'match fuzzy' (contém o nome da tabela)
            if (!$seqName) {
                foreach ($allSequences as $s) {
                    if (strpos($s, $table) !== false) {
                        $seqName = $s; // Pega o primeiro que parece ser dessa tabela
                        echo " (fuzzy match: $s) ";
                        break;
                    }
                }
            }

            if ($seqName) {
                echo " -> Sequência: <span style='color:blue'>$seqName</span>";

                // Get Max ID
                $stmtMax = $pdo->query("SELECT MAX(id) FROM $table");
                $maxId = $stmtMax->fetchColumn();
                $actualMax = $maxId ? $maxId : 0;

                echo " -> Max ID: <strong>$actualMax</strong>";

                // Fix Sequence
                $pdo->query("SELECT setval('$seqName', " . ($actualMax > 0 ? $actualMax : 1) . ", " . ($actualMax > 0 ? 'true' : 'false') . ")");
                echo " -> <span style='color:green'>OK</span>";
            } else {
                echo " -> <span style='color:red'>Sequência NÃO encontrada (Verifique lista acima)</span>";
            }
            echo "</p>";
        }
    } else {
        echo "<p>Não é PostgreSQL ($driver).</p>";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
