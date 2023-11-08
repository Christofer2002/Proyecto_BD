<?php
session_start(); // Iniciar sesión para acceder a las variables de sesión

// Vaciar todas las variables de sesión
$_SESSION = array();

// Destruir la sesión.
session_destroy();

// Redirigir al usuario a la página de login
header("Location: ../login.php");
exit;
?>
