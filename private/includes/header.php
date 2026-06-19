<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /PROJECTO/frontoffice/index.php");
    exit;
}

require_once private_root . 'config/db_connect.php';
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    <link href="/PROJECTO/private/assets/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="/PROJECTO/private/assets/css/styles.css" rel="stylesheet">
    <link href="/PROJECTO/private/assets/fontawesome/fontawesome.min.css" rel="stylesheet">
    <link href="/PROJECTO/private/assets/sweetalert2/sweetalert2.min.css" rel="stylesheet">
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