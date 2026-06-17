<aside id="sidebar-wrapper" class="text-light shadow d-flex flex-column" style="min-height: calc(100vh - 60px);">
    <div class="p-3 border-bottom border-secondary text-center mt-2">
        <small class="d-block text-muted">Bem-vindo(a),</small>
        <strong><?= htmlspecialchars($_SESSION['user_nome']); ?></strong>
        <span class="badge bg-secondary d-block mt-2"><?= htmlspecialchars($_SESSION['user_perfil']); ?></span>
    </div>
    <div class="list-group list-group-flush mt-2">
        <a href="/PROJECTO/backoffice/dashboard.php"
            class="list-group-item list-group-item-dark p-3 border-0">📊Dashboard</a>
        <a href="/PROJECTO/backoffice/views/equipamentos/index.php"
            class="list-group-item list-group-item-dark p-3 border-0">🩺 Equipamentos</a>
        <a href="/PROJECTO/backoffice/views/localizacoes/index.php"
            class="list-group-item list-group-item-dark p-3 border-0">📍 Localizações</a>
        <a href="/PROJECTO/backoffice/views/fornecedores/index.php"
            class="list-group-item list-group-item-dark p-3 border-0">🏢 Fornecedores</a>

        <?php if ($_SESSION['user_perfil'] === 'Admin'): ?>
            <a href="/PROJECTO/backoffice/views/users/index.php" class="list-group-item list-group-item-dark p-3 border-0 text-warning">⚙️ Gerir Utilizadores</a>
        <?php endif; ?>
    </div>
    <div class="list-group list-group-flush p-3 border-top border-secondary">
        <a href="/PROJECTO/login/logout.php" class="text-light text-decoration-none">Sair</a>
    </div>
</aside>

<div id="page-content-wrapper">
    <div class="container-fluid">