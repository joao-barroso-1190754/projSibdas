<?php
$current_path = $_SERVER['PHP_SELF']; 
function isActive($needle, $path) {
    return str_contains($path, $needle) ? 'active' : '';
}
require_once __DIR__ . '/../../includes/header.php'; 
require_once __DIR__ . '/../../includes/sidebar.php';

try {
    $stmt = $pdo->query("SELECT * FROM fornecedores ORDER BY nome_empresa ASC");
    $fornecedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_msg = "Erro ao carregar fornecedores: " . $e->getMessage();
}
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="text-secondary">🏢 Gestão de Fornecedores</h2>
        <p class="text-muted">Gira os fabricantes, distribuidores e empresas de assistência técnica.</p>
    </div>
    <div class="col-md-4 text-end align-self-center">
        <a href="create.php" class="btn btn-primary fw-bold shadow-sm">+ Novo Fornecedor</a>
    </div>
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
                        <th>Nome da Empresa</th>
                        <th>NIF</th>
                        <th>Tipo</th>
                        <th>Contacto Principal</th>
                        <th>Email</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($fornecedores)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Nenhum fornecedor registado no sistema.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($fornecedores as $forn): ?>
                            <tr>
                                <td class="fw-bold">
                                    <?= htmlspecialchars($forn['nome_empresa']); ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($forn['nif']); ?>
                                </td>
                                <td><span class="badge bg-secondary">
                                        <?= htmlspecialchars($forn['tipo_fornecedor']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?= htmlspecialchars($forn['contacto_telefonico']); ?><br>
                                    <small class="text-muted">
                                        <?= htmlspecialchars($forn['pessoa_contacto']); ?>
                                    </small>
                                </td>
                                <td>
                                    <?= htmlspecialchars($forn['email']); ?>
                                </td>
                                <td class="text-center align-middle">
                                    <a href="edit.php?id=<?= $forn['id']; ?>" class="btn btn-sm btn-outline-warning">Editar</a>

                                    <form action="delete.php" method="POST" class="d-inline delete-form">
                                        <input type="hidden" name="id" value="<?= $forn['id']; ?>">
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
<?php
require_once __DIR__ . '/../../includes/footer.php';
?>