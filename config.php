<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
// adatbázis kapcsolat
try {
    $host = 'localhost';
    $dbname = 'kaposvar';
    $username = 'root';
    $password = '';

    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {
    die("Kapcsolódási hiba: " . $e->getMessage());
}

?>