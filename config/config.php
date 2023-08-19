<?php

define('DB_HOST', 'localhost'); // El host donde se encuentra tu servidor de Oracle
define('DB_USER', 'root'); // El nombre de usuario para acceder a la base de datos
define('DB_PASS', 'root'); // La contraseña del usuario
define('DB_NAME', 'orcl'); // El nombre del servicio de Oracle que deseas utilizar

// Intenta conectarte a la base de datos Oracle
$conn = oci_connect(DB_USER, DB_PASS, DB_HOST . '/' . DB_NAME);

// Verifica si hubo algún error en la conexión
if (!$conn) {
    // Si la conexión falló, muestra el mensaje de error y termina el script
    $e = oci_error();
    die('Error de conexión: ' . $e['message']);
}

// Aquí puedes realizar operaciones con la base de datos Oracle
$query_cuestionary = "SELECT c.descripcion AS categoria, cu.id AS cuestionario_id, cu.pregunta
FROM cuestionario cu
INNER JOIN contenido cont ON cu.id_contenido = cont.id
INNER JOIN categoria c ON cu.id_categoria = c.id";
$stmt = oci_parse($conn, $query_cuestionary);
oci_execute($stmt);

$query_menu = "SELECT c.descripcion AS contenido_descripcion FROM contenido c";
$stmtm = oci_parse($conn, $query_menu);
oci_execute($stmtm);

// No olvides cerrar la conexión cuando hayas terminado
oci_close($conn);
