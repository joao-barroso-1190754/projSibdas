<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: ../private/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clínica Madureira Loureiro</title>
    <link href="assets/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="assets/fontawesome/fontawesome.min.css" rel="stylesheet">
    <link href="assets/css/styles.css?v=<?= time(); ?>" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg fixed-top bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold text-light" href="#inicio">
                <i class="fa-solid fa-bone me-2"></i>Clínica Madureira Loureiro
            </a>
            <div>
                <ul class="navbar-nav">
                    <li><a class="nav-link text-light" href="#inicio">Início</a></li>
                    <li><a class="nav-link text-light" href="#especialidades">Especialidades</a></li>
                    <li><a class="nav-link text-light" href="#equipa">Equipa</a></li>
                    <li><a class="nav-link text-light" href="#contacto">Contactos</a></li>
                </ul>
            </div>

            <div class="dropdown">
                <button class="btn btn-outline-light btn-sm dropdown-toggle navbar-staff-link" type="button" id="loginDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-user-lock me-1"></i> Acesso Interno
                </button>
                <div class="dropdown-menu dropdown-menu-end p-4 shadow" style="width: 300px;"
                    aria-labelledby="loginDropdown">
                    <h5 class="mb-3">Login no Sistema</h5>

                    <?php if (isset($_SESSION['login_error'])): ?>
                        <div class="alert alert-danger py-1 px-2" role="alert">
                            <?= $_SESSION['login_error']; unset($_SESSION['login_error']); ?>
                        </div>
                    <?php endif; ?>

                    <form action="../login/login_process.php" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Entrar</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main>
        <section id="inicio" class="text-center" style="margin-top: 0;">
            <div id="hero" class="py-5">
                <div class="container py-5">
                    <div class="icon-circle">
                        <i class="fa-solid fa-bone"></i>
                    </div>
                    <h1 class="display-5 fw-bold" style="color: var(--text);">Clínica Madureira Loureiro</h1>
                    <p class="lead" style="color: var(--text); opacity: 0.85; max-width: 700px; margin: 1rem auto;">
                        Cuidados especializados em ortopedia e cirurgia, com uma equipa dedicada
                        a devolver-lhe o movimento e a qualidade de vida.
                    </p>
                    <a href="#contacto" class="btn btn-primary btn-lg mt-3 px-4 rounded-pill shadow-sm">
                        <i class="fa-solid fa-calendar-check me-2"></i>Marcar Consulta
                    </a>
                </div>
            </div>
        </section>

        <section id="especialidades" class="container py-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold" style="color: var(--primary);">As Nossas Especialidades</h2>
                <p class="text-muted">Cuidados clínicos centrados no diagnóstico preciso e na recuperação do paciente.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card service-card h-100 shadow-sm">
                        <div class="card-body p-4 text-center">
                            <div class="service-icon mx-auto mb-3">
                                <i class="fa-solid fa-bone"></i>
                            </div>
                            <h3 class="h5 fw-bold" style="color: var(--text);">Ortopedia Geral</h3>
                            <p class="text-muted mb-0">Diagnóstico e tratamento de lesões e patologias do sistema
                                músculo-esquelético, da consulta à recuperação.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card service-card h-100 shadow-sm">
                        <div class="card-body p-4 text-center">
                            <div class="service-icon mx-auto mb-3">
                                <i class="fa-solid fa-user-doctor"></i>
                            </div>
                            <h3 class="h5 fw-bold" style="color: var(--text);">Cirurgia Especializada</h3>
                            <p class="text-muted mb-0">Intervenções cirúrgicas planeadas ao detalhe, com acompanhamento
                                próximo antes e depois da operação.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card service-card h-100 shadow-sm">
                        <div class="card-body p-4 text-center">
                            <div class="service-icon mx-auto mb-3">
                                <i class="fa-solid fa-dumbbell"></i>
                            </div>
                            <h3 class="h5 fw-bold" style="color: var(--text);">Reabilitação</h3>
                            <p class="text-muted mb-0">Planos de recuperação acompanhados, ajustados a cada paciente e à
                                sua evolução clínica.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="equipa" class="py-5" style="background-color: var(--background);">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="fw-bold" style="color: var(--primary);">A Nossa Equipa</h2>
                    <p class="text-muted">Profissionais dedicados ao seu acompanhamento clínico.</p>
                </div>
                <div class="row g-4 justify-content-center">
                    <div class="col-md-4 text-center">
                        <div class="team-avatar">ML</div>
                        <h3 class="h5 fw-bold mb-0" style="color: var(--text);">Ortopedia</h3>
                        <p class="text-muted small">Direção Clínica</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="team-avatar">ML</div>
                        <h3 class="h5 fw-bold mb-0" style="color: var(--text);">Cirurgia</h3>
                        <p class="text-muted small">Direção Cirúrgica</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="team-avatar"><i class="fa-solid fa-user-nurse"></i></div>
                        <h3 class="h5 fw-bold mb-0" style="color: var(--text);">Enfermagem</h3>
                        <p class="text-muted small">Cuidados &amp; Acompanhamento</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="contacto" class="container py-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold" style="color: var(--primary);">Contactos &amp; Localização</h2>
                <p class="text-muted">Estamos disponíveis para esclarecer dúvidas e agendar a sua consulta.</p>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-md-4">
                    <div class="card service-card h-100 shadow-sm text-center">
                        <div class="card-body p-4">
                            <div class="service-icon mx-auto mb-3"><i class="fa-solid fa-location-dot"></i></div>
                            <h3 class="h6 fw-bold">Endereço</h3>
                            <p class="text-muted small mb-0">Rua Example, 123<br>4000-000 Porto</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card service-card h-100 shadow-sm text-center">
                        <div class="card-body p-4">
                            <div class="service-icon mx-auto mb-3"><i class="fa-solid fa-phone"></i></div>
                            <h3 class="h6 fw-bold">Telefone</h3>
                            <p class="text-muted small mb-0">+351 000 000 000<br>Seg-Sex, 9h-18h</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card service-card h-100 shadow-sm text-center">
                        <div class="card-body p-4">
                            <div class="service-icon mx-auto mb-3"><i class="fa-solid fa-envelope"></i></div>
                            <h3 class="h6 fw-bold">Email</h3>
                            <p class="text-muted small mb-0">geral@clinicaml.pt</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="py-4 mt-3">
        <div class="container text-center">
            <p class="mb-1 fw-bold text-light">Clínica Madureira Loureiro</p>
            <p class="small mb-0">&copy; <?= date('Y'); ?> Clínica Madureira Loureiro. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="assets/bootstrap/bootstrap.bundle.min.js"></script>
</body>

</html>