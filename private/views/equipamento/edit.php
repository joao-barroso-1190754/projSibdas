<?php
// backoffice/views/equipamentos/edit.php

require_once __DIR__ . '/../../includes/header.php'; 
require_once __DIR__ . '/../../includes/sidebar.php';

$error_msg = null;
$eq = null;
$localizacoes = [];
$equipamentos_principais = [];

// 1. INITIAL LOAD (GET)
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        // Fetch the specific equipment
        $stmt = $pdo->prepare("SELECT * FROM equipamentos WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $eq = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$eq) {
            echo "<script>alert('Equipamento não encontrado!'); window.location.href='index.php';</script>";
            exit;
        }

        // Fetch dropdown data
        $localizacoes = $pdo->query("SELECT id, edificio, servico_departamento, sala_gabinete FROM localizacoes WHERE apagado = FALSE")->fetchAll();
        
        // Fetch parent equipment (excluding itself so it can't be its own parent)
        $stmtEq = $pdo->prepare("SELECT id, codigo_interno, designacao FROM equipamentos WHERE apagado = FALSE AND id != :id");
        $stmtEq->execute([':id' => $id]);
        $equipamentos_principais = $stmtEq->fetchAll();

    } catch (PDOException $e) {
        $error_msg = "Erro ao carregar dados: " . $e->getMessage();
    }
}

// 2. FORM SUBMISSION (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $id = $_POST['id'];
    $codigo = trim($_POST['codigo_interno']);
    $designacao = trim($_POST['designacao']);
    $categoria = trim($_POST['categoria']);
    $marca = trim($_POST['marca']);
    $modelo = trim($_POST['modelo']);
    $serial = trim($_POST['numero_serie']);
    $estado = trim($_POST['estado']);
    $criticidade = trim($_POST['criticidade']);
    $localizacao_id = $_POST['localizacao_id'];
    
    $parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;
    $observacoes = trim($_POST['observacoes']);

    if (empty($codigo) || empty($designacao) || empty($estado) || empty($localizacao_id)) {
        $error_msg = "Por favor, preencha todos os campos obrigatórios (*).";
        $eq = $_POST; // Keep data
    } else {
        try {
            $sql = "UPDATE equipamentos 
                    SET codigo_interno = :codigo, designacao = :desig, categoria = :cat, 
                        marca = :marca, modelo = :modelo, numero_serie = :serial, 
                        estado = :estado, criticidade = :crit, observacoes = :obs, 
                        localizacao_id = :loc_id, parent_id = :parent_id 
                    WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':codigo' => $codigo, ':desig' => $designacao, ':cat' => $categoria,
                ':marca' => $marca, ':modelo' => $modelo, ':serial' => $serial,
                ':estado' => $estado, ':crit' => $criticidade, ':obs' => $observacoes,
                ':loc_id' => $localizacao_id, ':parent_id' => $parent_id, ':id' => $id
            ]);

            $_SESSION['success_msg'] = "Equipamento atualizado com sucesso!";
            echo "<script>window.location.href='index.php';</script>"; 
            exit;

        } catch (PDOException $e) {
            $error_msg = "Erro ao atualizar: " . $e->getMessage();
            $eq = $_POST;
        }
    }
}

if (!$eq && !isset($error_msg)) { echo "<script>window.location.href='index.php';</script>"; exit; }
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="text-secondary">🩺 Editar Equipamento</h2>
        <hr>
    </div>
</div>

