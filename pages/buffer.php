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
    $query_bufferData = "SELECT distinct vs.sql_text, vs.persistent_mem, to_char(to_date(vs.first_load_time,'YYYY-MM-DD/HH24:MI:SS'),'DD/MM/YYYY HH24:MI:SS') first_load_time,
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
    $query_bufferData = "SELECT distinct vs.sql_text, vs.persistent_mem, to_char(to_date(vs.first_load_time,'YYYY-MM-DD/HH24:MI:SS'),'DD/MM/YYYY HH24:MI:SS') first_load_time,
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
            <div id="scrollable-table" class="scrollable-table">
                <table>
                    <thead>
                        <tr>
                            <th>DAY</th>
                            <th>TIME</th>
                            <th>SIZE</th>
                            <th>USED</th>
                            <th>PROCESS ID</th>
                            <th>PERSISTENT MEM</th>
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
                                    <td><?php echo isset($datosB['FIRST_LOAD_TIME']) ? substr($datosB['FIRST_LOAD_TIME'], 0, 10) : '' ?></td>
                                    <td><?php echo isset($datosB['FIRST_LOAD_TIME']) ? substr($datosB['FIRST_LOAD_TIME'], 11) : '' ?></td>
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
                }
            };
            xhr.send();
        }

        // Llamar a la función de actualización al cargar la página por primera vez
        updateRealTimeData();

        // Actualizar cada 10 segundos
        setInterval(updateRealTimeData, 10000);


        // Obtener el tamaño del búfer en megabytes
        const bufferSizeInMB = <?php echo $bufferSize['BYTES']; ?> / 1024 / 1024;


        // Función para procesar los datos y obtener el tamaño total por usuario
        function processDataForGraph(data) {
            var users = {};

            for (var i = 0; i < data.length; i++) {
                var parseUser = data[i].PARSEUSER;
                var persistentMem = parseInt(data[i].PERSISTENT_MEM);

                if (parseUser && persistentMem) {
                    if (!users[parseUser]) {
                        users[parseUser] = {
                            size: 0,
                            used: persistentMem
                        };
                    }
                    users[parseUser].size += persistentMem;
                }
            }

            var userLabels = [];
            var userSizes = [];
            var userUseds = [];
            for (var user in users) {
                userLabels.push(user);
                console.log(users[user].size);
                userSizes.push(users[user].size / 1024 / 1024); // Convertir a megabytes
                userUseds.push(users[user].used / 1024 / 1024); // Convertir a megabytes
            }

            return {
                labels: userLabels,
                sizes: userSizes,
                useds: userUseds
            };
        }

        // Obtener los datos procesados para la gráfica
        var processedData = processDataForGraph(<?php echo json_encode($dataToUse); ?>);

        console.log(processedData);

        // Configurar la gráfica de uso del búfer
        var bufferUsageChart = new Chart(document.getElementById("bufferUsageChart"), {
            type: "line", // Cambiar a tipo "line"
            data: {
                labels: processedData.labels,
                datasets: [{
                    label: "Buffer Usage",
                    data: processedData.sizes,
                    borderColor: "rgba(75, 192, 192, 1)",
                    borderWidth: 2,
                    pointBackgroundColor: "rgba(75, 192, 192, 1)",
                    fill: false
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: `Buffer Size ${bufferSizeInMB} (MB)`
                        },
                        ticks: {
                            callback: function(value, index, values) {
                                return value + ' MB';
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: "Parse User"
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var username = context.label;
                                var size = context.parsed.y;
                                var used = processedData.useds[context.dataIndex]; // Obtener el valor de uso correspondiente

                                return "User: " + username + " | Size: " + size.toFixed(2) + " MB" + " | Used: " + used.toFixed(2) + " MB";
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>