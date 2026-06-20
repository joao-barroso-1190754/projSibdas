<?php

require_once __DIR__ . '/../../includes/header.php'; 
require_once __DIR__ . '/../../includes/sidebar.php';

try {
    $sql = "SELECT e.id, e.codigo_interno, e.designacao, e.marca, e.modelo, e.estado, e.criticidade, l.servico_departamento 
            FROM equipamentos e
            LEFT JOIN localizacoes l ON e.localizacao_id = l.id
            ORDER BY e.codigo_interno ASC";
            
    $stmt = $pdo->query($sql);
    $equipamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_msg = "Erro ao carregar equipamentos: " . $e->getMessage();
}

function getEstadoBadge($estado) {
    switch ($estado) {
        case 'Ativo': return 'bg-success';
        case 'Em manutenção': return 'bg-warning text-dark';
        case 'Inativo': return 'bg-secondary';
        case 'Em calibração': return 'bg-info text-dark';
        case 'Em quarentena': return 'bg-danger';
        case 'Abatido': return 'bg-dark';
        default: return 'bg-primary';
    }
}

function getCriticidadeBadge($criticidade) {
    switch ($criticidade) {
        case 'Baixa': return 'bg-success';
        case 'Média': return 'bg-info text-dark';
        case 'Alta': return 'bg-warning text-dark';
        case 'Suporte de vida': return 'bg-danger shadow';
        default: return 'bg-secondary';
    }
}
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="text-secondary">🩺 Gestão de Equipamentos</h2>
        <p class="text-muted">Inventário geral do parque tecnológico hospitalar.</p>
    </div>
    <div class="col-md-4 text-end align-self-center">
        <a href="create.php" class="btn btn-primary fw-bold shadow-sm">+ Novo Equipamento</a>
    </div>
</div>

<?php if (isset($error_msg)): ?>
    <div class="alert alert-danger"><?= $error_msg; ?></div>
<?php endif; ?>

<?php if (isset($_SESSION['error_msg'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['success_msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped m-0">
                <thead class="table-dark">
                    <tr>
                        <th>Código</th>
                        <th>Designação</th>
                        <th>Marca / Modelo</th>
                        <th>Serviço Atual</th>
                        <th>Criticidade</th>
                        <th>Estado</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($equipamentos)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Nenhum equipamento registado no sistema.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($equipamentos as $eq): ?>
                            <tr>
                                <td class="fw-bold text-primary"><?= htmlspecialchars($eq['codigo_interno']); ?></td>
                                <td><?= htmlspecialchars($eq['designacao']); ?></td>
                                <td>
                                    <?= htmlspecialchars($eq['marca']); ?><br>
                                    <small class="text-muted"><?= htmlspecialchars($eq['modelo']); ?></small>
                                </td>
                                <td><?= htmlspecialchars($eq['servico_departamento'] ?? 'Sem Localização'); ?></td>
                                <td><span class="badge <?= getCriticidadeBadge($eq['criticidade']); ?>"><?= htmlspecialchars($eq['criticidade']); ?></span></td>
                                <td><span class="badge <?= getEstadoBadge($eq['estado']); ?>"><?= htmlspecialchars($eq['estado']); ?></span></td>
                                <td class="text-center align-middle">
                                    <a href="edit.php?id=<?= $eq['id']; ?>" class="btn btn-sm btn-outline-warning">Editar</a>
                                    
                                    <form action="delete.php" method="POST" class="d-inline delete-form">
                                        <input type="hidden" name="id" value="<?= $eq['id']; ?>">
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete">Remover</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>