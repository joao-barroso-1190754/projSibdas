<?php
$current_path = $_SERVER['PHP_SELF'];
function isActive($needle, $path)
{
    return str_contains($path, $needle) ? 'active' : '';
}
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';

if ($_SESSION['user_perfil'] === 'Normal') {
    header("Location: " . BASE_URL . "/private/dashboard.php");
    exit;
}

$search_text = $_GET['pesquisa'] ?? '';
$search_acao = $_GET['acao'] ?? '';
$search_data_inicio = $_GET['data_inicio'] ?? '';
$search_data_fim = $_GET['data_fim'] ?? '';

try {
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

    $sql = "SELECT l.id, l.acao, l.data_registo, e.codigo_interno, e.designacao, u.nome AS utilizador
            FROM logs_equipamentos l
            JOIN equipamentos e ON l.equipamento_id = e.id
            JOIN utilizadores u ON l.utilizador_id = u.id
            WHERE $where_sql
            ORDER BY l.data_registo DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $acoesDisponiveis = $pdo->query("SELECT DISTINCT acao FROM logs_equipamentos ORDER BY acao")->fetchAll(PDO::FETCH_COLUMN);

} catch (PDOException $e) {
    $error_msg = "Erro ao carregar histórico: " . $e->getMessage();
    $logs = [];
    $acoesDisponiveis = [];
}

function getAcaoBadge($acao)
{
    if (str_contains($acao, 'Abatido') || str_contains($acao, 'Removido')) {
        return 'bg-danger';
    }
    if (str_contains($acao, 'Avaria') || str_contains($acao, 'Manutenção')) {
        return 'bg-warning text-dark';
    }
    if (str_contains($acao, 'Criado') || str_contains($acao, 'Adicionado')) {
        return 'bg-success';
    }
    return 'bg-secondary';
}
?>

<?php if (isset($_SESSION['success_msg'])): ?>
    <div class="alert alert-success shadow-sm"><?= htmlspecialchars($_SESSION['success_msg']); unset($_SESSION['success_msg']); ?></div>
<?php endif; ?>
<?php if (isset($error_msg)): ?>
    <div class="alert alert-danger shadow-sm"><?= htmlspecialchars($error_msg); ?></div>
<?php endif; ?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="text-secondary"><i class="fa-solid fa-clock-rotate-left me-2"></i>Histórico de Intervenções</h2>
        <p class="text-muted">Registo de todas as ações realizadas sobre o parque tecnológico.</p>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4 bg-white">
    <div class="card-body">
        <form action="index.php" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label text-muted small fw-bold mb-1">Pesquisa Livre</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" class="form-control border-start-0" name="pesquisa"
                        placeholder="Código, Designação ou Utilizador..." value="<?= htmlspecialchars($search_text) ?>">
                </div>
            </div>

            <div class="col-md-3">
                <label class="form-label text-muted small fw-bold mb-1">Ação</label>
                <select class="form-select" name="acao">
                    <option value="">Todas as Ações</option>
                    <?php foreach ($acoesDisponiveis as $acao): ?>
                        <option value="<?= htmlspecialchars($acao) ?>" <?= $search_acao === $acao ? 'selected' : '' ?>>
                            <?= htmlspecialchars($acao) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label text-muted small fw-bold mb-1">De</label>
                <input type="date" class="form-control" name="data_inicio" value="<?= htmlspecialchars($search_data_inicio) ?>">
            </div>

            <div class="col-md-2">
                <label class="form-label text-muted small fw-bold mb-1">Até</label>
                <input type="date" class="form-control" name="data_fim" value="<?= htmlspecialchars($search_data_fim) ?>">
            </div>

            <div class="col-md-1 text-end">
                <button type="submit" class="btn btn-secondary w-100" title="Filtrar">Filtrar</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0">
    <div class="d-flex justify-content-end mb-2">
        <a href="export_csv.php?<?= htmlspecialchars($_SERVER['QUERY_STRING']) ?>" class="btn btn-sm btn-success me-2 shadow-sm">
            <i class="fa-solid fa-file-csv me-1"></i> Exportar CSV
        </a>

        <button onclick="exportarHistoricoPDF()" class="btn btn-sm btn-danger shadow-sm">
            <i class="fa-solid fa-file-pdf me-1"></i> Exportar PDF
        </button>
    </div>

    <div class="card shadow-sm border-0" id="tabela-historico">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped m-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Data</th>
                            <th>Equipamento</th>
                            <th>Ação</th>
                            <th>Utilizador</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    Nenhum registo encontrado com os filtros atuais.
                                    <?php if (!empty($search_text) || !empty($search_acao) || !empty($search_data_inicio) || !empty($search_data_fim)): ?>
                                        <br><a href="index.php" class="btn btn-sm btn-outline-secondary mt-2">Limpar Filtros</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td class="text-nowrap"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($log['data_registo']))) ?></td>
                                    <td>
                                        <span class="fw-bold text-primary"><?= htmlspecialchars($log['codigo_interno']) ?></span><br>
                                        <small class="text-muted"><?= htmlspecialchars($log['designacao']) ?></small>
                                    </td>
                                    <td><span class="badge <?= getAcaoBadge($log['acao']) ?>"><?= htmlspecialchars($log['acao']) ?></span></td>
                                    <td><?= htmlspecialchars($log['utilizador']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . '/../../includes/footer.php'; ?>
</div>