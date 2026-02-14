<?php
define('API_MODE', true);
require __DIR__ . '/../../Banco de dados/conexao.php';

echo "<h1>Reparo de Schema do Banco de Dados</h1>";

try {
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    echo "<p>Driver: <strong>$driver</strong></p>";

    if ($driver === 'pgsql') {

        $tablesWithoutSequence = [
            'pedido_itens',
            'carrinho_itens',
            'enderecos',
            'produtos',
            'cupons'
        ];

        echo "<h2>Tentando criar sequências faltantes...</h2>";

        foreach ($tablesWithoutSequence as $table) {
            echo "<div style='border:1px solid #ccc; padding:10px; margin-bottom:10px;'>";
            echo "<strong>Tabela: $table</strong><br>";

            $seqName = "{$table}_id_seq";

            try {
                // 1. Verificar se a sequência já existe
                $check = $pdo->query("SELECT 1 FROM pg_class WHERE relname = '$seqName'");
                if ($check->fetch()) {
                    echo "Sequência '$seqName' já existe.<br>";
                } else {
                    // 2. Criar a sequência
                    echo "Criando sequência '$seqName'... ";
                    $pdo->exec("CREATE SEQUENCE $seqName");
                    echo "<span style='color:green'>Sucesso!</span><br>";
                }

                // 3. Associar a sequência à coluna ID (DEFAULT nextval(...))
                echo "Associando sequência à coluna ID... ";
                // Nota: Usamos COALESCE para evitar erro se a tabela estiver vazia, mas MAX() retorna NULL se vazia.
                // Se max for null, começamos do 1.
                $pdo->exec("ALTER TABLE $table ALTER COLUMN id SET DEFAULT nextval('$seqName')");
                echo "<span style='color:green'>Associado!</span><br>";

                // 4. Sincronizar valor
                $maxId = $pdo->query("SELECT MAX(id) FROM $table")->fetchColumn();
                $nextVal = ($maxId) ? $maxId + 1 : 1;

                echo "Sincronizando valor para $nextVal... ";
                $pdo->query("SELECT setval('$seqName', $nextVal, false)"); // false = next value will be $nextVal
                echo "<span style='color:green'>Sincronizado!</span><br>";
            } catch (Exception $e) {
                echo "<br><span style='color:red'>Erro ao processar $table: " . $e->getMessage() . "</span>";
            }
            echo "</div>";
        }

        echo "<h2>Concluído. Tente comprar novamente.</h2>";
    } else {
        echo "<p>Este script é apenas para PostgreSQL.</p>";
    }
} catch (Exception $e) {
    echo "<h1>Erro Fatal: " . $e->getMessage() . "</h1>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
