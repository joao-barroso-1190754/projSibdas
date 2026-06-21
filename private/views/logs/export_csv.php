<?php
session_start();
require_once __DIR__ . '/../../../config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "/public/index.php");
    exit;
}

if ($_SESSION['user_perfil'] === 'Normal') {
    header("Location: " . BASE_URL . "/private/dashboard.php");
    exit;
}

$search_text = $_GET['pesquisa'] ?? '';
$search_acao = $_GET['acao'] ?? '';
$search_data_inicio = $_GET['data_inicio'] ?? '';
$search_data_fim = $_GET['data_fim'] ?? '';

$where_clauses = ["1=1"];
$params = [];

if (!empty($search_text)) {
    $where_clauses[] = "(e.codigo_interno LIKE :text OR e.designacao LIKE :text OR u.nome LIKE :text)";
    $params[':text'] = '%' . $search_text . '%';
}

if (!empty($search_acao)) {
    $where_clauses[] = "l.acao = :acao";
    $params[':acao'] = $search_acao;
}

if (!empty($search_data_inicio)) {
    $where_clauses[] = "l.data_registo >= :data_inicio";
    $params[':data_inicio'] = $search_data_inicio . ' 00:00:00';
}

if (!empty($search_data_fim)) {
    $where_clauses[] = "l.data_registo <= :data_fim";
    $params[':data_fim'] = $search_data_fim . ' 23:59:59';
}

$where_sql = implode(' AND ', $where_clauses);

$sql = "SELECT l.data_registo, e.codigo_interno, e.designacao, l.acao, u.nome AS utilizador
        FROM logs_equipamentos l
        JOIN equipamentos e ON l.equipamento_id = e.id
        JOIN utilizadores u ON l.utilizador_id = u.id
        WHERE $where_sql
        ORDER BY l.data_registo DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=Historico_Intervencoes_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');

fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

fputcsv($output, ['Data', 'Código', 'Designação', 'Ação', 'Utilizador'], ';');

foreach ($logs as $row) {
    fputcsv($output, [
        date('d/m/Y H:i', strtotime($row['data_registo'])),
        $row['codigo_interno'],
        $row['designacao'],
        $row['acao'],
        $row['utilizador']
    ], ';');
}

fclose($output);
exit;