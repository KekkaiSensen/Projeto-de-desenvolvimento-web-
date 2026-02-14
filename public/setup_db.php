<?php
// public/setup_db.php
require __DIR__ . '/../Banco de dados/conexao.php';

// Aumenta tempo de execução para importação
set_time_limit(300);

echo "<h1>Setup Inicial do Banco de Dados (v3 - Stream Parser)</h1>";

$sqlFile = __DIR__ . '/../Banco de dados/bancodadosteste.sql';

if (!file_exists($sqlFile)) {
    die("Arquivo SQL não encontrado em: $sqlFile");
}

echo "Lendo arquivo SQL...<br>";

// TENTATIVA: Leitura linha a linha para montar as queries
// Isso evita problemas de memória com regex em arquivos grandes e é mais seguro.
$handle = fopen($sqlFile, "r");
if ($handle) {
    echo "<ul>";
    $queryBuffer = '';

    // Processamento simplificado:
    // O dump do phpMyAdmin geralmente coloca cada comando INSERT em uma linha (ou poucas).
    // Mas CREATE TABLE pode usar várias.
    // Vamos acumular até encontrar ";\n" ou ";" no final da linha.

    while (($line = fgets($handle)) !== false) {
        $trimmedLine = trim($line);

        // Ignora comentários de linha inteira e linhas vazias (se buffer vazio)
        if (empty($trimmedLine) || strpos($trimmedLine, '--') === 0 || strpos($trimmedLine, '/*') === 0) {
            if (empty($queryBuffer)) continue;
        }

        $queryBuffer .= $line;

        // Verifica se a linha termina com ; (ignorando espaços)
        // Isso assume que o dump formata bem os comandos (phpMyAdmin geralmente faz isso)
        if (substr(rtrim($trimmedLine), -1) === ';') {

            // Query completa encontrada
            $query = trim($queryBuffer);
            $queryBuffer = ''; // Limpa buffer

            // === FILTROS E ADAPTAÇÕES ===
            if (empty($query)) continue;

            // Ignora LOCK/UNLOCK/COMMIT/SET/START
            if (
                stripos($query, 'LOCK TABLES') === 0 || stripos($query, 'UNLOCK TABLES') === 0 ||
                stripos($query, 'SET SQL_MODE') === 0 || stripos($query, 'START TRANSACTION') === 0 ||
                stripos($query, 'SET time_zone') === 0 || stripos($query, 'SET NAMES') === 0 ||
                stripos($query, 'COMMIT') === 0
            ) {
                continue;
            }

            // Remove ENGINE=InnoDB...
            $query = preg_replace('/ENGINE=InnoDB.*?;/i', ';', $query);
            $query = preg_replace('/DEFAULT CHARSET=.*?;/i', ';', $query);

            // Adaptação de aspas para Identificadores
            // CUIDADO: Substituir todas as crases pode quebrar se houver crases dentro de strings.
            // Mas em Dumps SQL, crases são usadas para delimitadores.
            $query = str_replace('`', '"', $query);

            // Adaptação AUTO_INCREMENT
            if (stripos($query, 'CREATE TABLE') === 0) {
                $query = preg_replace('/int\(\d+\) NOT NULL AUTO_INCREMENT/i', 'SERIAL PRIMARY KEY', $query);
                $query = preg_replace('/int NOT NULL AUTO_INCREMENT/i', 'SERIAL PRIMARY KEY', $query);
                // Substitui int(11) por INTEGER
                $query = preg_replace('/int\(\d+\)/i', 'INTEGER', $query);
                $query = preg_replace('/tinyint\(\d+\)/i', 'SMALLINT', $query);
            }

            // Remove ALTER TABLE ... AUTO_INCREMENT
            if (stripos($query, 'ALTER TABLE') === 0 && stripos($query, 'AUTO_INCREMENT') !== false) {
                continue;
            }

            try {
                $pdo->exec($query);
                // Feedback resumido
                if (stripos($query, 'CREATE TABLE') === 0) {
                    echo "<li style='color:blue'>Tabela criada: " . substr($query, 0, 50) . "...</li>";
                } elseif (stripos($query, 'INSERT INTO') === 0) {
                    // echo "."; // Feedback visual mínimo para inserts
                } else {
                    echo "<li style='color:green'>Executado: " . substr($query, 0, 50) . "...</li>";
                }
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'already exists') !== false) {
                    // echo "x";
                } else {
                    echo "<li style='color:red'>Erro no comando: <br>" . htmlspecialchars(substr($query, 0, 300)) . "<br>Msg: " . $e->getMessage() . "</li>";
                }
            }
        }
    }
    fclose($handle);
} else {
    echo "Erro ao abrir arquivo.";
}

echo "</ul>";

// Verifica contagem final
echo "<h2>Verificação Rápida</h2>";
try {
    $tables = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tabelas encontradas: " . implode(", ", $tables) . "<br>";
    if (in_array('produtos', $tables)) {
        $count = $pdo->query("SELECT COUNT(*) FROM produtos")->fetchColumn();
        echo "Produtos cadastrados: <strong>$count</strong>";
    }
} catch (Exception $e) {
    echo "Erro na verificação: " . $e->getMessage();
}

echo "<h2>Concluído!</h2>";
