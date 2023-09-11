<?php
// Incluir para usar los datos de la constante
require_once '../config/config.php';


//-------- Prueba
//Funcion para extrear los datos de los bloques dentro del buffer
function  getTablespaceData($connector){
    $tablespaceData = array();
    $tablespacesData_name = 'v$temp_extent_pool';

    $query_tablespaceData = "SELECT d.status \"Status\", d.tablespace_name \"Name\",
                        TO_CHAR(NVL(a.bytes / 1024 / 1024, 0),'99,999,990.90') \"Size (MB)\",
                        TO_CHAR(NVL(a.bytes - NVL(f.bytes, 0), 0)/1024/1024,'99999999.99') \"Used (MB)\",
                        TO_CHAR(NVL(f.bytes / 1024 / 1024, 0),'99,999,990.90') \"Free (MB)\",
                        TO_CHAR(NVL((a.bytes - NVL(f.bytes, 0)) / a.bytes * 100, 0), '990.00') \"(Used) %\"
        FROM sys.dba_tablespaces d,
            (select tablespace_name, sum(bytes) bytes from dba_data_files group by tablespace_name) a,
            (select tablespace_name, sum(bytes) bytes from dba_free_space group by tablespace_name) f 
        WHERE d.tablespace_name = a.tablespace_name(+) 
                AND d.tablespace_name = f.tablespace_name(+) 
                AND NOT (d.extent_management like 'LOCAL' AND d.contents like 'TEMPORARY')
                AND d.tablespace_name NOT IN ('SYSTEM', 'SYSAUX', 'UNDOTBS1', 'USERS', 'TEMP')
        UNION ALL
        SELECT d.status \"Status\", d.tablespace_name \"Name\",
                TO_CHAR(NVL(a.bytes / 1024 / 1024, 0),'99,999,990.90') \"Size (MB)\",
                TO_CHAR(NVL(t.bytes,0)/1024/1024,'99999999.99') \"Used (MB)\",
                TO_CHAR(NVL((a.bytes -NVL(t.bytes, 0)) / 1024 / 1024, 0),'99,999,990.90') \"Free (MB)\",
                TO_CHAR(NVL(t.bytes / a.bytes * 100, 0), '990.00') \"(Used) %\"
        FROM sys.dba_tablespaces d,
            (select tablespace_name, sum(bytes) bytes from dba_temp_files group by tablespace_name) a,
            (select tablespace_name, sum(bytes_cached) bytes from $tablespacesData_name group by tablespace_name) t
        WHERE d.tablespace_name = a.tablespace_name(+) 
                AND d.tablespace_name = t.tablespace_name(+) 
                AND d.extent_management like 'LOCAL' AND d.contents like 'TEMPORARY'
                AND d.tablespace_name NOT IN ('SYSTEM', 'SYSAUX', 'UNDOTBS1', 'USERS', 'TEMP')";
    $stmTD = oci_parse($connector, $query_tablespaceData);
    oci_execute($stmTD);

    //Se extraen los elementos que se obtuvieron
    if($stmTD){
        while($row = oci_fetch_assoc($stmTD)){
            $tablespaceData [] = $row;
        }
        oci_free_statement($stmTD);
        if(empty($tablespaceData)) { echo 'No hay datos de tablespace '; }
    }else{ echo 'error en la consulta '. oci_error($stmTD); }

    return $tablespaceData;
}

//Funcion para extraer la saturacion
function getSaturacion($tablespacesName){
    //Aqui tenemos que abrir la conecccion obligatoriamente
    $conn = oci_connect(DB_USER, DB_PASS, DB_HOST . '/' . DB_NAME);
    // Verifica si hubo algún error en la conexión
    if (!$conn) {
        $e = oci_error();
        die('Error de conexión: ' . $e['message']);
    }

    //Hacemos la peticion para encontrar el tamaño de registro del tablespaces
    $query_tablespace_sizeR = "SELECT Sum(T.bytes + SUM(I.bytes)) AS TOTAL_SIZE
        FROM
            (SELECT segment_name AS table_name, bytes
            FROM dba_segments
            WHERE segment_type = 'TABLE'
            AND tablespace_name = '$tablespacesName') T
        JOIN
            (SELECT i.index_name, i.table_name, s.bytes
            FROM dba_indexes i
            JOIN dba_segments s ON s.segment_name = i.index_name
            WHERE s.segment_type IN ('INDEX', 'INDEX PARTITION', 'INDEX SUBPARTITION')) I
        ON T.table_name = I.table_name
        GROUP BY T.table_name, T.bytes";
    $stmTZ = oci_parse($conn, $query_tablespace_sizeR);
    oci_execute($stmTZ);
    $tablespace_sizeR = oci_fetch_assoc($stmTZ);
    
    //Hacemos la peticion para encontrar el promedio de peticiones diarias de una tablespaces

    
    if(floatval($tablespace_sizeR['TOTAL_SIZE'])>0){
        return floatval($tablespace_sizeR['TOTAL_SIZE']);
    }
    return "Tablespace sin uso";
}

//Conectar con la base de datos
$conn = oci_connect(DB_USER, DB_PASS, DB_HOST . '/' . DB_NAME);
// Verifica si hubo algún error en la conexión
if (!$conn) {
    $e = oci_error();
    die('Error de conexión: ' . $e['message']);
}

$tablespaceData = getTablespaceData($conn);

oci_close($conn);

