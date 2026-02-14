<?php
// public/setup_db.php
require __DIR__ . '/../Banco de dados/conexao.php';

set_time_limit(300);

echo "<h1>Setup Inicial do Banco de Dados (v5 - Final Fix)</h1>";

$sqlFile = __DIR__ . '/../Banco de dados/bancodadosteste.sql';

if (!file_exists($sqlFile)) {
    die("Arquivo SQL não encontrado em: $sqlFile");
}

echo "Lendo arquivo SQL...<br>";

// Função helper para limpar queries
function cleanQuery($query)
{
    // 1. Remove ENGINE e CHARSET
    $query = preg_replace('/ENGINE=[a-zA-Z0-9_]+.*?;/i', ';', $query);
    $query = preg_replace('/DEFAULT CHARSET=[a-zA-Z0-9_]+.*?;/i', ';', $query);

    // 2. Remove ON UPDATE CURRENT_TIMESTAMP
    $query = preg_replace('/ON UPDATE CURRENT_TIMESTAMP/i', '', $query);

    // 3. Substitui crases por aspas duplas
    $query = str_replace('`', '"', $query);

    // 4. Corrige tipos de dados
    // tinyint(1) -> SMALLINT (sem parenteses!)
    $query = preg_replace('/\btinyint\(\d+\)/i', 'SMALLINT', $query);
    $query = preg_replace('/\btinyint\b/i', 'SMALLINT', $query);

    // int(11) -> INTEGER (sem parenteses!)
    $query = preg_replace('/\bint\(\d+\)/i', 'INTEGER', $query);
    // Mas cuidado para nao substituir "content" ou "print" (usamos \b)

    // datetime -> TIMESTAMP
    $query = preg_replace('/\bdatetime\b/i', 'TIMESTAMP', $query);

    // 5. Escaping de strings (MySQL \' -> Postgres '')
    // Apenas se houver ' no meio. Isso pode ser perigoso se fizermos replace global cegamente, 
    // mas em dumps SQL padrão textuais, \' é usado para escape de aspas simples.
    // Vamos substituir \' por '' (se não for fim de linha, etc, mas o replace simples costuma funcionar para dumps)
    $query = str_replace("\\'", "''", $query);

    // 6. Ajustes de PK e Auto Increment
    if (stripos($query, 'CREATE TABLE') === 0) {
        $query = preg_replace('/"id" (INTEGER|int) NOT NULL/i', '"id" SERIAL PRIMARY KEY', $query);
        // Remove definition posterior de PK duplicada se estiver no create
        $query = preg_replace('/,\s*PRIMARY KEY\s*\("id"\)/i', '', $query);
    }

    // 7. Ajustes de ALTER TABLE
    if (stripos($query, 'ALTER TABLE') === 0) {
        // Remove ADD KEY normais (índices não-unique, sintaxe MySQL)
        // Ex: ADD KEY "x" ("y")
        $query = preg_replace('/ADD KEY\s+"[^"]+"\s*\([^)]+\),?/i', '', $query);
        $query = preg_replace('/ADD KEY\s+"[^"]+"\s*\([^)]+\)/i', '', $query); // Caso seja o ultimo

        // Corrige ADD UNIQUE KEY -> ADD CONSTRAINT ... UNIQUE
        // Simplificado: ADD UNIQUE KEY "x" ("y") -> ADD UNIQUE ("y") ? Postgres aceita ADD UNIQUE ("col").
        $query = preg_replace('/ADD UNIQUE KEY\s+"[^"]+"\s*/i', 'ADD UNIQUE ', $query);

        // Limpa virgulas perdidas no final ou inicio de lista de alters
        $query = preg_replace('/ALTER TABLE\s+"[^"]+"\s+,/', 'ALTER TABLE ', $query); // virgula logo apos tabela?? Nao, logo apos nome da tabela vem as instrucoes.
        // Se removemos o primeiro item e sobrou ", ADD ...", precisamos limpar.
        // Mas regex é frágil. Vamos deixar falhar se for muito complexo.
    }

    // Remove virgulas extras deixadas por remoções
    // Ex: ALTER TABLE x , ADD ... -> ALTER TABLE x ADD ...
    $query = preg_replace('/(ALTER TABLE\s+"[^"]+"\s+),/', '$1', $query);
    // Ex: ... ADD CONSTRAINT x, ; -> ... ADD CONSTRAINT x;
    $query = preg_replace('/,\s*;/', ';', $query);

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

            // Remove linhas especificas de ALTER TABLE MODIFY (autoincrement)
            if (stripos($query, 'ALTER TABLE') === 0 && stripos($query, 'MODIFY') !== false && stripos($query, 'AUTO_INCREMENT') !== false) {
                continue;
            }

            $query = cleanQuery($query);

            // Verifica se sobrou algo util no ALTER TABLE
            if (stripos($query, 'ALTER TABLE') === 0) {
                $afterAlter = trim(substr($query, stripos($query, 'ALTER TABLE')));
                // Se só tem ALTER TABLE "tabela"; é inválido. precisa ter instruções.
                // Verifica se tem instrucoes (ADD, DROP, ALTER, etc)
            }

            try {
                $pdo->exec($query);

                if (stripos($query, 'CREATE TABLE') === 0) {
                    echo "<li style='color:blue'>Tabela criada: " . substr($query, 0, 50) . "...</li>";
                } elseif (stripos($query, 'INSERT INTO') === 0) {
                    // echo "."; 
                } else {
                    echo "<li style='color:green'>Executado: " . substr($query, 0, 100) . "...</li>";
                }
            } catch (PDOException $e) {
                // Filtra erros
                $msg = $e->getMessage();
                if (strpos($msg, 'already exists') !== false) {
                } elseif (strpos($msg, 'syntax error') !== false && stripos($query, 'ALTER TABLE') === 0 && stripos($query, 'ADD KEY') !== false) {
                    // Ignora erro de ADD KEY que escapou
                } else {
                    echo "<li style='color:red'>Erro: " . $msg . "<br><small>" . htmlspecialchars(substr($query, 0, 200)) . "</small></li>";
                }
            }
        }
    }
    fclose($handle);
}

echo "</ul>";
echo "<h2>Status Final</h2>";
try {
    $count = $pdo->query("SELECT COUNT(*) FROM produtos")->fetchColumn();
    echo "Produtos cadastrados: <strong>$count</strong>";
} catch (Exception $e) {
    echo "Erro ao verificar produtos: " . $e->getMessage();
}
echo "<h2>Fim</h2>";
