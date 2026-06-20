<aside id="sidebar-wrapper" class="text-light shadow d-flex flex-column" style="min-height: calc(100vh - 60px);">
    <div class="p-3 border-bottom border-secondary text-center mt-2">
        <small class="d-block text-muted">Bem-vindo(a),</small>
        <strong><?= htmlspecialchars($_SESSION['user_nome']); ?></strong>
        <span class="badge bg-secondary d-block mt-2"><?= htmlspecialchars($_SESSION['user_perfil']); ?></span>
    </div>
    <div class="list-group list-group-flush mt-2">
        <a href="<?= BASE_URL ?>/private/dashboard.php" class="list-group-item list-group-item-dark p-3 border-0">
            <i class="fa-solid fa-chart-line me-2"></i> Dashboard
        </a>

        <a href="<?= BASE_URL ?>/private/views/equipamento/index.php"
            class="list-group-item list-group-item-dark p-3 border-0">
            <i class="fa-solid fa-stethoscope me-2"></i> Equipamentos
        </a>

        <?php if ($_SESSION['user_perfil'] === 'Admin' || $_SESSION['user_perfil'] === 'Tecnico'): ?>
            <a href="<?= BASE_URL ?>/private/views/localizacoes/index.php"
                class="list-group-item list-group-item-dark p-3 border-0">
                <i class="fa-solid fa-location-dot me-2"></i> Localizações
            </a>
            <a href="<?= BASE_URL ?>/private/views/fornecedores/index.php"
                class="list-group-item list-group-item-dark p-3 border-0">
                <i class="fa-solid fa-building me-2"></i> Fornecedores
            </a>
        <?php endif; ?>

        <?php if ($_SESSION['user_perfil'] === 'Admin'): ?>
            <a href="<?= BASE_URL ?>/private/views/users/index.php"
                class="list-group-item list-group-item-dark p-3 border-0">
                <i class="fa-solid fa-users me-2"></i> Gerir Utilizadores
            </a>
        <?php endif; ?>
    </div>
    <div class="list-group list-group-flush p-3 border-top border-secondary">
        <a href="<?= BASE_URL ?>/login/logout.php" class="text-light text-decoration-none">Sair</a>
    </div>
</aside>

<div id="page-content-wrapper">
    <div class="container-fluid">