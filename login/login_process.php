<?php

session_start();
require_once '../config/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    try {
        $stmt = $pdo->prepare("SELECT id, nome, email, password, perfil FROM utilizadores WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nome'] = $user['nome'];
            $_SESSION['user_perfil'] = $user['perfil'];

            header("Location: ../backoffice/dashboard.php");
            exit;
        } else {
            $_SESSION['login_error'] = "Credenciais inválidas.";
            header("Location: ../frontoffice/index.php"); 
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['login_error'] = "Erro no sistema: " . $e->getMessage();
        header("Location: ../frontoffice/index.php"); 
        exit;
    }
} else {
    header("Location: ../frontoffice/index.php"); 
    exit;
}
?>