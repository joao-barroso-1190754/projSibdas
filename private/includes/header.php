<?php
session_start();

require_once __DIR__ . '/../../config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "/public/index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    <link href="<?= BASE_URL ?>/private/assets/bootstrap/bootstrap.min.css?v=<?= time(); ?>" rel="stylesheet">
    <link href="<?= BASE_URL ?>/private/assets/css/styles.css?v=<?= time(); ?>" rel="stylesheet">
    <link href="<?= BASE_URL ?>/private/assets/fontawesome/fontawesome.min.css?v=<?= time(); ?>" rel="stylesheet">
    <link href="<?= BASE_URL ?>/private/assets/sweetalert2/sweetalert2.min.css?v=<?= time(); ?>" rel="stylesheet">
</head>

<body>

    <nav id="top-navbar" class="navbar fixed-top navbar-expand-lg bg-primary shadow">
        <div class="container-fluid">
            <span class="navbar-brand text-light fw-bold m-0" id="menu-toggle"
                title="Clique para esconder/mostrar o menu">
                <i class="fa fa-bars"></i> Menu
            </span>
        </div>
    </nav>

    <main id="wrapper">