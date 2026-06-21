<?php
$current_path = $_SERVER['PHP_SELF']; 
function isActive($needle, $path) {
    return str_contains($path, $needle) ? 'active' : '';
}

require_once __DIR__ . '\includes\header.php';
require_once __DIR__ . '\includes\sidebar.php';

$total_equipamentos = $pdo->query(
    "SELECT COUNT(*) FROM equipamentos WHERE apagado = FALSE"
)->fetchColumn();

$total_ativos = $pdo->query(
    "SELECT COUNT(*) FROM equipamentos WHERE apagado = FALSE AND estado = 'Ativo'"
)->fetchColumn();

$total_manutencao = $pdo->query(
    "SELECT COUNT(*) FROM equipamentos 
     WHERE apagado = FALSE AND estado IN ('Em manutenção', 'Em calibração')"
)->fetchColumn();

$total_inativos = $pdo->query(
    "SELECT COUNT(*) FROM equipamentos 
     WHERE apagado = FALSE AND estado = 'Inativo'"
)->fetchColumn();

$stmt = $pdo->query(
    "SELECT codigo_interno, designacao, estado, criticidade 
     FROM equipamentos 
     WHERE apagado = FALSE 
       AND criticidade = 'Suporte de vida' 
       AND estado != 'Ativo'
     ORDER BY designacao"
);
$equip_criticos = $stmt->fetchAll(PDO::FETCH_ASSOC);

try {
    $stmtLogs = $pdo->query("
        SELECT l.acao, l.data_registo, e.codigo_interno, e.designacao, u.nome AS utilizador
        FROM logs_equipamentos l
        JOIN equipamentos e ON l.equipamento_id = e.id
        JOIN utilizadores u ON l.utilizador_id = u.id
        ORDER BY l.data_registo DESC LIMIT 10
    ");
    $logs = $stmtLogs->fetchAll(PDO::FETCH_ASSOC);

    $log_html = "<div class='table-responsive'><table class='table table-sm table-striped text-start' style='font-size: 0.85rem;'>
                    <thead><tr><th>Data</th><th>Equipamento</th><th>Ação</th><th>Utilizador</th></tr></thead><tbody>";
    
    if (empty($logs)) {
        $log_html .= "<tr><td colspan='4' class='text-center text-muted'>Nenhum registo encontrado.</td></tr>";
    } else {
        foreach ($logs as $log) {
            $data_formatada = date('d/m H:i', strtotime($log['data_registo']));
            $log_html .= "<tr>
                            <td class='text-nowrap'>{$data_formatada}</td>
                            <td><b>{$log['codigo_interno']}</b><br><small class='text-muted'>{$log['designacao']}</small></td>
                            <td><span class='badge bg-secondary'>{$log['acao']}</span></td>
                            <td>{$log['utilizador']}</td>
                          </tr>";
        }
    }
    $log_html .= "</tbody></table></div>";

} catch (PDOException $e) {
    $log_html = "<div class='alert alert-danger'>Erro ao carregar histórico.</div>";
}
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="text-secondary">Visão Geral do Parque Tecnológico</h2>
        <hr>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white h-100 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Total de Equipamentos</h5>
                <h1 class="display-4 fw-bold">
                    <?= $total_equipamentos; ?>
                </h1>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white h-100 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Equipamentos Ativos</h5>
                <h1 class="display-4 fw-bold">
                    <?= $total_ativos; ?>
                </h1>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark h-100 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Em Manutenção</h5>
                <h1 class="display-4 fw-bold">
                    <?= $total_manutencao; ?>
                </h1>
            </div>
        </div>
    </div><div class="col-md-3">
        <div class="card bg-secondary text-dark h-100 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Inactivos</h5>
                <h1 class="display-4 fw-bold">
                    <?= $total_inativos; ?>
                </h1>
            </div>
        </div>
    </div>
</div>
<div class="row mt-4">
    <div class="col-12">
        <h4 class="text-secondary">Equipamento de Suporte de Vida Fora de Serviço</h4>
        <?php if (empty($equip_criticos)): ?>
            <p class="text-muted">Nenhum equipamento crítico fora de serviço. ✓</p>
        <?php else: ?>
            <table class="table table-sm">
                <thead>
                    <tr><th>Código</th><th>Designação</th><th>Estado</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($equip_criticos as $e): ?>
                        <tr>
                            <td><?= htmlspecialchars($e['codigo_interno']) ?></td>
                            <td><?= htmlspecialchars($e['designacao']) ?></td>
                            <td><span class="badge bg-danger"><?= htmlspecialchars($e['estado']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
<div class="row mb-4 mt-4">
    <div class="col-12 text-end">
        <button onclick="verHistorico()" class="btn btn-outline-primary shadow-sm">
            <i class="fa-solid fa-clock-rotate-left me-2"></i>Ver Histórico de Intervenções
        </button>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>