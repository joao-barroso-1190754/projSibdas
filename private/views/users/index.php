<?php
$current_path = $_SERVER['PHP_SELF']; 
function isActive($needle, $path) {
    return str_contains($path, $needle) ? 'active' : '';
}
require_once __DIR__ . '/../../includes/header.php'; 

if ($_SESSION['user_perfil'] !== 'Admin') {
    $_SESSION['error_msg'] = "Acesso Negado: Não tem permissões para aceder a esta área.";
    header("Location: ../../dashboard.php");
    exit;
}

require_once __DIR__ . '/../../includes/sidebar.php';

try {
    $stmt = $pdo->query("SELECT id, nome, email, perfil, data_criacao FROM utilizadores ORDER BY nome ASC");
    $utilizadores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_msg = "Erro ao carregar utilizadores: " . $e->getMessage();
}
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="text-secondary"><i class="fa-solid fa-users me-2"></i>Gestão de Utilizadores</h2>
        <p class="text-muted">Crie e gira os acessos da equipa médica e técnica.</p>
    </div>
    <div class="col-md-4 text-end align-self-center">
        <a href="create.php" class="btn btn-primary fw-bold shadow-sm">+ Novo Utilizador</a>
    </div>
</div>

<?php if (isset($error_msg)): ?> <div class="alert alert-danger"><?= $error_msg; ?></div> <?php endif; ?>
<?php if (isset($_SESSION['success_msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped m-0">
                <thead class="table-dark">
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Perfil</th>
                        <th>Data de Criação</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($utilizadores as $user): ?>
                        <tr>
                            <td class="fw-bold"><?= htmlspecialchars($user['nome']); ?></td>
                            <td><?= htmlspecialchars($user['email']); ?></td>
                            <td>
                                <?php if($user['perfil'] == 'Admin'): ?>
                                    <span class="badge bg-danger">Admin</span>
                                <?php elseif($user['perfil'] == 'Tecnico'): ?>
                                    <span class="badge bg-warning text-dark">Técnico</span>
                                <?php else: ?>
                                    <span class="badge bg-info text-dark">Normal</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($user['data_criacao'])); ?></td>
                            <td class="text-center">
                                <a href="edit.php?id=<?= $user['id']; ?>" class="btn btn-sm btn-outline-warning">Editar</a>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <form action="delete.php" method="POST" class="d-inline" onsubmit="return confirm('Tem a certeza que deseja remover este acesso?');">
                                        <input type="hidden" name="id" value="<?= $user['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Remover</button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-secondary" disabled>Remover</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>