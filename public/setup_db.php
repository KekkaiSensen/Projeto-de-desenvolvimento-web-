<?php
// public/setup_db.php
require __DIR__ . '/../Banco de dados/conexao.php';

set_time_limit(300);

echo "<h1>Setup Inicial do Banco de Dados (v6 - Clean & Import)</h1>";

$sqlFile = __DIR__ . '/../Banco de dados/bancodadosteste.sql';

if (!file_exists($sqlFile)) {
    die("Arquivo SQL não encontrado em: $sqlFile");
}

// 1. Limpeza Inicial (DROP TABLES)
echo "<h3>1. Limpando tabelas antigas...</h3>";
$tablesToDrop = [
    'avaliacoes',
    'carrinho_itens',
    'carrinho',
    'conversas',
    'cupom_uso',
    'cupons',
    'enderecos',
    'entregas',
    'mensagens',
    'notificacoes',
    'order_events',
    'order_issues',
    'pagamentos',
    'pedido_itens',
    'pedidos',
    'produto_imagens',
    'produtos',
    'categorias',
    'usuarios'
];
// Ordem inversa de dependência (child -> parent) para evitar erro de FK, 
// ou usar CASCADE.
foreach ($tablesToDrop as $table) {
    try {
        $pdo->exec("DROP TABLE IF EXISTS \"$table\" CASCADE");
        // echo "Drop $table OK<br>";
    } catch (PDOException $e) {
        echo "Erro ao dropar $table: " . $e->getMessage() . "<br>";
    }
}

echo "<h3>2. Importando dados...</h3>";
echo "Lendo arquivo SQL...<br>";

function cleanQuery($query)
{
    // Remove ENGINE, CHARSET, LOCKS, ETC (já filtrado no loop, mas reforçando)

    // 1. Tipos de Dados
    // Remove comprimentos de inteiros: int(11) -> int, tinyint(1) -> tinyint
    $query = preg_replace('/\b(tinyint|smallint|mediumint|int|integer|bigint)\s*\(\s*\d+\s*\)/i', '$1', $query);

    // Mapeia tipos MySQL -> Postgres
    $query = preg_replace('/\btinyint\b/i', 'SMALLINT', $query);
    $query = preg_replace('/\bmediumint\b/i', 'INTEGER', $query);
    $query = preg_replace('/\bdatetime\b/i', 'TIMESTAMP', $query);

    // ENUM -> VARCHAR (Postgres nao tem ENUM inline simplificado)
    $query = preg_replace('/\benum\s*\(.*?\)/i', 'VARCHAR(50)', $query);

    // 2. Definições de Tabela
    $query = preg_replace('/ENGINE=[a-zA-Z0-9_]+.*?;/i', ';', $query);
    $query = preg_replace('/DEFAULT CHARSET=[a-zA-Z0-9_]+.*?;/i', ';', $query);
    $query = preg_replace('/ON UPDATE CURRENT_TIMESTAMP/i', '', $query);

    // 3. Aspas
    $query = str_replace('`', '"', $query);
    $query = str_replace("\\'", "''", $query); // Escape de single quote

    // 4. PK e Auto Increment (CREATE TABLE)
    if (stripos($query, 'CREATE TABLE') === 0) {
        // id int NOT NULL -> id SERIAL PRIMARY KEY
        $query = preg_replace('/"id"\s+(INTEGER|int|SMALLINT)\s+NOT\s+NULL/i', '"id" SERIAL PRIMARY KEY', $query);

        // Remove definition posterior de PK duplicada
        // Ex: PRIMARY KEY ("id") no final da create string
        $query = preg_replace('/,\s*PRIMARY KEY\s*\("id"\)/i', '', $query);
    }

    // 5. ALTER TABLE adjustments
    if (stripos($query, 'ALTER TABLE') === 0) {
        // Remove ADD PRIMARY KEY (já adicionado no Create)
        $query = preg_replace('/ADD PRIMARY KEY\s*\("id"\),?/i', '', $query);

        // Remove ADD KEY (indices comuns que dão erro de syntax as vezes ou não são críticos)
        $query = preg_replace('/ADD KEY\s+"[^"]+"\s*\([^)]+\),?/i', '', $query);

        // Corrige ADD UNIQUE KEY -> ADD UNIQUE
        $query = preg_replace('/ADD UNIQUE KEY\s+"[^"]+"\s*\(/i', 'ADD CONSTRAINT "$1" UNIQUE (', $query); // Tenta preservar nome? dificil regex.
        // Simplificado: ADD UNIQUE KEY "nome" (col) -> ADD UNIQUE (col)
        // Mas o Postgres precisa de nomes para constraints se quisermos ser chiques, mas inline UNIQUE(col) funciona.
        // Vamos usar substituição bruta:
        $query = str_ireplace('ADD UNIQUE KEY', 'ADD UNIQUE', $query);

        // Limpeza de virgulas orfans
        $query = preg_replace('/ALTER TABLE\s+"[^"]+"\s+,/', 'ALTER TABLE ', $query);
        $query = preg_replace('/,\s*;/', ';', $query);
        $query = preg_replace('/,\s*,/', ',', $query); // ,, -> ,
    }

    return $query;
}

