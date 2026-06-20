<?php
session_start();
require_once __DIR__ . '/../../../config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "/public/index.php");
    exit;
}

$sql = "SELECT e.codigo_interno, e.designacao, e.marca, e.modelo, e.estado, e.criticidade, l.servico_departamento 
        FROM equipamentos e
        LEFT JOIN localizacoes l ON e.localizacao_id = l.id
        WHERE e.apagado = FALSE
        ORDER BY e.codigo_interno ASC";
$stmt = $pdo->query($sql);
$equipamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=Inventario_Equipamentos_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');

fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

fputcsv($output, ['Código', 'Designação', 'Marca', 'Modelo', 'Serviço Atual', 'Criticidade', 'Estado'], ';');

foreach ($equipamentos as $row) {
    fputcsv($output, [
        $row['codigo_interno'],
        $row['designacao'],
        $row['marca'],
        $row['modelo'],
        $row['servico_departamento'] ?? 'Sem Localização',
        $row['criticidade'],
        $row['estado']
    ], ';');
}

fclose($output);
exit;