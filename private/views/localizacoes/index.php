<?php

$current_path = $_SERVER['PHP_SELF']; 
function isActive($needle, $path) {
    return str_contains($path, $needle) ? 'active' : '';
}

require_once __DIR__ . '/../../includes/header.php'; 
require_once __DIR__ . '/../../includes/sidebar.php';

try {
    $stmt = $pdo->query("SELECT * FROM localizacoes WHERE apagado = FALSE ORDER BY edificio, servico_departamento");
    $localizacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_msg = "Erro ao carregar localizações: " . $e->getMessage();
}
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="text-secondary">📍 Gestão de Localizações</h2>
        <p class="text-muted">Gira os edifícios, serviços e salas da clinica.</p>
    </div>
    <?php if ($_SESSION['user_perfil'] === 'Admin'): ?>
        <div class="col-md-4 text-end align-self-center">
            <a href="create.php" class="btn btn-primary fw-bold shadow-sm">+ Nova Localização</a>
        </div>
    <?php endif; ?>
</div>

<?php if (isset($error_msg)): ?>
    <div class="alert alert-danger">
        <?= $error_msg; ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error_msg'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $_SESSION['error_msg'];
        unset($_SESSION['error_msg']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['success_msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['success_msg'];
        unset($_SESSION['success_msg']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped m-0">
                <thead class="table-dark">
                    <tr>
                        <th>Edifício</th>
                        <th>Piso</th>
                        <th>Serviço / Departamento</th>
                        <th>Sala / Gabinete</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($localizacoes)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Nenhuma localização registada no sistema.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($localizacoes as $loc): ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars($loc['edificio']); ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($loc['piso']); ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($loc['servico_departamento']); ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($loc['sala_gabinete']); ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($_SESSION['user_perfil'] === 'Admin'): ?>
                                        <a href="edit.php?id=<?= $loc['id']; ?>" class="btn btn-sm btn-outline-warning">Editar</a>
                                        <form action="delete.php" method="POST" class="d-inline delete-form">
                                            <input type="hidden" name="id" value="<?= $loc['id']; ?>">
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete"
                                                data-confirm-title="Remover localização?"
                                                data-confirm-text="O equipamento associado a esta localização poderá ficar sem sala atribuída.">Remover</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted small">Apenas consulta</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php
    require_once __DIR__ . '/../../includes/footer.php';
    ?>