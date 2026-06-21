<?php
$current_path = $_SERVER['PHP_SELF']; 
function isActive($needle, $path) {
    return str_contains($path, $needle) ? 'active' : '';
}
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';

if ($_SESSION['user_perfil'] !== 'Admin') {
    header("Location: " . BASE_URL . "/private/views/fornecedores/index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_empresa = trim($_POST['nome_empresa']);
    $nif = trim($_POST['nif']);
    $contacto_telefonico = trim($_POST['contacto_telefonico']);
    $email = trim($_POST['email']);
    $morada = trim($_POST['morada']);
    $website = trim($_POST['website']);
    $pessoa_contacto = trim($_POST['pessoa_contacto']);
    $telefone_contacto = trim($_POST['telefone_contacto']);
    $tipo_fornecedor = trim($_POST['tipo_fornecedor']);
    $observacoes = trim($_POST['observacoes']);

    if (empty($nome_empresa)) {
        $error_msg = "O campo 'Nome da Empresa' é obrigatório.";
    } else {
        try {
            $sql = "INSERT INTO fornecedores 
                    (nome_empresa, nif, contacto_telefonico, email, morada, website, pessoa_contacto, telefone_contacto, tipo_fornecedor, observacoes) 
                    VALUES 
                    (:nome_empresa, :nif, :contacto, :email, :morada, :website, :pessoa, :telefone_pessoa, :tipo, :obs)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome_empresa' => $nome_empresa,
                ':nif' => empty($nif) ? null : $nif,
                ':contacto' => $contacto_telefonico,
                ':email' => $email,
                ':morada' => $morada,
                ':website' => $website,
                ':pessoa' => $pessoa_contacto,
                ':telefone_pessoa' => $telefone_contacto,
                ':tipo' => $tipo_fornecedor,
                ':obs' => $observacoes
            ]);

            $_SESSION['success_msg'] = "Fornecedor registado com sucesso!";
            echo "<script>window.location.href='index.php';</script>";
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error_msg = "Já existe um fornecedor registado com este NIF.";
            } else {
                $error_msg = "Erro ao guardar: " . $e->getMessage();
            }
        }
    }
}
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="text-secondary">🏢 Novo Fornecedor</h2>
        <hr>
    </div>
</div>

<div class="row">
    <div class="col-xl-10 mx-auto">

        <?php if (isset($error_msg)): ?>
            <div class="alert alert-danger shadow-sm"><?= $error_msg; ?></div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="mb-0 mt-2">Dados da Entidade</h5>
            </div>
            <div class="card-body bg-light">

                <form action="create.php" method="POST">

                    <h6 class="text-primary mb-3">Informação Principal</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="nome_empresa" class="form-label fw-bold">Nome da Empresa <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nome_empresa" name="nome_empresa" required
                                value="<?= isset($_POST['nome_empresa']) ? htmlspecialchars($_POST['nome_empresa']) : '' ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="nif" class="form-label fw-bold">NIF</label>
                            <input type="text" class="form-control" id="nif" name="nif" maxlength="20"
                                value="<?= isset($_POST['nif']) ? htmlspecialchars($_POST['nif']) : '' ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="tipo_fornecedor" class="form-label fw-bold">Tipo de Entidade</label>
                            <select class="form-select" id="tipo_fornecedor" name="tipo_fornecedor">
                                <option value="">Selecione...</option>
                                <option value="Fabricante" <?= (isset($_POST['tipo_fornecedor']) && $_POST['tipo_fornecedor'] == 'Fabricante') ? 'selected' : '' ?>>Fabricante</option>
                                <option value="Distribuidor/Comercial" <?= (isset($_POST['tipo_fornecedor']) && $_POST['tipo_fornecedor'] == 'Distribuidor/Comercial') ? 'selected' : '' ?>>
                                    Distribuidor / Comercial</option>
                                <option value="Assistência Técnica" <?= (isset($_POST['tipo_fornecedor']) && $_POST['tipo_fornecedor'] == 'Assistência Técnica') ? 'selected' : '' ?>>Assistência
                                    Técnica</option>
                                <option value="Fornecedor de Consumíveis" <?= (isset($_POST['tipo_fornecedor']) && $_POST['tipo_fornecedor'] == 'Fornecedor de Consumíveis') ? 'selected' : '' ?>>
                                    Fornecedor de Consumíveis</option>
                            </select>
                        </div>
                    </div>

                    <h6 class="text-primary mb-3 border-top pt-4">Contactos e Localização</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="contacto_telefonico" class="form-label fw-bold">Telefone Geral</label>
                            <input type="text" class="form-control" id="contacto_telefonico" name="contacto_telefonico"
                                value="<?= isset($_POST['contacto_telefonico']) ? htmlspecialchars($_POST['contacto_telefonico']) : '' ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="email" class="form-label fw-bold">Email Geral</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="website" class="form-label fw-bold">Website</label>
                            <input type="url" class="form-control" id="website" name="website" placeholder="https://"
                                value="<?= isset($_POST['website']) ? htmlspecialchars($_POST['website']) : '' ?>">
                        </div>
                        <div class="col-12">
                            <label for="morada" class="form-label fw-bold">Morada Completa</label>
                            <input type="text" class="form-control" id="morada" name="morada"
                                value="<?= isset($_POST['morada']) ? htmlspecialchars($_POST['morada']) : '' ?>">
                        </div>
                    </div>

                    <h6 class="text-primary mb-3 border-top pt-4">Pessoa de Contacto Direto</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="pessoa_contacto" class="form-label fw-bold">Nome do Contacto</label>
                            <input type="text" class="form-control" id="pessoa_contacto" name="pessoa_contacto"
                                value="<?= isset($_POST['pessoa_contacto']) ? htmlspecialchars($_POST['pessoa_contacto']) : '' ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="telefone_contacto" class="form-label fw-bold">Telefone Direto</label>
                            <input type="text" class="form-control" id="telefone_contacto" name="telefone_contacto"
                                value="<?= isset($_POST['telefone_contacto']) ? htmlspecialchars($_POST['telefone_contacto']) : '' ?>">
                        </div>
                    </div>

                    <h6 class="text-primary mb-3 border-top pt-4">Informação Adicional</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label for="observacoes" class="form-label fw-bold">Observações</label>
                            <textarea class="form-control" id="observacoes" name="observacoes"
                                rows="3"><?= isset($_POST['observacoes']) ? htmlspecialchars($_POST['observacoes']) : '' ?></textarea>
                        </div>
                    </div>

                    <div class="mt-5 text-end border-top pt-4">
                        <a href="index.php" class="btn btn-outline-secondary me-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary fw-bold">Registar Fornecedor</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>