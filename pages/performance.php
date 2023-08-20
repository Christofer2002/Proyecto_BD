<?php
while(1){
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance</title>
    <link href="/Proyecto_BD/assets/css/graphic.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body id="CuerpoB">
    <!-- Parte del PHP para la extraccion de todos los datos -->
    <?php
        // Incluir para usar los datos de la constante
        require_once '../config/config.php';

        // --------------- *** CONEXIONES  *** ---------------
        // Conexion a la base de datos de nuevo ya que los datos son dinamicos
        $conn = oci_connect(DB_USER, DB_PASS, DB_HOST . '/' . DB_NAME);

        // Verifica si hubo algún error en la conexión
        if (!$conn) {
            // Si la conexión falló, muestra el mensaje de error y termina el script
            $e = oci_error();
            die('Error de conexión: ' . $e['message']);
        }
        // -----------------------------------------------------

        // ----------------- *** Peticiones *** ----------------
        // Peticion para extraer los contenidos del buffer
        $table_bufferData_name = 'v$sqlarea';
        $query_bufferData = "SELECT distinct vs.sql_text, vs.persistent_mem, to_char(to_date(vs.first_load_time,'YYYY-MM-DD/HH24:MI:SS'),'MM/DD HH24:MI:SS') first_load_time,
        vs.parsing_user_id , au.USERNAME parseuser
        FROM $table_bufferData_name vs , all_users au
        WHERE (parsing_user_id != 0)
            AND (au.user_id(+)=vs.parsing_user_id)
            AND (executions >= 1)";
        $stmtd = oci_parse($conn, $query_bufferData);
        oci_execute($stmtd);

        // Peticion para extraer el tamaño del buffer
        $table_bufferSizw_name = 'V$SGAINFO';
        $query_bufferSize = "SELECT bytes 
        FROM $table_bufferSizw_name
        WHERE name = 'Buffer Cache Size'";
        $stmtS = oci_parse($conn, $query_bufferSize);
        oci_execute($stmtS);
        // -----------------------------------------------------


        // Se cierra la conexion con la base de datos
        oci_close($conn);


        //---------- **** Extraccion de los datos **** ------------
        $bufferData = array(); //Array para los datos del buffer
        $bufferSize = 0;
        $bufferUsed = 0; //Variable para la cantidad de memoria usada del buffer

        //Se extraen los datos de la consulta de los elementos
        if ($stmtd) {
            // Obtener los resultados y almacenarlos en el array
            while ($row = oci_fetch_assoc($stmtd)) {
                $bufferData[] = $row;
            }
            // Liberar los recursos
            oci_free_statement($stmtd);
            if (empty($bufferData)) {
                $message = 'No hay preguntas en la tabla.';
            }
        } else {
            $message = 'Error en la consulta: ' . oci_error($stmtd);
        }

        //Se extrae el dato de la peticion del tamaño
        $bufferSize = oci_fetch_assoc($stmtS);
        
        //Se calcula el espacio usado del buffer
        foreach($bufferData as $dato){
            $bufferUsed = $bufferUsed + intval($dato['PERSISTENT_MEM']);
        }
        // -----------------------------------------------------
    ?>
    <div class="Datos_buffer">

        <!-- Parte para mostrar todos los datos-->
        <table>
            <thead>
                <th>DAY</th>
                <th>TIME</th>
                <th>SIZE</th>
                <th>USED</th>
                <th>PROCESS ID</th>
                <th>USED</th>
                <th id="sql">SQL TEXT</th>
            </thead>
            <tbody>
                <?php
                foreach($bufferData as $datosB){?>
                    <tr>
                        <td><?php echo substr($datosB['FIRST_LOAD_TIME'], 0, 5)?></td>
                        <td><?php echo substr($datosB['FIRST_LOAD_TIME'], 6)?></td>
                        <td><?php echo $bufferSize['BYTES']?></td>
                        <td><?php echo $bufferUsed?></td>
                        <td><?php echo $datosB['PARSEUSER']?></td>
                        <td><?php echo $datosB['PERSISTENT_MEM']?></td>
                        <td><?php echo $datosB['SQL_TEXT']?></td>
                    </tr>
                <?php
                }?>
            </tbody>    
        </table>
    </div>

    <div class="chart-container">
        <canvas id="sharedSqlChart"></canvas>
        <!-- <canvas id="databaseBufferChart"></canvas> -->
        <!-- <canvas id="redoLogBufferChart"></canvas> -->
    </div>
    
    <!--<script>
        document.addEventListener("DOMContentLoaded", function() {
            var sharedSqlChartCtx = document.getElementById('sharedSqlChart').getContext('2d');
            var sharedSqlChart = new Chart(sharedSqlChartCtx, {
                type: 'bar',
                data: {
                    labels: ['SQL1', 'SQL2', 'SQL3', 'SQL4', 'SQL5'],
                    datasets: [{
                        label: 'Tiempo de Ejecución (segundos)',
                        data: [10, 15, 5, 8, 12],
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>-->

</body>

</html>
<?php sleep(60)?>
<script>
    document.querySelector('#CuerpoB').replaceChildren();
</script>
<?php
}
?>