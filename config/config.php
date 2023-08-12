<?php
define('DB_HOST', 'localhost'); // El host donde se encuentra tu servidor de MySQL
define('DB_USER', 'root'); // El nombre de usuario para acceder a la base de datos
define('DB_PASS', 'root'); // La contraseña del usuario
define('DB_NAME', 'proyecto_bd'); // El nombre de la base de datos que deseas utilizar

// Intenta conectarte a la base de datos
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verifica si hubo algún error en la conexión
if ($mysqli->connect_error) {
    // Si la conexión falló, muestra el mensaje de error y termina el script
    die('Error de conexión: ' . $mysqli->connect_error);
}