$hwm = 0.80; // Porcentaje del HWM 85%

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tablespaces</title>
    <link href="/Proyecto_BD/assets/css/graphic.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body id="CuerpoB">
    <div>
        <?php include '../includes/menu.php'; ?>
    </div>
    <main class="main">
        <table>
            <thead>
                <th>Tablespace</th>
                <th>Grafico</th>
                <th>Saturacion</th>
                <th>Estatus</th>
                <th>HWM</th>
            </thead>
            <tbody>
            <?php
                foreach($tablespaceData as $datosT){?>
                    <tr>
                        <td><?php echo $datosT['Name']?></td>
                        <td>
                            <div class="chart-container">
                                <canvas id="bufferUsageChart<?php echo $datosT['Name']?>"></canvas>
                            </div>
                        </td>
                        <td><?php echo getSaturacion($datosT['Name'])?></td>
                        <td>
                            <div class= "semaforo" id="semaforo<?php echo $datosT['Name']?>" style="margin: 0">
                                <div class="luz-roja"></div>
                                <div class="luz-verde"></div>
                            </div>
                        </td>
                        <td><?php echo $hwm*100?> %</td>
                    </tr>
                <?php
                }?>
            </tbody>
        </table>
        <div class="chart-container">
            <canvas id="bufferUsageChart"></canvas>
        </div>
        <canvas id="myChart" width="400" height="400"></canvas>

    </main>

    <!-- Script para renderizar los graficos-->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php
            foreach($tablespaceData as $datosT){?>
                var bufferUsageChart = new Chart(document.getElementById("bufferUsageChart<?php echo $datosT['Name']?>"), {
                                type: "bar",
                                data: {
                                    labels: ['Tablespace <?php echo $datosT['Name']?>'],
                                    datasets: [{
                                        label: 'Tablespace <?php echo $datosT['Name']?> Used',
                                        data: [parseFloat(<?php echo $datosT['Used (MB)']?>)], // Debes proporcionar tus datos aquí
                                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                        borderColor: 'rgba(255, 99, 132, 1)',
                                        borderWidth: 1
                                    },{
                                        label: 'Tablespace <?php echo $datosT['Name']?> Free',
                                        data: [parseFloat(<?php echo $datosT['Free (MB)']?>)], // Debes proporcionar tus datos aquí
                                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                        borderColor: 'rgba(75, 192, 192, 1)',
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    scales: {
                                        y: {
                                            stacked: true,
                                            beginAtZero: true,
                                            max: parseInt(<?php echo $datosT['Size (MB)']?>),
                                            title: {
                                                display: true,
                                                text: "Tablespace <?php echo $datosT['Name']?> size"
                                            },
                                            ticks: {
                                                callback: function(value, index, values) {
                                                    return value + ' MB';
                                                }
                                            }
                                        },
                                        x: {
                                            stacked: true,
                                            beginAtZero: true,
                                            ticks: {
                                                display: false
                                            }
                                        }
                                    }
                                },
                                plugins: [{
                                    afterDraw: function(chart, args, options) {
                                        var ctx = chart.ctx;
                                        var xAxis = chart.scales.x;
                                        var yAxis = chart.scales.y;
                                        var yValue = yAxis.getPixelForValue(parseFloat(<?php echo $datosT['Size (MB)']?>) * parseFloat(<?php echo $hwm?>));

                                        // Dibujar la línea horizontal
                                        ctx.beginPath();
                                        ctx.moveTo(xAxis.left, yValue);
                                        ctx.lineTo(xAxis.right, yValue);
                                        ctx.strokeStyle = 'rgba(255, 0, 0, 0.8)';
                                        ctx.lineWidth = 2;
                                        ctx.stroke();

                                        // Agregar un tooltip a la línea
                                        if (chart._active && chart._active[0]) {
                                            var tooltipLabel = "HWM <?php echo $hwm*100?>%";
                                            var tooltipX = (xAxis.right - xAxis.left) / 2 + xAxis.left;
                                            var tooltipY = yValue - 10;
                                            ctx.fillStyle = 'rgba(255, 0, 0, 0.8)';
                                            ctx.font = '12px Arial';
                                            ctx.textAlign = 'center';
                                            ctx.fillText(tooltipLabel, tooltipX, tooltipY);
                                        }
                                    }
                                }]
                            });

            <?php
            }?>
        });
    </script>

    <!-- Script para renderizar los semaforos-->
    <script>
        <?php foreach($tablespaceData as $datosT){?>
            var semaforo = document.querySelector('#semaforo<?php echo $datosT['Name']?>');
            <?php
            if($datosT['Status']== 'ONLINE'){ ?>
                semaforo.querySelector('.luz-roja').style.backgroundColor="gray";
            <?php }else {?>
                semaforo.querySelector('.luz-verde').style.backgroundColor="gray";
            <?php }?>
        <?php
            }?>
    </script>

    <!-- Parte del refresh de la pagina-->
    <script>
        
        function refreshPage() {
            //Guarda la posicion del scroll en el almacenamiento local
            localStorage.setItem('scrollPosition', window.scrollY);

            //Recarga la pagina
            location.reload();
        }

        //Cuando se recargue la magina haga esta funcion
        window.onload = function () {
            //Se extrae la posicion almacenada
            const scrollPosition = localStorage.getItem('scrollPosition');
            //Si es distinto a nulo entonces si tiene valor y se le coloca al scroll actual
            if (scrollPosition !== null) {
                window.scrollTo(0, scrollPosition);
                localStorage.removeItem('scrollPosition');
            }
        };

        //Refresca cada 10 segundos (10000 milisegundos)
        setInterval(refreshPage, 10000);
    </script>

</body>
</html>