<?php

define('DB_HOST', 'localhost'); // El host donde se encuentra tu servidor de Oracle
define('DB_USER', 'u1'); // El nombre de usuario para acceder a la base de datos
define('DB_PASS', 'u1'); // La contraseña del usuario
define('DB_NAME', 'xe'); // El nombre del servicio de Oracle que deseas utilizar

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
INNER JOIN categoria c ON cu.id_categoria = c.id
ORDER BY
    CASE c.descripcion
        WHEN 'Quality' THEN 1
        WHEN 'Risk' THEN 2
        WHEN 'Planning' THEN 3
    END,
    cu.id";
$stmt = oci_parse($conn, $query_cuestionary);
oci_execute($stmt);

//Peticion para el menu
$query_menu = "SELECT c.descripcion AS contenido_descripcion FROM contenido c";
$stmtm = oci_parse($conn, $query_menu);
oci_execute($stmtm);

// Peticion del buffer
$table_bufferData_name = 'v$sqlarea';
$query_bufferData = "SELECT distinct vs.sql_text, vs.persistent_mem, to_char(to_date(vs.first_load_time,'YYYY-MM-DD/HH24:MI:SS'),'MM/DD HH24:MI:SS') first_load_time,
vs.parsing_user_id , au.USERNAME parseuser
FROM $table_bufferData_name vs , all_users au
WHERE (parsing_user_id != 0)
    AND (au.user_id(+)=vs.parsing_user_id)
    AND (executions >= 1)";
$stmtd = oci_parse($conn, $query_bufferData);
oci_execute($stmtd);

// No olvides cerrar la conexión cuando hayas terminado
oci_close($conn);
