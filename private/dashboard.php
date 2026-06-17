<?php

require_once __DIR__ . '\includes\header.php';
require_once __DIR__ . '\includes\sidebar.php';

$total_equipamentos = 0; // Placeholder
$total_ativos = 0;       // Placeholder
$total_manutencao = 0;   // Placeholder
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="text-secondary">Visão Geral do Parque Tecnológico</h2>
        <hr>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white h-100 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Total de Equipamentos</h5>
                <h1 class="display-4 fw-bold">
                    <?= $total_equipamentos; ?>
                </h1>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white h-100 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Equipamentos Ativos</h5>
                <h1 class="display-4 fw-bold">
                    <?= $total_ativos; ?>
                </h1>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-warning text-dark h-100 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Em Manutenção</h5>
                <h1 class="display-4 fw-bold">
                    <?= $total_manutencao; ?>
                </h1>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>