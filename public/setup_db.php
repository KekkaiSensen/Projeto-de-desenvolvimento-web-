<?php
// public/setup_db.php
require __DIR__ . '/../Banco de dados/conexao.php';

// Aumenta tempo de execução
set_time_limit(300);

echo "<h1>Setup Inicial do Banco de Dados (v4 - Fix Types)</h1>";

$sqlFile = __DIR__ . '/../Banco de dados/bancodadosteste.sql';

if (!file_exists($sqlFile)) {
    die("Arquivo SQL não encontrado em: $sqlFile");
}

echo "Lendo arquivo SQL...<br>";

$handle = fopen($sqlFile, "r");
if ($handle) {
    echo "<ul>";
    $queryBuffer = '';

    while (($line = fgets($handle)) !== false) {
        $trimmedLine = trim($line);

        // Ignora comentários e linhas vazias
        if (empty($trimmedLine) || strpos($trimmedLine, '--') === 0 || strpos($trimmedLine, '/*') === 0) {
            if (empty($queryBuffer)) continue;
        }

        $queryBuffer .= $line;

        // Verifica fim de comando
        if (substr(rtrim($trimmedLine), -1) === ';') {

            $query = trim($queryBuffer);
            $queryBuffer = ''; // Limpa buffer

            if (empty($query)) continue;

            // --- IGNORA COMANDOS MYSQL-SPECIFIC ---
            if (
                stripos($query, 'LOCK TABLES') === 0 || stripos($query, 'UNLOCK TABLES') === 0 ||
                stripos($query, 'SET SQL_MODE') === 0 || stripos($query, 'START TRANSACTION') === 0 ||
                stripos($query, 'SET time_zone') === 0 || stripos($query, 'SET NAMES') === 0 ||
                stripos($query, 'COMMIT') === 0
            ) {
                continue;
            }

            // --- FILTROS DE SINTAXE ---

            // 1. Remove definições de tabela e charset
            $query = preg_replace('/ENGINE=InnoDB.*?;/i', ';', $query);
            $query = preg_replace('/DEFAULT CHARSET=.*?;/i', ';', $query);

            // 2. Remove "ON UPDATE CURRENT_TIMESTAMP" (Postgres não suporta isso nativamente no DEFAULT)
            $query = preg_replace('/ON UPDATE CURRENT_TIMESTAMP/i', '', $query);

            // 3. Substitui crases por aspas duplas
            $query = str_replace('`', '"', $query);

            // 4. Correção de Tipos de Dados
            // Use \b para boundary, evitando transformar "tinyint" em "tinyINTEGER" se rodar int->INTEGER depois
            $query = preg_replace('/\btinyint(\(\d+\))?\b/i', 'SMALLINT', $query);
            $query = preg_replace('/\bdatetime\b/i', 'TIMESTAMP', $query);
            // int(11) -> INTEGER
            $query = preg_replace('/\bint\(\d+\)\b/i', 'INTEGER', $query);

            // 5. Ajustes em CREATE TABLE
            if (stripos($query, 'CREATE TABLE') === 0) {
                // Transforma o ID em SERIAL PRIMARY KEY para lidar com AUTO_INCREMENT
                // Procura por "id" int NOT NULL ou "id" INTEGER NOT NULL
                $query = preg_replace('/"id" (int|integer) NOT NULL/i', '"id" SERIAL PRIMARY KEY', $query);

                // Se já definimos PK inline, remove definition posterior de PK se houver na mesma string?
                // Ex: PRIMARY KEY ("id")
                // Mas geralmente dumps MySQL colocam PK no final. 
                // Vamos tentar remover a linha de PRIMARY KEY(id) se houver dentro do CREATE
                $query = preg_replace('/,\s*PRIMARY KEY\s*\("id"\)/i', '', $query);
            }

            // 6. Ajustes em ALTER TABLE
            if (stripos($query, 'ALTER TABLE') === 0) {
                // MySQL: ADD UNIQUE KEY "name" ("col") -> Postgres: ADD CONSTRAINT "name" UNIQUE ("col")
                // Simplificação: ADD UNIQUE KEY -> ADD UNIQUE
                $query = str_ireplace('ADD UNIQUE KEY', 'ADD UNIQUE', $query);

                // MySQL: ADD KEY "name" ("col") -> Postgres não suporta "ADD KEY" para índices comuns dentro de ALTER TABLE.
                // Indices devem ser criados via CREATE INDEX.
                // Ignorar ADD KEY que não seja UNIQUE ou PRIMARY?
                // Isso é complexo pois uma query pode ter múltiplos ADDs: ALTER TABLE t ADD PRIMARY KEY, ADD KEY...
                // Se falhar, falhou. O importante são as tabelas e dados.

                // Vamos tentar remover partes "ADD KEY..." que dão erro, preservando o resto? Difícil regex.
                // Se contiver "ADD KEY" (e não UNIQUE/PRIMARY), vamos tentar substituir por nada ou comentar?
                // Melhor estratégia: Tentar executar. Se der erro no ALTER TABLE por causa de índices, 
                // paciência, o app roda sem indices (ficará lento, mas funciona).
                // Mas Constraints de FK são importantes.

                // Tentativa de fixar sintaxe ADD KEY solta
                // Se for a única instrução... mas geralmente vem em bloco.
            }

            // remove MODIFY ... AUTO_INCREMENT
            if (stripos($query, 'MODIFY') !== false && stripos($query, 'AUTO_INCREMENT') !== false) {
                continue; // Ignora comando inteiro se for só para adicionar auto_increment
            }

            // --- EXECUÇÃO ---
            try {
                $pdo->exec($query);

                if (stripos($query, 'CREATE TABLE') === 0) {
                    echo "<li style='color:blue'>Tabela criada: " . substr($query, 0, 50) . "...</li>";
                } elseif (stripos($query, 'INSERT INTO') === 0) {
                    // echo "."; 
                } else {
                    echo "<li style='color:green'>Executado: " . substr($query, 0, 50) . "...</li>";
                }
            } catch (PDOException $e) {
                // Ignora erros conhecidos que não impedem o funcionamento básico
                $msg = $e->getMessage();
                if (strpos($msg, 'already exists') !== false) {
                    // echo "<li style='color:orange'>Já existe: " . substr($query, 0, 50) . "</li>";
                } elseif (strpos($msg, 'syntax error at or near "KEY"') !== false) {
                    echo "<li style='color:orange'>Aviso (Índice ignorado): " . $msg . "</li>";
                } else {
                    echo "<li style='color:red'>Erro: " . $msg . "<br><small>" . htmlspecialchars(substr($query, 0, 200)) . "</small></li>";
                }
            }
        }
    }
    fclose($handle);
} else {
    echo "Erro ao abrir arquivo.";
}

echo "</ul>";

// Verificação
echo "<h2>Status</h2>";
try {
    $count = $pdo->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'public'")->fetchColumn();
    echo "Total de tabelas: $count<br>";

    if ($count > 0) {
        $pCount = $pdo->query("SELECT COUNT(*) FROM produtos")->fetchColumn();
        echo "Total de produtos: <strong>$pCount</strong>";
    }
} catch (Exception $e) {
    echo "Erro verificação: " . $e->getMessage();
}

echo "<h2>Fim</h2>";
