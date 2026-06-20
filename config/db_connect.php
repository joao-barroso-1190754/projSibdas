<?php

require_once 'config.php';

$host = 'vsgate-s1.dei.isep.ipp.pt:10464';
$db_name = 'db1190754';
$username = '1190754'; 
$password = 'barroso_754'; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}