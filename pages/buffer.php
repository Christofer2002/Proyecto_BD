<?php
// Incluir para usar los datos de la constante
require_once '../config/config.php';

// Función para obtener datos actualizados y alertas según el HWM
// Función para obtener datos actualizados y alertas según el HWM
function getRealTimeData()
{
    $realTimeData = array();

    // Coloca aquí la lógica para obtener los datos actualizados
    $conn = oci_connect(DB_USER, DB_PASS, DB_HOST . '/' . DB_NAME);

    // Verifica si hubo algún error en la conexión
    if (!$conn) {
        $e = oci_error();
        die('Error de conexión: ' . $e['message']);
    }

    // Peticiones para extraer los contenidos del buffer en tiempo real
    $table_bufferData_name = 'v$sqlarea';
    $query_bufferData = "SELECT distinct vs.sql_text, vs.persistent_mem, to_char(to_date(vs.first_load_time,'YYYY-MM-DD/HH24:MI:SS'),'MM/DD HH24:MI:SS') first_load_time,
    vs.parsing_user_id , au.USERNAME parseuser
    FROM $table_bufferData_name vs , all_users au
    WHERE (parsing_user_id != 0)
        AND (au.user_id(+)=vs.parsing_user_id)
        AND (executions >= 1)";
    $stmtd = oci_parse($conn, $query_bufferData);
    oci_execute($stmtd);

    $table_bufferSizw_name = 'V$SGAINFO';
    $query_bufferSize = "SELECT bytes 
        FROM $table_bufferSizw_name
        WHERE name = 'Buffer Cache Size'";
    $stmtS = oci_parse($conn, $query_bufferSize);
    oci_execute($stmtS);

    // Se cierra la conexión con la base de datos
    oci_close($conn);

    $bufferData = array();
    $bufferSize = oci_fetch_assoc($stmtS);
    $bufferUsed = 0;

    if ($stmtd) {
        while ($row = oci_fetch_assoc($stmtd)) {
            $bufferData[] = $row;
        }
        oci_free_statement($stmtd);
        if (empty($bufferData)) {
            echo 'No hay preguntas en la tabla.';
        }
    } else {
        echo 'Error en la consulta: ' . oci_error($stmtd);
    }

    foreach ($bufferData as $dato) {
        $bufferUsed = $bufferUsed + intval($dato['PERSISTENT_MEM']);
    }

    // Almacena los datos en el array $realTimeData
    $realTimeData['bufferData'] = $bufferData;
    $realTimeData['bufferSize'] = $bufferSize;
    $realTimeData['bufferUsed'] = $bufferUsed;

    return $realTimeData;
}


// Verificar si se debe actualizar los datos o mostrar el contenido estático
$updateRealTimeData = isset($_GET['update']) && $_GET['update'] === 'true';

if ($updateRealTimeData) {
    $realTimeData = getRealTimeData();
    $bufferData = $realTimeData['bufferData'];
    $bufferSize = $realTimeData['bufferSize'];
    $bufferUsed = $realTimeData['bufferUsed'];
} else {
    $conn = oci_connect(DB_USER, DB_PASS, DB_HOST . '/' . DB_NAME);

    // Verifica si hubo algún error en la conexión
    if (!$conn) {
        $e = oci_error();
        die('Error de conexión: ' . $e['message']);
    }

    $table_bufferData_name = 'v$sqlarea';
    $query_bufferData = "SELECT distinct vs.sql_text, vs.persistent_mem, to_char(to_date(vs.first_load_time,'YYYY-MM-DD/HH24:MI:SS'),'MM/DD HH24:MI:SS') first_load_time,
    vs.parsing_user_id , au.USERNAME parseuser
    FROM $table_bufferData_name vs , all_users au
    WHERE (parsing_user_id != 0)
        AND (au.user_id(+)=vs.parsing_user_id)
        AND (executions >= 1)";
    $stmtd = oci_parse($conn, $query_bufferData);
    oci_execute($stmtd);

    $table_bufferSizw_name = 'V$SGAINFO';
    $query_bufferSize = "SELECT bytes 
    FROM $table_bufferSizw_name
    WHERE name = 'Buffer Cache Size'";
    $stmtS = oci_parse($conn, $query_bufferSize);
    oci_execute($stmtS);

    // Se cierra la conexión con la base de datos
    oci_close($conn);

    $bufferData = array();
    $bufferSize = oci_fetch_assoc($stmtS);
    $bufferUsed = 0;

    if ($stmtd) {
        while ($row = oci_fetch_assoc($stmtd)) {
            $bufferData[] = $row;
        }
        oci_free_statement($stmtd);
        if (empty($bufferData)) {
            $message = 'No hay preguntas en la tabla.';
        }
    } else {
        $message = 'Error en la consulta: ' . oci_error($stmtd);
    }

    foreach ($bufferData as $dato) {
        $bufferUsed = $bufferUsed + intval($dato['PERSISTENT_MEM']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buffer</title>
    <link href="/Proyecto_BD/assets/css/graphic.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body id="CuerpoB">
    <div>
        <?php include '../includes/menu.php'; ?>
    </div>
    <main class="main">
        <div id="real-time-data-container" class="table-graphic">
            <div class="scrollable-table">
                <table>
                    <thead>
                        <tr>
                            <th>DAY</th>
                            <th>TIME</th>
                            <th>SIZE</th>
                            <th>USED</th>
                            <th>PROCESS ID</th>
                            <th>USED</th>
                            <th id="sql">SQL TEXT</th>
                        </tr>
                    </thead>
                    <tbody class="">
                        <?php
                        $dataToUse = $updateRealTimeData ? $realTimeData['bufferData'] : $bufferData;

                        foreach ($dataToUse as $datosB) {
                            $sqlTextExists = false;
                            if ($updateRealTimeData) {
                                foreach ($realTimeData['bufferData'] as $realTimeDatum) {
                                    if ($realTimeDatum['SQL_TEXT'] === $datosB['SQL_TEXT']) {
                                        $sqlTextExists = true;
                                        break;
                                    }
                                }
                            }

                            if ($sqlTextExists) {
                        ?>
                                <tr>
                                    <td><?php echo isset($datosB['FIRST_LOAD_TIME']) ? substr($datosB['FIRST_LOAD_TIME'], 0, 5) : '' ?></td>
                                    <td><?php echo isset($datosB['FIRST_LOAD_TIME']) ? substr($datosB['FIRST_LOAD_TIME'], 6) : '' ?></td>
                                    <td><?php echo $bufferSize['BYTES'] ?></td>
                                    <td><?php echo $bufferUsed ?></td>
                                    <td><?php echo isset($datosB['PARSEUSER']) ? $datosB['PARSEUSER'] : '' ?></td>
                                    <td><?php echo isset($datosB['PERSISTENT_MEM']) ? $datosB['PERSISTENT_MEM'] : '' ?></td>
                                    <td><?php echo isset($datosB['SQL_TEXT']) ? $datosB['SQL_TEXT'] : '' ?></td>
                                </tr>
                        <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="chart-container">
                <canvas id="sharedSqlChart"></canvas>
            </div>
        </div>
        
    </main>

    <script>
        function updateRealTimeData() {
            var realTimeDataContainer = document.getElementById(
                "real-time-data-container"
            );
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "buffer.php?update=true", true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    realTimeDataContainer.innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        // Llamar a la función de actualización al cargar la página por primera vez
        updateRealTimeData();

        // Actualizar cada 10 segundos
        setInterval(updateRealTimeData, 10000);
    </script>
</body>

</html>