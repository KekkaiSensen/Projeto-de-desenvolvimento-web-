<?php
// public/processa_cadastro.php
session_start();
require __DIR__ . '/../Banco de dados/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $cpf = trim($_POST['cpf']);
    $telefone = trim($_POST['telefone']);
    $senha = $_POST['senha'];
    $confirma_senha = $_POST['confirma_senha'];

    if ($senha !== $confirma_senha) {
        header("Location: tela_cadastro.html?erro=senhas_nao_conferem");
        exit();
    }

    if (strlen($senha) < 6) {
        header("Location: tela_cadastro.html?erro=senha_curta");
        exit();
    }

    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    $tipo_usuario = 'cliente';
    // Lógica simples de fornecedor por domínio (pode ser ajustada)
    if (strpos($email, "@LojaLTDA.com") !== false) {
        $tipo_usuario = 'fornecedor';
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, cpf, telefone, senha, tipo) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $email, $cpf, $telefone, $senha_hash, $tipo_usuario]);

        $_SESSION['usuario_id'] = $pdo->lastInsertId();
        $_SESSION['usuario_nome'] = $nome;
        $_SESSION['usuario_tipo'] = $tipo_usuario;

        // Cupom de boas vindas
        if ($tipo_usuario === 'cliente') {
            $novo_id = $_SESSION['usuario_id'];
            $codigo_cupom = 'BemVindo_' . $novo_id;
            // Verifica se tabela cupons existe e tem colunas certas, assumindo V6 setup
            $stmt_cupom = $pdo->prepare("INSERT INTO cupons (codigo, descricao, tipo_desconto, valor_desconto, valor_minimo, data_inicio, data_fim, limite_uso, ativo, usuario_id) VALUES (?, ?, ?, ?, ?, NOW(), NOW() + INTERVAL '7 days', 1, 1, ?)");
            // Postgres interval syntax: NOW() + INTERVAL '7 days'
            // MySQL: DATE_ADD(NOW(), INTERVAL 7 DAY)
            // Vamos usar sintaxe compatível ou ajustada.
            // O driver PDO pode aceitar syntax padrão SQL.
            // Para Postgres: NOW() + INTERVAL '7 day'

            // Vou usar uma query mais genérica ou ajustada para Postgres, já que mudamos o banco.
            $stmt_cupom = $pdo->prepare("INSERT INTO cupons (codigo, descricao, tipo_desconto, valor_desconto, valor_minimo, data_inicio, data_fim, limite_uso, ativo, usuario_id) VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP + INTERVAL '7 days', 1, 1, ?)");
            $stmt_cupom->execute([$codigo_cupom, 'Cupom de Boas Vindas', 'porcentagem', 10.00, 50.00, $novo_id]);
        }

        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        // Erro 23505 é Unique Violation no Postgres (equivalente a 1062 no MySQL)
        if ($e->getCode() == 23505) {
            header("Location: tela_cadastro.html?erro=email_cpf_duplicado");
        } else {
            // error_log($e->getMessage());
            header("Location: tela_cadastro.html?erro=db_error");
        }
        exit();
    }
} else {
    header("Location: tela_cadastro.html");
    exit();
}
