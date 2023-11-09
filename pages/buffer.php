<?php
session_start(); // Inicia la sesión al comienzo del script

// Si el usuario no está logueado, redirigir a login.php
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_logueado'] !== true) {
  header('Location: ../login.php');
  exit;
}

// Incluir para usar los datos de la constante
require_once '../config/config.php';

//-------- Prueba
//Funcion para extrear los datos de los bloques dentro del buffer
function  getBufferData($connector){
    $bufferData = array();
    $table_bufferData_name = 'v$sqlarea';
    $query_bufferData = "SELECT distinct vs.sql_text, vs.persistent_mem, to_char(to_date(vs.first_load_time,'YYYY-MM-DD/HH24:MI:SS'),'DD/MM/YYYY HH24:MI:SS') first_load_time,
    vs.parsing_user_id , au.USERNAME parseuser
    FROM $table_bufferData_name vs , all_users au
    WHERE (parsing_user_id != 0)
        AND (au.user_id(+)=vs.parsing_user_id)
        AND (executions >= 1)";
    $stmtD = oci_parse($connector, $query_bufferData);
    oci_execute($stmtD);

    //Extraemos los elementos del arreglo
    if ($stmtD) {
        while ($row = oci_fetch_assoc($stmtD)) {
            $bufferData[] = $row;
        }
        if (empty($bufferData)) { echo 'No hay preguntas en la tabla.'; }
    }else { echo 'Error en la consulta: ' . oci_error($stmtd); }

    return $bufferData;
}

//Funcion para extraer el tamaño del buffer
function getBufferSize($connector){
    $table_bufferSize_name = 'V$SGAINFO';
    $query_bufferSize = "SELECT ((bytes/1000)/1000) AS megabytes 
        FROM $table_bufferSize_name
        WHERE name = 'Buffer Cache Size'";
    $stmtS = oci_parse($connector, $query_bufferSize);
    oci_execute($stmtS);
    $bufferSize = oci_fetch_assoc($stmtS);
    return $bufferSize;
}

//Funcion para extraer el usado del buffer
function getBufferUsed($connector){
    $table_bufferUsed_name = 'V$Bh';
    $query_bufferUsed = "SELECT (((COUNT(*) * 8192)/1000)/1000)  AS megabytes 
        FROM $table_bufferUsed_name
        WHERE status = 'xcur' OR status = 'cr' OR status = 'read'";
    $stmtU = oci_parse($connector, $query_bufferUsed);
    oci_execute($stmtU);
    $bufferUsed = oci_fetch_assoc($stmtU);
    return $bufferUsed;
}

//Funciona para obtener los datos en tiempo real
function getRealTimeData($connector){
    $realTimeData = array();

    $bufferData = getBufferData($connector);
    $bufferSize = getBufferSize($connector);
    $bufferUsed = getBufferUsed($connector);

    $realTimeData['bufferData'] = $bufferData;
    $realTimeData['bufferSize'] = $bufferSize;
    $realTimeData['bufferUsed'] = $bufferUsed;

    return $realTimeData;
}

//Conectar con la base de datos
$conn = oci_connect(DB_USER, DB_PASS, DB_HOST . '/' . DB_NAME);
// Verifica si hubo algún error en la conexión
if (!$conn) {
    $e = oci_error();
    die('Error de conexión: ' . $e['message']);
}

// Verificar si se debe actualizar los datos o mostrar el contenido estático
$updateRealTimeData = isset($_GET['update']) && $_GET['update'] === 'true';

if ($updateRealTimeData) {
    $realTimeData = getRealTimeData($conn);
    $bufferData = $realTimeData['bufferData'];
    $bufferSize = $realTimeData['bufferSize'];
    $bufferUsed = $realTimeData['bufferUsed'];
} else {
    $bufferData = getBufferData($conn);
    $bufferSize = getBufferSize($conn);
    $bufferUsed = getBufferUsed($conn); 
}

oci_close($conn);

$hwm = 0.10; // Porcentaje del HWM 85%
$flagHWM = ((int)$bufferUsed['MEGABYTES']) >= ((int)$bufferSize['MEGABYTES'] * $hwm);

//---------Prueba-----

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
                    <tbody id="TablaBuffer">
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
            <canvas id="bufferUsageChart"> </canvas>
        </div>
    </main>

    <script>
        var bufferUsageChart;

        function updateBufferUsageChart(bufferSize, bufferUsed, hwm) {
            if (bufferUsageChart) {
                bufferUsageChart.destroy();
            }

            bufferUsageChart = new Chart(document.getElementById("bufferUsageChart"), {
                type: "bar",
                data: {
                    labels: ['Database Buffer Cache'],
                    datasets: [{
                        label: 'Database Buffer Used',
                        data: [parseInt(bufferUsed)],
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: parseInt(bufferSize),
                            title: {
                                display: true,
                                text: "Buffer Size "+parseInt(bufferSize)+" (MB)"
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
                                    return  "Database Buffer Used: "+ parseInt(bufferUsed) + " MB";
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
                        var yValue = yAxis.getPixelForValue(parseInt(bufferSize) * parseFloat(hwm));

                        // Dibujar la línea horizontal
                        ctx.beginPath();
                        ctx.moveTo(xAxis.left, yValue);
                        ctx.lineTo(xAxis.right, yValue);
                        ctx.strokeStyle = 'rgba(255, 0, 0, 0.8)';
                        ctx.lineWidth = 2;
                        ctx.stroke();

                        // Agregar un tooltip a la línea
                        if (chart._active && chart._active[0]) {
                            var tooltipLabel = "HWM: "+ parseInt(parseInt(bufferSize) * parseFloat(hwm))+" MB";
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

        function updateRealTimeData() {
            var realTimeDataContainer = document.getElementById("real-time-data-container");
            var realTimeData = document.getElementById("scrollable-table");
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "buffer.php?update=true", true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    var currentScrollTop = realTimeData.scrollTop;
                    var newContent = xhr.responseText;
                    realTimeDataContainer.innerHTML = newContent;

                    var bufferSize = document.querySelector("#bufferSize").value;
                    var bufferUsed = document.querySelector("#bufferUsed").value;
                    var hwm = document.querySelector("#hwm").value;

                    updateBufferUsageChart(bufferSize, bufferUsed, hwm);

                    document.getElementById("scrollable-table").scrollTop = currentScrollTop;
                }
            };
            xhr.send();
        }

        updateRealTimeData();
        // Actualizar cada 10 segundos
        setInterval(updateRealTimeData, 10000);
    </script>
    <input type="hidden" id="bufferSize" value=<?php echo $bufferSize['MEGABYTES']?>>
    <input type="hidden" id="bufferUsed" value=<?php echo $bufferUsed['MEGABYTES']?>>
    <input type="hidden" id="hwm" value=<?php echo $hwm?>>
</body>

</html>