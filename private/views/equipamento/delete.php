<?php
session_start();
require_once __DIR__ . '/../../../config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "/public/index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];

    try {
        $stmt = $pdo->prepare("UPDATE equipamentos SET apagado = TRUE WHERE id = :id");
        $stmt->execute([':id' => $id]);
        
        $log_sql = "INSERT INTO logs_equipamentos (equipamento_id, utilizador_id, acao) VALUES (:eq_id, :user_id, 'Equipamento Abatido / Removido')";
        $log_stmt = $pdo->prepare($log_sql);
        $log_stmt->execute([':eq_id' => $target_id, ':user_id' => $_SESSION['user_id']]);
        
        $_SESSION['success_msg'] = "Equipamento/Componente removido do inventário ativo.";
        
    } catch (PDOException $e) {
        $_SESSION['error_msg'] = "Erro ao remover equipamento: " . $e->getMessage();
    }
}

header("Location: index.php");
exit;