<?php
session_start();

// töröljük az összes session változót
$_SESSION = array();

// töröljük a session cookie-t
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

session_destroy();

// átirányítása bejelentkező oldalra
header("Location: login.php");
exit();
?>