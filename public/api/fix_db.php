<?php
define('API_MODE', true);
require __DIR__ . '/../../Banco de dados/conexao.php';

echo "<h1>Reparo de Schema do Banco de Dados (Notificações)</h1>";

try {
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    echo "<p>Driver: <strong>$driver</strong></p>";

    if ($driver === 'pgsql') {

        $queries = [
            'notificacoes' => "
                CREATE TABLE IF NOT EXISTS notificacoes (
                  id SERIAL PRIMARY KEY,
                  usuario_id INTEGER NOT NULL,
                  mensagem TEXT,
                  lida SMALLINT DEFAULT 0,
                  tipo VARCHAR(50) DEFAULT 'primary', 
                  link VARCHAR(255),
                  data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                );
            "
        ];

        echo "<h2>Verificando tabelas adicionais...</h2>";

        foreach ($queries as $table => $sql) {
            echo "<strong>Verificando tabela '$table'...</strong> ";
            try {
                $pdo->exec($sql);
                echo "<span style='color:green'>Sucesso (ou já existia)!</span><br>";

                $seqName = "{$table}_id_seq";
                $maxId = $pdo->query("SELECT MAX(id) FROM $table")->fetchColumn();
                $nextVal = ($maxId) ? $maxId + 1 : 1;

                echo "Sincronizando sequência $seqName para $nextVal... ";

                $checkSeq = $pdo->query("SELECT 1 FROM pg_class WHERE relname = '$seqName'")->fetch();
                if ($checkSeq) {
                    $pdo->query("SELECT setval('$seqName', $nextVal, false)");
                    echo "<span style='color:green'>Sincronizado!</span><br>";
                } else {
                    echo "<span style='color:orange'>Sequência não encontrada.</span><br>";
                }
            } catch (Exception $e) {
                echo "<br><span style='color:red'>Erro ao processar $table: " . $e->getMessage() . "</span><br>";
            }
            echo "<hr>";
        }

        echo "<h2>Concluído. Tente comprar novamente.</h2>";
    } else {
        echo "<p>Este script é apenas para PostgreSQL.</p>";
    }
} catch (Exception $e) {
    echo "<h1>Erro Fatal: " . $e->getMessage() . "</h1>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
