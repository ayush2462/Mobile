<?php
//$host = 'localhost';
//$db   = 'u807377921_mobile_checker';
//$user = 'u807377921_mobile';
//$pass = '28YyOO@jT';
//$charset = 'utf8mb4';
$host = 'localhost';
$db   = 'mobile_checker';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