$handle = fopen($sqlFile, "r");
if ($handle) {
    echo "<ul>";
    $queryBuffer = '';

    while (($line = fgets($handle)) !== false) {
        $trimmedLine = trim($line);
        if (empty($trimmedLine) || strpos($trimmedLine, '--') === 0 || strpos($trimmedLine, '/*') === 0) {
            if (empty($queryBuffer)) continue;
        }

        $queryBuffer .= $line;

        if (substr(rtrim($trimmedLine), -1) === ';') {
            $query = trim($queryBuffer);
            $queryBuffer = '';

            if (empty($query)) continue;

            // Ignora comandos inuteis
            if (
                stripos($query, 'LOCK TABLES') === 0 || stripos($query, 'UNLOCK TABLES') === 0 ||
                stripos($query, 'SET SQL_MODE') === 0 || stripos($query, 'START TRANSACTION') === 0 ||
                stripos($query, 'SET time_zone') === 0 || stripos($query, 'SET NAMES') === 0 ||
                stripos($query, 'COMMIT') === 0
            ) {
                continue;
            }

            // Remove ALTER TABLE MODIFY AUTO_INCREMENT
            if (stripos($query, 'ALTER TABLE') === 0 && stripos($query, 'MODIFY') !== false && stripos($query, 'AUTO_INCREMENT') !== false) {
                continue;
            }

            $query = cleanQuery($query);

            // Verifica se a query ficou vazia ou inválida (só "ALTER TABLE table ;")
            if (stripos($query, 'ALTER TABLE') === 0) {
                // Verifica se tem 'ADD', 'DROP', 'ALTER', 'CONSTRAINT'
                if (!preg_match('/(ADD|DROP|ALTER|CONSTRAINT)/i', $query)) {
                    continue;
                }
            }

            try {
                $pdo->exec($query);

                if (stripos($query, 'CREATE TABLE') === 0) {
                    echo "<li style='color:blue'>Tabela criada: " . substr($query, 13, 30) . "...</li>";
                } elseif (stripos($query, 'INSERT INTO') === 0) {
                    // echo "."; 
                } else {
                    echo "<li style='color:green'>Executado: " . substr($query, 0, 60) . "...</li>";
                }
            } catch (PDOException $e) {
                $msg = $e->getMessage();
                // Ignora erros "irrelevantes" para o MVP
                echo "<li style='color:red'>Erro: " . $msg . "<br><small>" . htmlspecialchars(substr($query, 0, 150)) . "</small></li>";
            }
        }
    }
    fclose($handle);
}

echo "</ul>";
echo "<h2>Status Final</h2>";
try {
    $tables = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tabelas (" . count($tables) . "): " . implode(", ", $tables) . "<br>";

    if (in_array('produtos', $tables)) {
        $count = $pdo->query("SELECT COUNT(*) FROM produtos")->fetchColumn();
        echo "Produtos cadastrados: <strong>$count</strong>";
    }
} catch (Exception $e) {
    echo "Erro verificação: " . $e->getMessage();
}
echo "<h2>Fim</h2>";
