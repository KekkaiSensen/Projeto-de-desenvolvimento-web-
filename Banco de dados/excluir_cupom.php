<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'fornecedor') {
    header("Location: ../public/index.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $usuario_id = $_SESSION['usuario_id'];

    try {
        // Soft Delete: Marca como inativo para manter histórico e evitar erro de FK
        $stmt = $pdo->prepare("UPDATE cupons SET ativo = 0 WHERE id = ? AND usuario_id = ?");
        $stmt->execute([$id, $usuario_id]);

        header("Location: ../public/tela_minha_conta.php?msg=cupom_excluido");
    } catch (PDOException $e) {
        error_log("Erro ao excluir cupom: " . $e->getMessage());
        header("Location: ../public/tela_minha_conta.php?erro=erro_excluir");
    }
}
