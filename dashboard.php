<?php

require_once 'config.php';  
// config 

// bejelentkezés ellenörzés
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");  
    // ha nincs bejelentkezve átirányítás a login.php-ra
    exit();
}

echo "Üdvözöljük, " . $_SESSION['username'] . "!"; 
// felhasználónév megjelenítés
?>
