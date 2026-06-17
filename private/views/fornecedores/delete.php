<?php

session_start();

require_once __DIR__ . '/../../../config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /PROJECTO/frontoffice/index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM fornecedores WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $_SESSION['success_msg'] = "Fornecedor removido com sucesso!";
        
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $_SESSION['error_msg'] = "Não é possível remover este fornecedor porque existem equipamentos registados com ele.";
        } else {
            $_SESSION['error_msg'] = "Erro ao remover o fornecedor: " . $e->getMessage();
        }
    }
}

header("Location: index.php");
exit;