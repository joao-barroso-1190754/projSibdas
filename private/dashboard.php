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
        ORDER BY l.data_registo DESC LIMIT 5
    ");
    $logs_recentes = $stmtLogs->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $logs_recentes = [];
    $logs_error = "Erro ao carregar histórico recente.";
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
    <div class="col-6">
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
    <div class="col-6">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="text-secondary mb-0">Atividade Recente</h4>
        </div>
        <?php if (isset($logs_error)): ?>
            <div class="alert alert-danger shadow-sm"><?= htmlspecialchars($logs_error) ?></div>
        <?php elseif (empty($logs_recentes)): ?>
            <p class="text-muted">Nenhum registo de atividade ainda.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm table-striped">
                    <thead><tr><th>Data</th><th>Equipamento</th><th>Ação</th><th>Utilizador</th></tr></thead>
                    <tbody>
                        <?php foreach ($logs_recentes as $log): ?>
                            <tr>
                                <td class="text-nowrap"><?= htmlspecialchars(date('d/m H:i', strtotime($log['data_registo']))) ?></td>
                                <td>
                                    <b><?= htmlspecialchars($log['codigo_interno']) ?></b><br>
                                    <small class="text-muted"><?= htmlspecialchars($log['designacao']) ?></small>
                                </td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($log['acao']) ?></span></td>
                                <td><?= htmlspecialchars($log['utilizador']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>