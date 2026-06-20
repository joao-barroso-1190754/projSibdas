<?php
// private/views/equipamento/index.php

require_once __DIR__ . '/../../includes/header.php'; 
require_once __DIR__ . '/../../includes/sidebar.php';

// 1. Initialize variables for our search form to keep it "sticky"
$search_text = $_GET['pesquisa'] ?? '';
$search_estado = $_GET['estado'] ?? '';
$search_crit = $_GET['criticidade'] ?? '';

try {
    // 2. Build the Dynamic SQL Query
    $where_clauses = ["e.apagado = FALSE"];
    $params = [];

    // If the user typed text, search Code, Name, or Brand
    if (!empty($search_text)) {
        $where_clauses[] = "(e.codigo_interno LIKE :text OR e.designacao LIKE :text OR e.marca LIKE :text)";
        $params[':text'] = '%' . $search_text . '%';
    }
    
    // If the user selected a specific state
    if (!empty($search_estado)) {
        $where_clauses[] = "e.estado = :estado";
        $params[':estado'] = $search_estado;
    }
    
    // If the user selected a specific criticality
    if (!empty($search_crit)) {
        $where_clauses[] = "e.criticidade = :crit";
        $params[':crit'] = $search_crit;
    }

    // Combine all conditions with "AND"
    $where_sql = implode(' AND ', $where_clauses);

    $sql = "SELECT e.id, e.codigo_interno, e.designacao, e.marca, e.modelo, e.estado, e.criticidade, l.servico_departamento 
            FROM equipamentos e
            LEFT JOIN localizacoes l ON e.localizacao_id = l.id
            WHERE $where_sql
            ORDER BY e.codigo_interno ASC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $equipamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_msg = "Erro ao carregar equipamentos: " . $e->getMessage();
}

// Badge Helpers
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
        <h2 class="text-secondary"><i class="fa-solid fa-stethoscope me-2"></i>Gestão de Equipamentos</h2>
        <p class="text-muted">Inventário geral do parque tecnológico hospitalar.</p>
    </div>
    
    <?php if ($_SESSION['user_perfil'] !== 'Normal'): ?>
        <div class="col-md-4 text-end align-self-center">
            <a href="create.php" class="btn btn-primary fw-bold shadow-sm">+ Novo Equipamento</a>
        </div>
    <?php endif; ?>
</div>

<div class="card shadow-sm border-0 mb-4 bg-white">
    <div class="card-body">
        <form action="index.php" method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label text-muted small fw-bold mb-1">Pesquisa Livre</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" class="form-control border-start-0" name="pesquisa" placeholder="Código, Designação ou Marca..." value="<?= htmlspecialchars($search_text) ?>">
                </div>
            </div>
            
            <div class="col-md-3">
                <label class="form-label text-muted small fw-bold mb-1">Estado</label>
                <select class="form-select" name="estado">
                    <option value="">Todos os Estados</option>
                    <option value="Ativo" <?= $search_estado == 'Ativo' ? 'selected' : '' ?>>Ativo</option>
                    <option value="Em manutenção" <?= $search_estado == 'Em manutenção' ? 'selected' : '' ?>>Em manutenção</option>
                    <option value="Em calibração" <?= $search_estado == 'Em calibração' ? 'selected' : '' ?>>Em calibração</option>
                    <option value="Inativo" <?= $search_estado == 'Inativo' ? 'selected' : '' ?>>Inativo</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label text-muted small fw-bold mb-1">Criticidade</label>
                <select class="form-select" name="criticidade">
                    <option value="">Todas</option>
                    <option value="Baixa" <?= $search_crit == 'Baixa' ? 'selected' : '' ?>>Baixa</option>
                    <option value="Média" <?= $search_crit == 'Média' ? 'selected' : '' ?>>Média</option>
                    <option value="Alta" <?= $search_crit == 'Alta' ? 'selected' : '' ?>>Alta</option>
                    <option value="Suporte de vida" <?= $search_crit == 'Suporte de vida' ? 'selected' : '' ?>>Suporte de vida</option>
                </select>
            </div>

            <div class="col-md-1 text-end">
                <button type="submit" class="btn btn-secondary w-100" title="Filtrar">Filtrar</button>
            </div>
        </form>
    </div>
</div>

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
                            <td colspan="7" class="text-center py-4 text-muted">
                                Nenhum equipamento encontrado com os filtros atuais.
                                <?php if(!empty($search_text) || !empty($search_estado)): ?>
                                    <br><a href="index.php" class="btn btn-sm btn-outline-secondary mt-2">Limpar Filtros</a>
                                <?php endif; ?>
                            </td>
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
                                    
                                    <?php if ($_SESSION['user_perfil'] === 'Normal'): ?>
                                        <button class="btn btn-sm btn-outline-danger">Reportar Avaria</button>
                                    
                                    <?php else: ?>
                                        <a href="edit.php?id=<?= $eq['id']; ?>" class="btn btn-sm btn-outline-warning">Editar</a>
                                        <form action="delete.php" method="POST" class="d-inline delete-form">
                                            <input type="hidden" name="id" value="<?= $eq['id']; ?>">
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete">Remover</button>
                                        </form>
                                    <?php endif; ?>

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