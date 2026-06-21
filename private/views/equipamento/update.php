<?php
session_start();
require_once __DIR__ . '/../../../config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "/public/index.php");
    exit;
}

if ($_SESSION['user_perfil'] !== 'Tecnico' && $_SESSION['user_perfil'] !== 'Admin') {
    header("Location: index.php");
    exit;
}

$estadosPermitidos = ['Ativo', 'Em manutenção', 'Em calibração', 'Inativo'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    $novo_estado = $_POST['estado'] ?? '';
    $nota = trim($_POST['nota'] ?? '');

    if (!in_array($novo_estado, $estadosPermitidos, true)) {
        $_SESSION['error_msg'] = "Estado inválido.";
        header("Location: index.php");
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT observacoes FROM equipamentos WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $atual = $stmt->fetchColumn();

        $linha = '[' . date('d/m/Y H:i') . ' - ' . $_SESSION['user_nome'] . '] ' . ($nota !== '' ? $nota : '(sem nota)');
        $observacoes_atualizadas = trim(($atual ?? '') . "\n" . $linha);

        $stmt = $pdo->prepare("UPDATE equipamentos SET estado = :estado, observacoes = :obs WHERE id = :id");
        $stmt->execute([
            ':estado' => $novo_estado,
            ':obs' => $observacoes_atualizadas,
            ':id' => $id
        ]);

        $log_sql = "INSERT INTO logs_equipamentos (equipamento_id, utilizador_id, acao) VALUES (:eq_id, :user_id, :acao)";
        $log_stmt = $pdo->prepare($log_sql);
        $log_stmt->execute([
            ':eq_id' => $id,
            ':user_id' => $_SESSION['user_id'],
            ':acao' => 'Estado atualizado para "' . $novo_estado . '"'
        ]);

        $_SESSION['success_msg'] = "Equipamento atualizado com sucesso.";

    } catch (PDOException $e) {
        $_SESSION['error_msg'] = "Erro ao atualizar equipamento: " . $e->getMessage();
    }
}

header("Location: index.php");
exit;