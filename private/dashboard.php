<?php
$current_path = $_SERVER['PHP_SELF']; 
function isActive($needle, $path) {
    return str_contains($path, $needle) ? 'active' : '';
}

require_once __DIR__ . '\includes\header.php';
require_once __DIR__ . '\includes\sidebar.php';

// Total de equipamentos (não apagados)
$total_equipamentos = $pdo->query(
    "SELECT COUNT(*) FROM equipamentos WHERE apagado = FALSE"
)->fetchColumn();

// Equipamentos ativos
$total_ativos = $pdo->query(
    "SELECT COUNT(*) FROM equipamentos WHERE apagado = FALSE AND estado = 'Ativo'"
)->fetchColumn();

// Em manutenção OU em calibração — agrupados, já que ambos significam "fora de serviço temporariamente"
$total_manutencao = $pdo->query(
    "SELECT COUNT(*) FROM equipamentos 
     WHERE apagado = FALSE AND estado IN ('Em manutenção', 'Em calibração')"
)->fetchColumn();

// TODO (seu): considerar um quarto cartão para 'Inativo', se fizer sentido no fluxo da clínica
$total_inativos = $pdo->query(
    "SELECT COUNT(*) FROM equipamentos 
     WHERE apagado = FALSE AND estado = 'Inativo'"
)->fetchColumn();

// Equipamento crítico que não está ativo — provavelmente a informação mais "actionable" do dashboard
$stmt = $pdo->query(
    "SELECT codigo_interno, designacao, estado, criticidade 
     FROM equipamentos 
     WHERE apagado = FALSE 
       AND criticidade = 'Suporte de vida' 
       AND estado != 'Ativo'
     ORDER BY designacao"
);
$equip_criticos = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

<?php
require_once __DIR__ . '/includes/footer.php';
?>