<?php

session_start();

require_once private_root . 'config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /public/index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM localizacoes WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $_SESSION['success_msg'] = "Localização removida com sucesso!";
        
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $_SESSION['error_msg'] = "Não é possível remover esta localização porque existem equipamentos registados nela.";
        } else {
            $_SESSION['error_msg'] = "Erro ao remover a localização: " . $e->getMessage();
        }
    }
}

header("Location: index.php");
exit;