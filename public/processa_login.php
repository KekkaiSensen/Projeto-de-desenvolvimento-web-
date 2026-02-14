<?php
// public/processa_login.php
session_start();

// 1. Inclui a conexão (ajustado para estar em public/)
require __DIR__ . '/../Banco de dados/conexao.php';

// Carregamento via Composer ou manual
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
} else {
    require __DIR__ . '/../src/Services/AuthService.php';
}

use Services\AuthService;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $senha_digitada = $_POST['senha'];

    try {
        $authService = new AuthService($pdo);
        $usuario = $authService->login($email, $senha_digitada);

        if ($usuario) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_tipo'] = $authService->identifyUserType($email);

            // Redireciona para home (mesma pasta)
            header("Location: index.php");
            exit();
        } else {
            // Login inválido
            header("Location: tela_login.html?erro=login_invalido");
            exit();
        }
    } catch (PDOException $e) {
        die("Erro ao fazer login: " . $e->getMessage());
    }
} else {
    header("Location: tela_login.html");
    exit();
}
