<?php

require_once __DIR__ . '/../../includes/header.php'; 

if ($_SESSION['user_perfil'] !== 'Admin') {
    header("Location: ../../dashboard.php");
    exit;
}

require_once __DIR__ . '/../../includes/sidebar.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $perfil = $_POST['perfil'];

    if (empty($nome) || empty($email) || empty($password) || empty($perfil)) {
        $error_msg = "Todos os campos são obrigatórios.";
    } else {
        // HASH THE PASSWORD!
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO utilizadores (nome, email, password, perfil) VALUES (:nome, :email, :pass, :perfil)");
            $stmt->execute([
                ':nome' => $nome,
                ':email' => $email,
                ':pass' => $hashed_password,
                ':perfil' => $perfil
            ]);

            $_SESSION['success_msg'] = "Utilizador criado com sucesso!";
            echo "<script>window.location.href='index.php';</script>"; exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error_msg = "Já existe um utilizador registado com este Email.";
            } else {
                $error_msg = "Erro ao guardar: " . $e->getMessage();
            }
        }
    }
}
?>

<div class="row mb-4"><div class="col-12"><h2 class="text-secondary">Novo Utilizador</h2><hr></div></div>

<div class="row">
    <div class="col-md-6 mx-auto">
        <?php if (isset($error_msg)): ?> <div class="alert alert-danger"><?= $error_msg; ?></div> <?php endif; ?>
        
        <div class="card shadow-sm border-0">
            <div class="card-body bg-light p-4">
                <form action="create.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nome</label>
                        <input type="text" class="form-control" name="nome" required value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" class="form-control" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Password Provisória</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Perfil de Acesso</label>
                        <select class="form-select" name="perfil" required>
                            <option value="Normal">Normal (Enfermeiro/Médico - Apenas Reporte)</option>
                            <option value="Tecnico">Técnico (Manutenção de Equipamentos)</option>
                            <option value="Admin">Admin (Acesso Total)</option>
                        </select>
                    </div>
                    <div class="text-end">
                        <a href="index.php" class="btn btn-outline-secondary me-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Criar Acesso</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>