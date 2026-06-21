<?php
session_start();
require_once __DIR__ . '/../../../config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "/public/index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    $nota = trim($_POST['nota'] ?? '');

    try {
        if ($nota !== '') {
            $stmt = $pdo->prepare("SELECT observacoes FROM equipamentos WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $atual = $stmt->fetchColumn();

            $linha = '[' . date('d/m/Y H:i') . ' - ' . $_SESSION['user_nome'] . '] Avaria reportada: ' . $nota;
            $observacoes_atualizadas = trim(($atual ?? '') . "\n" . $linha);

            $stmt = $pdo->prepare("UPDATE equipamentos SET estado = 'Em manutenção', observacoes = :obs WHERE id = :id");
            $stmt->execute([':obs' => $observacoes_atualizadas, ':id' => $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE equipamentos SET estado = 'Em manutenção' WHERE id = :id");
            $stmt->execute([':id' => $id]);
        }

        $log_sql = "INSERT INTO logs_equipamentos (equipamento_id, utilizador_id, acao) VALUES (:eq_id, :user_id, 'Reportada Avaria (Em Manutenção)')";
        $log_stmt = $pdo->prepare($log_sql);
        $log_stmt->execute([':eq_id' => $id, ':user_id' => $_SESSION['user_id']]);
        
        $_SESSION['success_msg'] = "Avaria reportada com sucesso. A equipa técnica foi notificada.";
        
    } catch (PDOException $e) {
        $_SESSION['error_msg'] = "Erro ao reportar avaria: " . $e->getMessage();
    }
}

header("Location: index.php");
exit;