<?php
// Incluir para usar los datos de la constante
require_once '../config/config.php';

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
    $query_bufferData = "SELECT distinct vs.sql_text, vs.persistent_mem, to_char(to_date(vs.first_load_time,'YYYY-MM-DD/HH24:MI:SS'),'DD/MM/YYYY HH24:MI:SS') first_load_time,
    vs.parsing_user_id , au.USERNAME parseuser
    FROM $table_bufferData_name vs , all_users au
    WHERE (parsing_user_id != 0)
        AND (au.user_id(+)=vs.parsing_user_id)
        AND (executions >= 1)";
    $stmtd = oci_parse($conn, $query_bufferData);
    oci_execute($stmtd);

    //Extraer el tamaño del buffer
    $table_bufferSize_name = 'V$SGAINFO';
    $query_bufferSize = "SELECT ((bytes/1000)/1000) AS megabytes 
        FROM $table_bufferSize_name
        WHERE name = 'Buffer Cache Size'";
    $stmtS = oci_parse($conn, $query_bufferSize);
    oci_execute($stmtS);

    //Extraer el usado del buffer
    $table_bufferUsed_name = 'V$Bh';
    $query_bufferUsed = "SELECT (((COUNT(*) * 8192)/1000)/1000)  AS megabytes 
        FROM $table_bufferUsed_name
        WHERE status = 'xcur' OR status = 'cr' OR status = 'read'";
    $stmtU = oci_parse($conn, $query_bufferUsed);
    oci_execute($stmtU);


    // Se cierra la conexión con la base de datos
    oci_close($conn);

    $bufferData = array();
    $bufferSize = oci_fetch_assoc($stmtS);
    $bufferUsed = oci_fetch_assoc($stmtU);

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
    $query_bufferData = "SELECT distinct vs.sql_text, vs.persistent_mem, to_char(to_date(vs.first_load_time,'YYYY-MM-DD/HH24:MI:SS'),'DD/MM/YYYY HH24:MI:SS') first_load_time,
    vs.parsing_user_id , au.USERNAME parseuser
    FROM $table_bufferData_name vs , all_users au
    WHERE (parsing_user_id != 0)
        AND (au.user_id(+)=vs.parsing_user_id)
        AND (executions >= 1)";
    $stmtd = oci_parse($conn, $query_bufferData);
    oci_execute($stmtd);

    $table_bufferSize_name = 'V$SGAINFO';
    $query_bufferSize = "SELECT ((bytes/1000)/1000) AS megabytes
    FROM $table_bufferSize_name
    WHERE name = 'Buffer Cache Size'";
    $stmtS = oci_parse($conn, $query_bufferSize);
    oci_execute($stmtS);

    //Extraer el usado del buffer
    $table_bufferUsed_name = 'V$Bh';
    $query_bufferUsed = "SELECT (((COUNT(*) * 8192)/1000)/1000)  AS megabytes 
        FROM $table_bufferUsed_name
        WHERE status = 'xcur' OR status = 'cr' OR status = 'read'";
    $stmtU = oci_parse($conn, $query_bufferUsed);
    oci_execute($stmtU);

    // Se cierra la conexión con la base de datos
    oci_close($conn);

    $bufferData = array();
    $bufferSize = oci_fetch_assoc($stmtS);
    $bufferUsed = oci_fetch_assoc($stmtU);;

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
}

$hwm = 0.85; // Porcentaje del HWM 85%

