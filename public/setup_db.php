<?php
// public/setup_db.php
require __DIR__ . '/../Banco de dados/conexao.php';

// Aumenta tempo de execução para importação
set_time_limit(300);

echo "<h1>Setup Inicial do Banco de Dados</h1>";

$sqlFile = __DIR__ . '/../Banco de dados/bancodadosteste.sql';

if (!file_exists($sqlFile)) {
    die("Arquivo SQL não encontrado em: $sqlFile");
}

echo "Lendo arquivo SQL...<br>";
$sqlContent = file_get_contents($sqlFile);

// === CONVERSÃO MYSQL PARA POSTGRESQL ===
echo "Convertendo sintaxe MySQL para PostgreSQL...<br>";

// 1. Remove SET SQL_MODE, START TRANSACTION, etc do início
$sqlContent = preg_replace('/SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";/', '', $sqlContent);
$sqlContent = preg_replace('/START TRANSACTION;/', '', $sqlContent);
$sqlContent = preg_replace('/SET time_zone = "\+00:00";/', '', $sqlContent);
$sqlContent = preg_replace('/SET NAMES utf8mb4;/', '', $sqlContent);

// 2. Remove comentários de versão condicional
$sqlContent = preg_replace('/\/\*!.*?\*\//s', '', $sqlContent);

// 3. Substitui crases (`) por aspas duplas (") para nomes de tabelas/colunas
$sqlContent = str_replace('`', '"', $sqlContent);

// 4. Converte AUTO_INCREMENT para SERIAL ou identidade
// Postgres usa SERIAL para inteiros auto-incrementáveis na criação da tabela.
// Mas o dump tem "int NOT NULL AUTO_INCREMENT".
// Vamos substituir "int NOT NULL AUTO_INCREMENT" por "SERIAL PRIMARY KEY" (se for PK)
// Mas o dump separa a definição da chave primária no final (PRIMARY KEY (`id`)).
// Estratégia simples:
// Trocar `int(11)` por `INTEGER`
$sqlContent = preg_replace('/int\(\d+\)/', 'INTEGER', $sqlContent);
// Trocar `tinyint(1)` por `BOOLEAN` ou `SMALLINT`
$sqlContent = preg_replace('/tinyint\(\d+\)/', 'SMALLINT', $sqlContent);
// Remover `ENGINE=InnoDB ...`
$sqlContent = preg_replace('/ENGINE=InnoDB.*?\;/', ';', $sqlContent);
// Remover `DEFAULT CHARSET=...` se sobrou
$sqlContent = preg_replace('/DEFAULT CHARSET=.*?;/', ';', $sqlContent);

// 5. Ajustes específicos de CREATE TABLE
// O dump tem "CREATE TABLE ... ( ... ) ENGINE=..."
// Precisa virar "CREATE TABLE ... ( ... );"
// Já removemos o ENGINE acima.

// 6. Ajustar aspas em strings (MySQL aceita aspas duplas para strings, Postgres não, só simples)
// O dump parece usar aspas simples para valores ('Valor'), o que é OK.
// Mas se tiver aspas escapadas (\'), Postgres prefere (''').
// O dump usa backslash escape? O padrão SQL é aspas duplicadas.
// Vamos assumir que o dump padrão do PHPMyAdmin usa aspas simples.

// 7. Lidando com chaves primárias e AUTO_INCREMENT separados
// MySQL dump:
//   `id` int NOT NULL,
//   ...
//   ALTER TABLE `tabela` ADD PRIMARY KEY (`id`);
//   ALTER TABLE `tabela` MODIFY `id` int NOT NULL AUTO_INCREMENT;
//
// Postgres não suporta MODIFY ... AUTO_INCREMENT.
// Precisamos criar SEQUENCEs e associar.
// OU, mais fácil para este script "quick fix":
// Criar as tabelas com SERIAL se a coluna for `id` e apagar os ALTER TABLE posteriores.

// TENTATIVA: Executar comando por comando e ignorar erros específicos ou adaptar.
// A divisão por ";" simples falha se houver ";" dentro de strings.
// Vamos usar um regex mais robusto para separar as queries.
// Regex explicado: Separa por ; seguido de fim de linha ou fim de string, tentando ignorar ; dentro de aspas simples.
// Nota: O dump do PHPMyAdmin usa aspas simples para valores.
$queries = preg_split("/;+(?=([^']*'[^']*')*[^']*$)/", $sqlContent);

echo "<ul>";
foreach ($queries as $query) {
    $query = trim($query);
    if (empty($query)) continue;

    // Ignora comentários
    if (strpos($query, '--') === 0 || strpos($query, '/*') === 0) continue;

    // Ignora LOCK TABLES / UNLOCK TABLES
    if (stripos($query, 'LOCK TABLES') === 0 || stripos($query, 'UNLOCK TABLES') === 0) continue;

    // --- ADAPTAÇÕES EM TEMPO DE EXECUÇÃO ---

    // Converte definições de coluna AUTO_INCREMENT (se houver na linha)
    $query = str_ireplace('AUTO_INCREMENT', '', $query); // Remove, pois Postgres usa Sequence

    // IMPORTANTE: Postgres não aceita "int NOT NULL" para autoincremento sem sequence.
    // Hack: Se a query for CREATE TABLE e tiver "id" int, transformar em SERIAL.
    if (stripos($query, 'CREATE TABLE') === 0) {
        $query = preg_replace('/"id" int NOT NULL/', '"id" SERIAL PRIMARY KEY', $query);
        // Precisamos remover a definição de PRIMARY KEY lá do final da query se já definimos aqui?
        // O dump do PHPMyAdmin geralmente coloca PK no create ou no alter?
        // No arquivo que vi: `id` int NOT NULL, ... PRIMARY KEY (`id`) não estava no create?
        // O arquivo diz:
        // CREATE TABLE `avaliacoes` ( `id` int NOT NULL, ... ) ENGINE=...
        // E depois:
        // ALTER TABLE `avaliacoes` ADD PRIMARY KEY (`id`);
        // ALTER TABLE `avaliacoes` MODIFY `id` int NOT NULL AUTO_INCREMENT;

        // Se mudarmos para SERIAL PRIMARY KEY no Create, o ALTER TABLE ADD PRIMARY KEY vai falhar (duplicado), o que é aceitável (podemos ignorar o erro).
    }

    // Ignore ALTER TABLE ... MODIFY ... AUTO_INCREMENT
    if (stripos($query, 'ALTER TABLE') === 0 && stripos($query, 'AUTO_INCREMENT') !== false) {
        echo "<li>Ignorando ajuste de Auto Increment (feito via SERIAL): <span style='color:gray'>" . substr($query, 0, 50) . "...</span></li>";
        continue;
    }

    // Ignore ALTER TABLE ... ADD PRIMARY KEY se já definimos no create
    // Mas vamos deixar rodar, se der erro capturamos.

    try {
        $pdo->exec($query);
        echo "<li style='color:green'>Sucesso: " . substr($query, 0, 100) . "...</li>";
    } catch (PDOException $e) {
        // Ignora erros de "tabela já existe" ou "primary key já existe"
        if (strpos($e->getMessage(), 'already exists') !== false) {
            echo "<li style='color:orange'>Aviso (já existe): " . substr($query, 0, 100) . "...</li>";
        } else {
            echo "<li style='color:red'>Erro: " . $e->getMessage() . "<br><small>" . htmlspecialchars(substr($query, 0, 200)) . "</small></li>";
        }
    }
}
echo "</ul>";

echo "<h2>Concluído!</h2>";
