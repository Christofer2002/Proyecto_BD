<?php
// Posibles consultas para obtener los datos de Shared SQL, Database Buffer y Redo Log Buffer
$querySharedSql = "SELECT sql_name, execution_time FROM shared_sql";
$queryDatabaseBuffer = "SELECT buffer_name, usage_percentage FROM database_buffer";
$queryRedoLogBuffer = "SELECT log_name, used_space FROM redo_log_buffer";

// Ejecutar las consultas y almacenar los resultados en arrays
$resultSharedSql = $mysqli->query($querySharedSql);
$resultDatabaseBuffer = $mysqli->query($queryDatabaseBuffer);
$resultRedoLogBuffer = $mysqli->query($queryRedoLogBuffer);

// Verificar si las consultas tuvieron éxito
if (!$resultSharedSql || !$resultDatabaseBuffer || !$resultRedoLogBuffer) {
    die('Error en las consultas: ' . $mysqli->error);
}

// Almacenar los resultados en arrays asociativos
$sharedSqlData = $resultSharedSql->fetch_all(MYSQLI_ASSOC);
$databaseBufferData = $resultDatabaseBuffer->fetch_all(MYSQLI_ASSOC);
$redoLogBufferData = $resultRedoLogBuffer->fetch_all(MYSQLI_ASSOC);

// Liberar los resultados
$resultSharedSql->free_result();
$resultDatabaseBuffer->free_result();
$resultRedoLogBuffer->free_result();