if ( ((int)$bufferUsed['MEGABYTES']) >= ((int)$bufferSize['MEGABYTES']*$hwm)) {   
    $flagHWM = True;
    /*
    // Calcular el porcentaje en relación al tamaño del caché en megabytes
    $usagePercentage = ($bufferUsedInMB / $bufferSizeInMB) * 100;

    // Obtener la fecha y hora actual
    $timestamp = date('Y-m-d H:i:s');

    // Obtener el proceso y usuario
    $process = ""; //Por arreglar;
    $user = ""; // Por arreglar

    // Obtener el detalle de SQL
    $sqlDetail = ""; // Por arreglar

    // Crear el mensaje de alerta
    $alertMessage = "High Water Mark (HWM) exceeded. Buffer Usage: " . $usagePercentage . "%";
    $logMessage = "$timestamp - Process: , User: $user, SQL Detail: $sqlDetail - $alertMessage\n";

    // Registrar la alerta en el CBLog
    file_put_contents('../CBLog.log', $logMessage, FILE_APPEND);*/
}else{$flagHWM = False;}

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
            <div id="scrollable-table" class="scrollable-table">
                <table>
                    <thead>
                        <tr>
                            <th>DAY</th>
                            <th>TIME</th>
                            <th>SIZE (MB)</th>
                            <th>USED (MB)</th>
                            <th>PROCESS ID</th>
                            <th>PERSISTENT MEM (BYTES)</th>
                            <th id="sql">SQL TEXT</th>
                        </tr>
                    </thead>
                    <tbody class="" id="TablaBuffer">
                        <?php
                        if($flagHWM){
                            date_default_timezone_set("America/Costa_Rica");
                            $fecha_actual = date("d/m/Y G:i:s");    
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
                                    $fechaBuffer = DateTime::createFromFormat("d/m/Y H:i:s", $datosB['FIRST_LOAD_TIME']);
                                    //if($dataToUse <= $fecha_actual){
                            ?>
                                    <tr>
                                        <td><?php echo isset($datosB['FIRST_LOAD_TIME']) ? substr($datosB['FIRST_LOAD_TIME'], 0, 10) : '' ?></td>
                                        <td><?php echo isset($datosB['FIRST_LOAD_TIME']) ? substr($datosB['FIRST_LOAD_TIME'], 11) : '' ?></td>
                                        <td><?php echo (float)$bufferSize['MEGABYTES'] ?></td>
                                        <td><?php echo (float)$bufferUsed['MEGABYTES'] ?></td>
                                        <td><?php echo isset($datosB['PARSEUSER']) ? $datosB['PARSEUSER'] : '' ?></td>
                                        <td><?php echo isset($datosB['PERSISTENT_MEM']) ? $datosB['PERSISTENT_MEM'] : '' ?></td>
                                        <td><?php echo isset($datosB['SQL_TEXT']) ? $datosB['SQL_TEXT'] : '' ?></td>
                                    </tr>
                            <?php
                                    //}
                                }
                            }
                            ?>
                        <?php
                        }else{
                        ?>
                        <tr>
                            <td COLSPAN="7"> NO HAY HWM </td>
                        </td>
                        <?php }?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="chart-container">
            <!-- <div id="bufferSizeLabel"><?php echo round($bufferSize['BYTES'] / 1024 / 1024); ?> MB</div> -->
            <canvas id="bufferUsageChart"></canvas>
        </div>
    </main>

    <script>
        function updateRealTimeData() {
            var realTimeDataContainer = document.getElementById("real-time-data-container");
            var realTimeData = document.getElementById("scrollable-table");
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "buffer.php?update=true", true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    // Guardar la posición actual del scroll
                    var currentScrollTop = realTimeData.scrollTop;
                    realTimeDataContainer.innerHTML = xhr.responseText;
                    // Restaurar la posición del scroll después de que se haya actualizado el contenido
                    document.getElementById("scrollable-table").scrollTop = currentScrollTop;

                    // Actualizar el gráfico después de actualizar los datos
                    updateBufferUsageChart();
                }
            };
            xhr.send();
        }

        // Llamar a la función de actualización al cargar la página por primera vez
        updateRealTimeData();

        // Actualizar cada 10 segundos
        setInterval(updateRealTimeData, 10000);

        //Parte del grafico
        var bufferUsageChart

        // Función para actualizar el gráfico de uso del búfer
        function updateBufferUsageChart(){
            if (bufferUsageChart) {
                bufferUsageChart.destroy(); // Destruir el gráfico existente antes de crear uno nuevo
            }

            // Configurar la gráfica de uso del búfer
            bufferUsageChart = new Chart(document.getElementById("bufferUsageChart"), {
                type: "bar",
                data: {
                    labels: ['Database Buffer Cache'],
                    datasets: [{
                        label: 'Database Buffer Used',
                        data: [<?php echo (float)$bufferUsed['MEGABYTES']?>],
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: <?php echo (float)$bufferSize['MEGABYTES']?>,
                            title: {
                                display: true,
                                text: `Buffer Size <?php echo (float)$bufferSize['MEGABYTES']?> (MB)`
                            },
                            ticks: {
                                callback: function(value, index, values) {
                                    return value + ' MB';
                                }
                            }
                        },
                        x: {
                            drawTicks: false
                        }
                    },
                    plugins: {
                        legend: {
                            display: true
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return  "Database Buffer Used: "+ [<?php echo (float)$bufferUsed['MEGABYTES']?>]+ " MB";
                                }
                            }
                        }
                    }
                },
                plugins: [{
                    afterDraw: function(chart, args, options) {
                        var ctx = chart.ctx;
                        var xAxis = chart.scales.x;
                        var yAxis = chart.scales.y;
                        var yValue = yAxis.getPixelForValue(<?php echo (float)$bufferSize['MEGABYTES']*$hwm?>);

                        // Dibujar la línea horizontal
                        ctx.beginPath();
                        ctx.moveTo(xAxis.left, yValue);
                        ctx.lineTo(xAxis.right, yValue);
                        ctx.strokeStyle = 'rgba(255, 0, 0, 0.8)';
                        ctx.lineWidth = 2;
                        ctx.stroke();

                        // Agregar un tooltip a la línea
                        if (chart._active && chart._active[0]) {
                            var tooltipLabel = "HWM: "+<?php echo (float)$bufferSize['MEGABYTES']*$hwm?>+" MB";
                            var tooltipX = (xAxis.right - xAxis.left) / 2 + xAxis.left; // Posición X para el tooltip
                            var tooltipY = yValue - 10; // Posición Y para el tooltip
                            ctx.fillStyle = 'rgba(255, 0, 0, 0.8)';
                            ctx.font = '12px Arial';
                            ctx.textAlign = 'center';
                            ctx.fillText(tooltipLabel, tooltipX, tooltipY);
                        }
                    }
                }]
            });
        }
    </script>
</body>

</html>