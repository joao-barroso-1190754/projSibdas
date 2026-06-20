<?php
session_start();
require_once __DIR__ . '/../../config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /PROJECTO/frontoffice/index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];

    try {
        $stmt = $pdo->prepare("UPDATE equipamentos SET apagado = TRUE WHERE id = :id");
        $stmt->execute([':id' => $id]);
        
        $_SESSION['success_msg'] = "Equipamento/Componente removido do inventário ativo.";
        
    } catch (PDOException $e) {
        $_SESSION['error_msg'] = "Erro ao remover equipamento: " . $e->getMessage();
    }
}

header("Location: index.php");
exit;