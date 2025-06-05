<?php
// Hibák megjelenítése
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $host = 'localhost';
    $dbname = 'kkzrt';
    $username = 'root';
    $password = '';

    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {
    die("Kapcsolódási hiba: " . $e->getMessage());
}

?>