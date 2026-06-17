<?php

require_once __DIR__ . '\includes\header.php'; 
require_once __DIR__ . '\includes\sidebar.php';

$total_equipamentos = 0; // Placeholder
$total_ativos = 0;       // Placeholder
$total_manutencao = 0;   // Placeholder
?>


                <div class="row mb-4">
                    <div class="col-12">
                        <h2 class="text-secondary">Vista Geral</h2>
                        <hr>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card bg-primary text-light h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Total de Equipamentos</h5>
                                <h1 class="display-4 fw-bold">
                                    <a>O</a>
                                </h1>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-light h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Equipamentos Ativos</h5>
                                <h1 class="display-4 fw-bold">
                                    <a>O</a>
                                </h1>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-warning text-dark h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Em Manutenção</h5>
                                <h1 class="display-4 fw-bold">
                                    <a>O</a>
                                </h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="assets/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="assets/sweetalert2/sweetalert2.all.min.js"></script>
    <script src="assets/js/1190754.js">
    </script>

</body>

</html>