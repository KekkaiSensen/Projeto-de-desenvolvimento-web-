<?php
define('API_MODE', true);
require __DIR__ . '/../../Banco de dados/conexao.php';

echo "<h1>Reparo de Schema do Banco de Dados (Tabelas Faltantes)</h1>";

try {
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    echo "<p>Driver: <strong>$driver</strong></p>";

    if ($driver === 'pgsql') {

        // SQL para criar tabelas se não existirem (Sintaxe PostgreSQL)
        $queries = [
            'cupons' => "
                CREATE TABLE IF NOT EXISTS cupons (
                  id SERIAL PRIMARY KEY,
                  codigo VARCHAR(50) NOT NULL,
                  descricao VARCHAR(255),
                  tipo_desconto VARCHAR(50) NOT NULL, 
                  valor_desconto DECIMAL(10,2) NOT NULL,
                  valor_minimo DECIMAL(10,2) DEFAULT 0.00,
                  data_inicio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                  data_fim TIMESTAMP,
                  limite_uso INTEGER,
                  ativo SMALLINT DEFAULT 1,
                  usuario_id INTEGER,
                  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                );
            ",
            'cupom_uso' => "
                CREATE TABLE IF NOT EXISTS cupom_uso (
                  id SERIAL PRIMARY KEY,
                  cupom_id INTEGER NOT NULL,
                  usuario_id INTEGER NOT NULL,
                  pedido_id INTEGER,
                  data_uso TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                );
            "
        ];

        echo "<h2>Criando tabelas faltantes...</h2>";

        foreach ($queries as $table => $sql) {
            echo "<strong>Verificando tabela '$table'...</strong> ";
            try {
                // Tenta criar
                $pdo->exec($sql);
                echo "<span style='color:green'>Sucesso (ou já existia)!</span><br>";

                // Se a tabela foi recém criada, precisamos garantir as sequências
                // O SERIAL já cria a sequência implicitamente em PG, mas podemos sincronizar pra garantir

                $seqName = "{$table}_id_seq";
                // Sincronizar valor
                $maxId = $pdo->query("SELECT MAX(id) FROM $table")->fetchColumn();
                $nextVal = ($maxId) ? $maxId + 1 : 1;

                echo "Sincronizando sequência $seqName para $nextVal... ";

                // Verifica se sequencia existe (SERIAL cria automatico, mas vai que...)
                $checkSeq = $pdo->query("SELECT 1 FROM pg_class WHERE relname = '$seqName'")->fetch();
                if ($checkSeq) {
                    $pdo->query("SELECT setval('$seqName', $nextVal, false)");
                    echo "<span style='color:green'>Sincronizado!</span><br>";
                } else {
                    echo "<span style='color:orange'>Sequência automática não encontrada (talvez criada com outro nome?).</span><br>";
                }
            } catch (Exception $e) {
                echo "<br><span style='color:red'>Erro ao criar $table: " . $e->getMessage() . "</span><br>";
            }
            echo "<hr>";
        }

        // Re-executar a correção de sequências para as outras tabelas também, só por garantia
        echo "<h2>Verificando sequências das outras tabelas...</h2>";
        $tablesMap = [
            'pedido_itens' => 'pedido_itens_id_seq',
            'carrinho_itens' => 'carrinho_itens_id_seq',
            'enderecos' => 'enderecos_id_seq',
            'produtos' => 'produtos_id_seq'
        ];

        foreach ($tablesMap as $table => $seqName) {
            try {
                // Cria se não existir (para tabelas que não usamos SERIAL no script anterior)
                // Como o script anterior já rodou, elas devem existir.
                // Apenas sincronizamos.
                $maxId = $pdo->query("SELECT MAX(id) FROM $table")->fetchColumn();
                $nextVal = ($maxId) ? $maxId + 1 : 1;
                $pdo->query("SELECT setval('$seqName', $nextVal, false)");
                echo "$table: <span style='color:green'>OK ($nextVal)</span><br>";
            } catch (Exception $e) {
                // Ignora erro aqui
                echo "$table: " . $e->getMessage() . "<br>";
            }
        }


        echo "<h2>Concluído. Tente comprar novamente.</h2>";
    } else {
        echo "<p>Este script é apenas para PostgreSQL.</p>";
    }
} catch (Exception $e) {
    echo "<h1>Erro Fatal: " . $e->getMessage() . "</h1>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
