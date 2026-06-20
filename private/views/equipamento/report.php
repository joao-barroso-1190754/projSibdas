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
        $stmt = $pdo->prepare("UPDATE equipamentos SET estado = 'Em manutenção' WHERE id = :id");
        $stmt->execute([':id' => $id]);
        
        $_SESSION['success_msg'] = "Avaria reportada com sucesso. A equipa técnica foi notificada.";
        
    } catch (PDOException $e) {
        $_SESSION['error_msg'] = "Erro ao reportar avaria: " . $e->getMessage();
    }
}

header("Location: index.php");
exit;