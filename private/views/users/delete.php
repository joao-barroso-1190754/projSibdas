<?php
session_start();
require_once __DIR__ . '/../../../config/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'Admin') {
    header("Location: ../../dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $target_id = $_POST['id'];

    // CRITICAL: Prevent the Admin from deleting themselves
    if ($target_id == $_SESSION['user_id']) {
        $_SESSION['error_msg'] = "Ação bloqueada: Não pode remover a sua própria conta ativa.";
    } else {
        try {
            $stmt = $pdo->prepare("DELETE FROM utilizadores WHERE id = :id");
            $stmt->execute([':id' => $target_id]);
            $_SESSION['success_msg'] = "Acesso de utilizador removido com sucesso.";
        } catch (PDOException $e) {
            $_SESSION['error_msg'] = "Erro ao remover utilizador: " . $e->getMessage();
        }
    }
}

header("Location: index.php");
exit;