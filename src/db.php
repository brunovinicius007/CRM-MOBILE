<?php
// src/db.php

$host = 'localhost';
$db   = 'iafinance_crm';
$user = 'root'; // Adjust as needed
$pass = '';     // Adjust as needed
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // In production, log this error and show a generic message
    // error_log($e->getMessage());
    // exit('Database connection error');
    // For development/demo, we might output it (careful with credentials)
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
