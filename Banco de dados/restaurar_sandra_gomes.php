<?php
// Script para restaurar a conta de Sandra Gomes Fictícia

require __DIR__ . '/conexao.php';

try {
    echo "Iniciando restauração da conta de Sandra Gomes...\n";

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
        echo "⚠️ AVISO: Já existe uma conta com ID 6, email ou CPF de Sandra Gomes!\n";
        echo "ID encontrado: " . $conta_existente['id'] . "\n";
        echo "Atualizando conta automaticamente...\n\n";

        // Atualiza a conta existente
        $stmt = $pdo->prepare("
            UPDATE usuarios 
            SET nome = ?, email = ?, cpf = ?, senha = ?, telefone = ?, tipo = ?, data_cadastro = ?
            WHERE id = ?
        ");
        $stmt->execute([$nome, $email, $cpf, $senha_hash, $telefone, $tipo, $data_cadastro, $conta_existente['id']]);
        echo "✅ Conta atualizada com sucesso!\n";
    } else {
        // Insere nova conta com ID específico
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

        $stmt = $pdo->prepare("
            INSERT INTO usuarios (id, nome, email, cpf, senha, telefone, data_cadastro, tipo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$id, $nome, $email, $cpf, $senha_hash, $telefone, $data_cadastro, $tipo]);

        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        echo "✅ Conta criada com sucesso!\n";
    }

    echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📋 DADOS DE LOGIN:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Email:  sandra.gomes@LojaLTDA.com\n";
    echo "Senha:  senha123\n";
    echo "Tipo:   Fornecedor\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "\n✨ Conta de Sandra Gomes restaurada!\n";
    echo "Todos os produtos cadastrados com usuario_id = 6 agora estão vinculados a esta conta.\n";
} catch (Exception $e) {
    echo "❌ Erro ao restaurar conta: " . $e->getMessage() . "\n";
    exit(1);
}
