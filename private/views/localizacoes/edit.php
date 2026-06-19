<?php

require_once private_root . 'includes/header.php'; 
require_once private_root . 'includes/sidebar.php';

$error_msg = null;
$loc = null; 

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM localizacoes WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $loc = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$loc) {
            echo "<script>alert('Localização não encontrada!'); window.location.href='index.php';</script>";
            exit;
        }
    } catch (PDOException $e) {
        $error_msg = "Erro ao carregar os dados: " . $e->getMessage();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $id = $_POST['id'];
    $edificio = trim($_POST['edificio']);
    $piso = trim($_POST['piso']);
    $servico = trim($_POST['servico_departamento']);
    $sala = trim($_POST['sala_gabinete']);

    if (empty($edificio) || empty($servico)) {
        $error_msg = "Os campos 'Edifício' e 'Serviço/Departamento' são obrigatórios.";
        $loc = ['id' => $id, 'edificio' => $edificio, 'piso' => $piso, 'servico_departamento' => $servico, 'sala_gabinete' => $sala];
    } else {
        try {
            $sql = "UPDATE localizacoes 
                    SET edificio = :edificio, 
                        piso = :piso, 
                        servico_departamento = :servico, 
                        sala_gabinete = :sala 
                    WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':edificio' => $edificio,
                ':piso' => $piso,
                ':servico' => $servico,
                ':sala' => $sala,
                ':id' => $id
            ]);

            $_SESSION['success_msg'] = "Localização atualizada com sucesso!";
            echo "<script>window.location.href='index.php';</script>"; 
            exit;

        } catch (PDOException $e) {
            $error_msg = "Erro ao atualizar a base de dados: " . $e->getMessage();
            $loc = ['id' => $id, 'edificio' => $edificio, 'piso' => $piso, 'servico_departamento' => $servico, 'sala_gabinete' => $sala];
        }
    }
}
if (!$loc && !isset($error_msg)) {
    echo "<script>window.location.href='index.php';</script>";
    exit;
}
?>

                <div class="row mb-4">
                    <div class="col-12">
                        <h2 class="text-secondary">📍 Editar Localização</h2>
                        <hr>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 mx-auto">

                        <?php if ($error_msg): ?>
            <div class="alert alert-danger shadow-sm"><?= $error_msg; ?></div>
        <?php endif; ?>

                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white">
                                <h5 class="mb-0 mt-2">Atualizar Detalhes do Espaço Físico</h5>
                            </div>
                            <div class="card-body bg-light">

                                <form action="edit.php" method="POST">
                    
                    <input type="hidden" name="id" value="<?= htmlspecialchars($loc['id']); ?>">

                                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="edificio" class="form-label fw-bold">Edifício <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edificio" name="edificio" required 
                                   value="<?= htmlspecialchars($loc['edificio']); ?>">
                        </div>
                        
                        <div class="col-md-4">
                            <label for="piso" class="form-label fw-bold">Piso</label>
                            <input type="text" class="form-control" id="piso" name="piso" 
                                   value="<?= htmlspecialchars($loc['piso']); ?>">
                        </div>

                        <div class="col-md-6 mt-4">
                            <label for="servico_departamento" class="form-label fw-bold">Serviço / Departamento <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="servico_departamento" name="servico_departamento" required 
                                   value="<?= htmlspecialchars($loc['servico_departamento']); ?>">
                        </div>

                        <div class="col-md-6 mt-4">
                            <label for="sala_gabinete" class="form-label fw-bold">Sala / Gabinete</label>
                            <input type="text" class="form-control" id="sala_gabinete" name="sala_gabinete" 
                                   value="<?= htmlspecialchars($loc['sala_gabinete']); ?>">
                        </div>
                    </div>

                                    <div class="mt-5 text-end">
                        <a href="index.php" class="btn btn-outline-secondary me-2">Cancelar</a>
                        <button type="submit" class="btn btn-warning fw-bold">Atualizar Localização</button>
                    </div>
                </form>

                            </div>
                        </div>
                    </div>

    <?php
require_once private_root . 'includes/footer.php';
?>