<div class="row">
    <div class="col-xl-10 mx-auto">
        <?php if ($error_msg): ?>
            <div class="alert alert-danger shadow-sm"><?= $error_msg; ?></div>
        <?php endif; ?>

        <div class="card shadow-sm border-0 mb-5">
            <div class="card-body bg-light p-4">
                
                <form action="edit.php" method="POST">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($eq['id']); ?>">
                    
                    <h5 class="text-primary border-bottom pb-2 mb-4">Identificação Técnica</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Código Interno <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="codigo_interno" required
                                   value="<?= htmlspecialchars($eq['codigo_interno']); ?>">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold">Designação <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="designacao" required
                                   value="<?= htmlspecialchars($eq['designacao']); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Categoria</label>
                            <select class="form-select" name="categoria">
                                <option value="">Selecione...</option>
                                <option value="Monitorização" <?= ($eq['categoria'] == 'Monitorização') ? 'selected' : '' ?>>Monitorização</option>
                                <option value="Suporte de Vida" <?= ($eq['categoria'] == 'Suporte de Vida') ? 'selected' : '' ?>>Suporte de Vida</option>
                                <option value="Laboratório" <?= ($eq['categoria'] == 'Laboratório') ? 'selected' : '' ?>>Laboratório</option>
                                <option value="Reagente/Consumível" <?= ($eq['categoria'] == 'Reagente/Consumível') ? 'selected' : '' ?>>Reagente/Consumível</option>
                                <option value="Acessório/Componente" <?= ($eq['categoria'] == 'Acessório/Componente') ? 'selected' : '' ?>>Acessório/Componente</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Marca</label>
                            <input type="text" class="form-control" name="marca" value="<?= htmlspecialchars($eq['marca'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Modelo</label>
                            <input type="text" class="form-control" name="modelo" value="<?= htmlspecialchars($eq['modelo'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Número de Série</label>
                            <input type="text" class="form-control" name="numero_serie" value="<?= htmlspecialchars($eq['numero_serie'] ?? ''); ?>">
                        </div>
                    </div>

                    <h5 class="text-primary border-bottom pb-2 mb-4 mt-5">Hierarquia e Operação</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold text-muted">Item Principal (Se for componente)</label>
                            <select class="form-select" name="parent_id">
                                <option value="">Não aplicável</option>
                                <?php foreach ($equipamentos_principais as $parent): ?>
                                    <option value="<?= $parent['id'] ?>" <?= ($eq['parent_id'] == $parent['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($parent['codigo_interno'] . ' - ' . $parent['designacao']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Estado Atual <span class="text-danger">*</span></label>
                            <select class="form-select" name="estado" required>
                                <option value="Ativo" <?= ($eq['estado'] == 'Ativo') ? 'selected' : '' ?>>Ativo</option>
                                <option value="Em manutenção" <?= ($eq['estado'] == 'Em manutenção') ? 'selected' : '' ?>>Em manutenção</option>
                                <option value="Inativo" <?= ($eq['estado'] == 'Inativo') ? 'selected' : '' ?>>Inativo</option>
                                <option value="Abatido" <?= ($eq['estado'] == 'Abatido') ? 'selected' : '' ?>>Abatido</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Criticidade</label>
                            <select class="form-select" name="criticidade">
                                <option value="Baixa" <?= ($eq['criticidade'] == 'Baixa') ? 'selected' : '' ?>>Baixa</option>
                                <option value="Média" <?= ($eq['criticidade'] == 'Média') ? 'selected' : '' ?>>Média</option>
                                <option value="Alta" <?= ($eq['criticidade'] == 'Alta') ? 'selected' : '' ?>>Alta</option>
                                <option value="Suporte de vida" <?= ($eq['criticidade'] == 'Suporte de vida') ? 'selected' : '' ?>>Suporte de vida</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Localização Atual <span class="text-danger">*</span></label>
                            <select class="form-select" name="localizacao_id" required>
                                <?php foreach ($localizacoes as $loc): ?>
                                    <option value="<?= $loc['id'] ?>" <?= ($eq['localizacao_id'] == $loc['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($loc['edificio'] . ' > ' . $loc['servico_departamento']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 mt-3">
                            <label class="form-label fw-bold">Observações</label>
                            <textarea class="form-control" name="observacoes" rows="2"><?= htmlspecialchars($eq['observacoes'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <a href="index.php" class="btn btn-outline-secondary me-2">Cancelar</a>
                        <button type="submit" class="btn btn-warning fw-bold">Atualizar Equipamento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>