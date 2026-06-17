<?php

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $edificio = trim($_POST['edificio']);
    $piso = trim($_POST['piso']);
    $servico = trim($_POST['servico_departamento']);
    $sala = trim($_POST['sala_gabinete']);

    if (empty($edificio) || empty($servico)) {
        $error_msg = "Os campos 'Edifício' e 'Serviço/Departamento' são obrigatórios.";
    } else {
        try {
            $sql = "INSERT INTO localizacoes (edificio, piso, servico_departamento, sala_gabinete) 
                    VALUES (:edificio, :piso, :servico, :sala)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':edificio' => $edificio,
                ':piso' => $piso,
                ':servico' => $servico,
                ':sala' => $sala
            ]);

            $_SESSION['success_msg'] = "Localização criada com sucesso!";
            echo "<script>window.location.href = 'index.php';</script>";
            exit;

        } catch (PDOException $e) {
            $error_msg = "Erro ao guardar na base de dados: " . $e->getMessage();
        }
    }
}
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="text-secondary">📍 Nova Localização</h2>
        <hr>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">

        <?php if (isset($error_msg)): ?>
            <div class="alert alert-danger shadow-sm">
                <?= $error_msg; ?>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="mb-0 mt-2">Detalhes do Espaço Físico</h5>
            </div>
            <div class="card-body bg-light">

                <form action="create.php" method="POST">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="edificio" class="form-label fw-bold">Edifício <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edificio" name="edificio" required
                                placeholder="Ex: Pavilhão Central, Edifício Sul"
                                value="<?= isset($_POST['edificio']) ? htmlspecialchars($_POST['edificio']) : '' ?>">
                        </div>

                        <div class="col-md-4">
                            <label for="piso" class="form-label fw-bold">Piso</label>
                            <input type="text" class="form-control" id="piso" name="piso" placeholder="Ex: R/C, Piso 2"
                                value="<?= isset($_POST['piso']) ? htmlspecialchars($_POST['piso']) : '' ?>">
                        </div>

                        <div class="col-md-6 mt-4">
                            <label for="servico_departamento" class="form-label fw-bold">Serviço / Departamento <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="servico_departamento"
                                name="servico_departamento" required placeholder="Ex: Cuidados Intensivos, Imagiologia"
                                value="<?= isset($_POST['servico_departamento']) ? htmlspecialchars($_POST['servico_departamento']) : '' ?>">
                        </div>

                        <div class="col-md-6 mt-4">
                            <label for="sala_gabinete" class="form-label fw-bold">Sala / Gabinete</label>
                            <input type="text" class="form-control" id="sala_gabinete" name="sala_gabinete"
                                placeholder="Ex: Box 4, Gabinete 12"
                                value="<?= isset($_POST['sala_gabinete']) ? htmlspecialchars($_POST['sala_gabinete']) : '' ?>">
                        </div>
                    </div>

                    <div class="mt-5 text-end">
                        <a href="index.php" class="btn btn-outline-secondary me-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary fw-bold">Guardar Localização</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
<?php
require_once __DIR__ . '/../../includes/footer.php';
?>