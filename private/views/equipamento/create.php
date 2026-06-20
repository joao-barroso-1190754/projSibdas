<?php
// backoffice/views/equipamentos/create.php

require_once __DIR__ . '/../../includes/header.php'; 
require_once __DIR__ . '/../../includes/sidebar.php';

$error_msg = null;

// 1. PRE-LOAD DATA FOR DROPDOWNS
try {
    // Get all active locations
    $stmtLoc = $pdo->query("SELECT id, edificio, servico_departamento, sala_gabinete FROM localizacoes WHERE apagado = FALSE ORDER BY edificio, servico_departamento");
    $localizacoes = $stmtLoc->fetchAll(PDO::FETCH_ASSOC);

    // Get all active equipment (to act as "parents" for components)
    $stmtEq = $pdo->query("SELECT id, codigo_interno, designacao FROM equipamentos WHERE apagado = FALSE ORDER BY codigo_interno");
    $equipamentos_principais = $stmtEq->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_msg = "Erro ao carregar dados de suporte: " . $e->getMessage();
}

// 2. PROCESS FORM SUBMISSION
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sanitize basic inputs
    $codigo = trim($_POST['codigo_interno']);
    $designacao = trim($_POST['designacao']);
    $categoria = trim($_POST['categoria']);
    $marca = trim($_POST['marca']);
    $modelo = trim($_POST['modelo']);
    $serial = trim($_POST['numero_serie']);
    $estado = trim($_POST['estado']);
    $criticidade = trim($_POST['criticidade']);
    $localizacao_id = $_POST['localizacao_id'];
    
    // Handle optional fields correctly (convert empty strings to NULL for the database)
    $parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;
    $ano_fabrico = !empty($_POST['ano_fabrico']) ? $_POST['ano_fabrico'] : null;
    $custo = !empty($_POST['custo_aquisicao']) ? $_POST['custo_aquisicao'] : null;
    $data_aquisicao = !empty($_POST['data_aquisicao']) ? $_POST['data_aquisicao'] : null;
    $observacoes = trim($_POST['observacoes']);

    // Mandatory fields check
    if (empty($codigo) || empty($designacao) || empty($estado) || empty($localizacao_id)) {
        $error_msg = "Por favor, preencha todos os campos obrigatórios (*).";
    } else {
        try {
            $sql = "INSERT INTO equipamentos 
                    (codigo_interno, designacao, categoria, marca, modelo, numero_serie, data_aquisicao, ano_fabrico, custo_aquisicao, estado, criticidade, observacoes, localizacao_id, parent_id) 
                    VALUES 
                    (:codigo, :desig, :cat, :marca, :modelo, :serial, :data_aq, :ano, :custo, :estado, :crit, :obs, :loc_id, :parent_id)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':codigo' => $codigo,
                ':desig' => $designacao,
                ':cat' => $categoria,
                ':marca' => $marca,
                ':modelo' => $modelo,
                ':serial' => $serial,
                ':data_aq' => $data_aquisicao,
                ':ano' => $ano_fabrico,
                ':custo' => $custo,
                ':estado' => $estado,
                ':crit' => $criticidade,
                ':obs' => $observacoes,
                ':loc_id' => $localizacao_id,
                ':parent_id' => $parent_id
            ]);

            $_SESSION['success_msg'] = "Equipamento/Componente registado com sucesso!";
            echo "<script>window.location.href='index.php';</script>"; 
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error_msg = "O Código Interno ou o Número de Série (para esta marca/modelo) já existe no sistema.";
            } else {
                $error_msg = "Erro ao guardar: " . $e->getMessage();
            }
        }
    }
}
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="text-secondary">🩺 Registar Equipamento / Componente</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="index.php">Equipamentos</a></li>
                <li class="breadcrumb-item active" aria-current="page">Registar</li>
            </ol>
        </nav>
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
                
                <form action="create.php" method="POST">
                    
                    <h5 class="text-primary border-bottom pb-2 mb-4">1. Identificação Técnica</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Código Interno <span class="text-danger">*</span></label>
                            <input type="text" class="form-control border-primary" name="codigo_interno" required
                                   placeholder="Ex: EQ-2024-001" value="<?= htmlspecialchars($_POST['codigo_interno'] ?? '') ?>">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold">Designação <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="designacao" required
                                   placeholder="Ex: Monitor Multiparamétrico" value="<?= htmlspecialchars($_POST['designacao'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Categoria</label>
                            <select class="form-select" name="categoria">
                                <option value="">Selecione...</option>
                                <option value="Monitorização">Monitorização</option>
                                <option value="Suporte de Vida">Suporte de Vida</option>
                                <option value="Laboratório">Laboratório</option>
                                <option value="Reagente/Consumível">Reagente / Consumível</option>
                                <option value="Acessório/Componente">Acessório / Componente</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Marca</label>
                            <input type="text" class="form-control" name="marca" value="<?= htmlspecialchars($_POST['marca'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Modelo</label>
                            <input type="text" class="form-control" name="modelo" value="<?= htmlspecialchars($_POST['modelo'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Número de Série</label>
                            <input type="text" class="form-control" name="numero_serie" value="<?= htmlspecialchars($_POST['numero_serie'] ?? '') ?>">
                        </div>
                    </div>

                    <h5 class="text-primary border-bottom pb-2 mb-4 mt-5">2. Relação de Componentes</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="form-label fw-bold text-muted">Este item é um componente/acessório de outro equipamento?</label>
                            <select class="form-select" name="parent_id">
                                <option value="">Não (É um equipamento principal ou item isolado)</option>
                                <?php foreach ($equipamentos_principais as $eq): ?>
                                    <option value="<?= $eq['id'] ?>">
                                        Anexar a: <?= htmlspecialchars($eq['codigo_interno'] . ' - ' . $eq['designacao']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Se for um sensor, cabo ou bateria, selecione a máquina principal acima.</small>
                        </div>
                    </div>

                    <h5 class="text-primary border-bottom pb-2 mb-4 mt-5">3. Operação e Localização</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Estado Atual <span class="text-danger">*</span></label>
                            <select class="form-select" name="estado" required>
                                <option value="Ativo">Ativo</option>
                                <option value="Em manutenção">Em manutenção</option>
                                <option value="Em calibração">Em calibração</option>
                                <option value="Inativo">Inativo</option>
                                <option value="Em quarentena">Em quarentena</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Criticidade</label>
                            <select class="form-select" name="criticidade">
                                <option value="Baixa">Baixa</option>
                                <option value="Média">Média</option>
                                <option value="Alta">Alta</option>
                                <option value="Suporte de vida">Suporte de vida</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Localização Atual <span class="text-danger">*</span></label>
                            <select class="form-select" name="localizacao_id" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($localizacoes as $loc): ?>
                                    <option value="<?= $loc['id'] ?>">
                                        <?= htmlspecialchars($loc['edificio'] . ' > ' . $loc['servico_departamento'] . ' (' . $loc['sala_gabinete'] . ')') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mt-5 text-end border-top pt-4">
                        <a href="index.php" class="btn btn-outline-secondary me-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary fw-bold">Registar Equipamento</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>