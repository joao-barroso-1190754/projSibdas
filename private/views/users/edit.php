<?php
$current_path = $_SERVER['PHP_SELF']; 
function isActive($needle, $path) {
    return str_contains($path, $needle) ? 'active' : '';
}
require_once __DIR__ . '/../../includes/header.php'; 

if ($_SESSION['user_perfil'] !== 'Admin') {
    header("Location: ../../dashboard.php");
    exit;
}

require_once __DIR__ . '/../../includes/sidebar.php';

$error_msg = null;
$user_data = null;

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("SELECT id, nome, email, perfil FROM utilizadores WHERE id = :id");
        $stmt->execute([':id' => $_GET['id']]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user_data) {
            echo "<script>window.location.href='index.php';</script>"; exit;
        }
    } catch (PDOException $e) {
        $error_msg = "Erro ao carregar dados: " . $e->getMessage();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $perfil = $_POST['perfil'];
    $new_password = $_POST['password']; 

    if (empty($nome) || empty($email) || empty($perfil)) {
        $error_msg = "Nome, Email e Perfil são obrigatórios.";
        $user_data = $_POST;
    } else {
        try {
            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $sql = "UPDATE utilizadores SET nome = :nome, email = :email, perfil = :perfil, password = :pass WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':nome' => $nome, ':email' => $email, ':perfil' => $perfil, ':pass' => $hashed_password, ':id' => $id]);
            } else {
                $sql = "UPDATE utilizadores SET nome = :nome, email = :email, perfil = :perfil WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':nome' => $nome, ':email' => $email, ':perfil' => $perfil, ':id' => $id]);
            }

            if ($id == $_SESSION['user_id']) {
                $_SESSION['user_nome'] = $nome;
            }

            $_SESSION['success_msg'] = "Dados do utilizador atualizados com sucesso!";
            echo "<script>window.location.href='index.php';</script>"; exit;

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error_msg = "Este email já está a ser utilizado por outra conta.";
            } else {
                $error_msg = "Erro ao atualizar: " . $e->getMessage();
            }
            $user_data = $_POST;
        }
    }
}

if (!$user_data && !$error_msg) { echo "<script>window.location.href='index.php';</script>"; exit; }
?>

<div class="row mb-4"><div class="col-12"><h2 class="text-secondary">Editar Utilizador</h2><hr></div></div>

<div class="row">
    <div class="col-md-6 mx-auto">
        <?php if ($error_msg): ?> <div class="alert alert-danger"><?= $error_msg; ?></div> <?php endif; ?>
        
        <div class="card shadow-sm border-0">
            <div class="card-body bg-light p-4">
                <form action="edit.php" method="POST">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($user_data['id']); ?>">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nome</label>
                        <input type="text" class="form-control" name="nome" required value="<?= htmlspecialchars($user_data['nome']); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" class="form-control" name="email" required value="<?= htmlspecialchars($user_data['email']); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Perfil de Acesso</label>
                        <select class="form-select" name="perfil" required <?= ($user_data['id'] == $_SESSION['user_id']) ? 'disabled' : '' ?>>
                            <option value="Normal" <?= ($user_data['perfil'] == 'Normal') ? 'selected' : '' ?>>Normal (Apenas Reporte)</option>
                            <option value="Tecnico" <?= ($user_data['perfil'] == 'Tecnico') ? 'selected' : '' ?>>Técnico (Manutenção)</option>
                            <option value="Admin" <?= ($user_data['perfil'] == 'Admin') ? 'selected' : '' ?>>Admin (Acesso Total)</option>
                        </select>
                        <?php if ($user_data['id'] == $_SESSION['user_id']): ?>
                            <small class="text-danger">Não pode alterar o seu próprio perfil.</small>
                            <input type="hidden" name="perfil" value="Admin">
                        <?php endif; ?>
                    </div>

                    <hr class="my-4">
                    <h6 class="text-muted mb-3">Segurança</h6>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Nova Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Deixe em branco para manter a atual">
                    </div>

                    <div class="text-end">
                        <a href="index.php" class="btn btn-outline-secondary me-2">Cancelar</a>
                        <button type="submit" class="btn btn-warning fw-bold">Atualizar Conta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>