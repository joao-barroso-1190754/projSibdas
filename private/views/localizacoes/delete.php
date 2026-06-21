<?php

session_start();

require_once __DIR__ . '/../../../config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "/public/index.php");
    exit;
}

if ($_SESSION['user_perfil'] !== 'Admin') {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];

    try {
        $stmt = $pdo->prepare("UPDATE localizacoes SET apagado = TRUE WHERE id = :id");
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