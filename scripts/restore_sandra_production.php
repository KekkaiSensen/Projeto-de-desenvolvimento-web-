<?php

/**
 * Script Web para restaurar a conta de Sandra Gomes em PRODUÇÃO
 * 
 * ATENÇÃO DE SEGURANÇA:
 * - Este arquivo deve ser DELETADO após o uso
 * - Só funciona em ambientes de produção (não-localhost)
 * - Requer token de segurança na URL
 * 
 * USO: https://seu-app.onrender.com/scripts/restore_sandra_production.php?token=SUA_SENHA_AQUI
 */

// Token de segurança - MUDE ISSO ANTES DE FAZER DEPLOY!
define('SECURITY_TOKEN', 'sandra_restore_2025');

// Verifica se estamos em produção (não localhost)
$isLocalhost = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'])
    || strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false;

if ($isLocalhost) {
    die("❌ Este script só pode ser executado em PRODUÇÃO. Use o script local em 'Banco de dados/restaurar_sandra_gomes.php'");
}

// Verifica o token de segurança
if (!isset($_GET['token']) || $_GET['token'] !== SECURITY_TOKEN) {
    http_response_code(403);
    die("❌ Token de segurança inválido");
}

// Inicia output em HTML para melhor visualização
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Restaurar Conta Sandra Gomes</title>
    <style>
        body {
            font-family: monospace;
            padding: 20px;
            background: #1e1e1e;
            color: #d4d4d4;
        }

        .success {
            color: #4ade80;
        }

        .warning {
            color: #fbbf24;
        }

        .error {
            color: #f87171;
        }

        .info {
            color: #60a5fa;
        }

        pre {
            background: #2d2d2d;
            padding: 15px;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <pre>
<?php

require __DIR__ . '/../Banco de dados/conexao.php';

try {
    echo "🔄 <span class='info'>Iniciando restauração da conta de Sandra Gomes...</span>\n\n";

    // Dados da conta Sandra Gomes
    $id = 6;
    $nome = 'Sandra Gomes Fictícia';
    $email = 'sandra.gomes@LojaLTDA.com';
    $cpf = '707.808.909-00';
    $senha_hash = '$2y$10$87ZxH.N.bJtnM.2Od6txi.Vky0Rs7rzFyU/dV0xa3f.irbaDbymwe';
    $telefone = '31 96666-5555';
    $tipo = 'fornecedor';
    $data_cadastro = '2025-10-28 23:11:37';

    // Verifica se a conta já existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE id = ? OR email = ? OR cpf = ?");
    $stmt->execute([$id, $email, $cpf]);
    $conta_existente = $stmt->fetch();

    if ($conta_existente) {
        echo "<span class='warning'>⚠️ Conta já existe (ID: {$conta_existente['id']})</span>\n";
        echo "   Atualizando dados...\n\n";

        $stmt = $pdo->prepare("
            UPDATE usuarios 
            SET nome = ?, email = ?, cpf = ?, senha = ?, telefone = ?, tipo = ?, data_cadastro = ?
            WHERE id = ?
        ");
        $stmt->execute([$nome, $email, $cpf, $senha_hash, $telefone, $tipo, $data_cadastro, $conta_existente['id']]);
        echo "<span class='success'>✅ Conta atualizada com sucesso!</span>\n";
    } else {
        echo "   Criando nova conta...\n\n";

        // Desabilita checagem de chaves estrangeiras temporariamente
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

        $stmt = $pdo->prepare("
            INSERT INTO usuarios (id, nome, email, cpf, senha, telefone, data_cadastro, tipo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$id, $nome, $email, $cpf, $senha_hash, $telefone, $data_cadastro, $tipo]);

        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        echo "<span class='success'>✅ Conta criada com sucesso!</span>\n";
    }

    echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "<span class='info'>📋 DADOS DE LOGIN:</span>\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Email:  sandra.gomes@LojaLTDA.com\n";
    echo "Senha:  senha123\n";
    echo "Tipo:   Fornecedor\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    echo "<span class='success'>✨ Operação concluída com sucesso!</span>\n";
    echo "Todos os produtos com usuario_id = 6 agora estão vinculados.\n\n";

    echo "<span class='warning'>⚠️ IMPORTANTE DE SEGURANÇA:</span>\n";
    echo "Por favor, DELETE este arquivo IMEDIATAMENTE:\n";
    echo "   1. Remova 'scripts/restore_sandra_production.php'\n";
    echo "   2. Faça commit e push das mudanças\n\n";
} catch (Exception $e) {
    echo "<span class='error'>❌ Erro ao restaurar conta:</span>\n";
    echo "   " . $e->getMessage() . "\n";
    http_response_code(500);
}

?>
</pre>
</body>

</html>