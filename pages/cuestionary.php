<?php
// Incluir el archivo de configuración de la conexión a la base de datos
require_once '../config/config.php';

// Variable para almacenar los mensajes de error o éxito
$message = '';

// Realizar la consulta SELECT para obtener todas las preguntas de la tabla "cuestionario"

$query = "SELECT *, cu.id AS cuestionario_id
            FROM cuestionario cu
            INNER JOIN contenido c ON cu.contenidoC = c.id;";
$result = $mysqli->query($query);


// Crear un array para almacenar las preguntas
$cuestionario = array();

// Verificar si la consulta fue exitosa
if ($result) {
    // Obtener todos los resultados y almacenarlos en el array
    $cuestionario = $result->fetch_all(MYSQLI_ASSOC);
    // Liberar los resultados de la memoria
    $result->free();
    if (empty($cuestionario)) {
        $message = 'No hay preguntas en la tabla.';
    }
} else {
    $message = 'Error en la consulta: ' . $mysqli->error;
}

// Cierra la conexión a la base de datos al finalizar
$mysqli->close();
?>

<!DOCTYPE html>
<html>

<head>
    <!-- Agregar el enlace al archivo CSS de Bootstrap -->
    <link href="../assets/css/normalize.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <script src="../assets/js/cuestionary.js"></script>
    <title>Cuestionary</title>
    <link href="../assets/img/cuestionary_icon.png" type="png" rel="website icon">
</head>

<body>
    <h1 class="mb-3">Cuestionary Evaluation</h1>

    <div class="container">
        <div class="contenedor">
            <div class="table-container">
                <table class="container-preguntas">
                    <thead>
                        <tr>
                            <th>
                                <h1>Questions</h1>
                            </th>
                            <th>
                                <h1>S</h1>
                            </th>
                            <th>
                                <h1>N</h1>
                            </th>
                            <th>
                                <h1>N/A</h1>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($cuestionario as $pregunta) {
                        ?>
                            <tr>
                                <td><?php echo $pregunta['pregunta']; ?></td>
                                <td><label class="checkbox-btn">
                                        <label for="checkbox"></label>
                                        <input id="checkbox" type="checkbox" class="checkbox-group" value= "YES" name="pregunta_<?php echo $pregunta['id']; ?>_s">
                                        <span class="checkmark"></span>
                                    </label></td>
                                <td><label class="checkbox-btn">
                                        <label for="checkbox"></label>
                                        <input id="checkbox" type="checkbox" class="checkbox-group" value = "NO" name="pregunta_<?php echo $pregunta['id']; ?>_n">
                                        <span class="checkmark"></span>
                                    </label></td>
                                <td><label class="checkbox-btn">
                                        <label for="checkbox"></label>
                                        <input id="checkbox" type="checkbox" class="checkbox-group" value="N/A" name="pregunta_<?php echo $pregunta['id']; ?>_na">
                                        <span class="checkmark"></span>
                                    </label></td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <button id="evaluateButton" class="cta">
                <span class="hover-underline-animation"> Evaluate </span>
                <svg viewBox="0 0 46 16" height="10" width="30" xmlns="http://www.w3.org/2000/svg" id="arrow-horizontal">
                    <path transform="translate(30)" d="M8,0,6.545,1.455l5.506,5.506H-30V9.039H12.052L6.545,14.545,8,16l8-8Z" data-name="Path 10" id="Path_10"></path>
                </svg>
            </button>
            <script>
                const evaluateButton = document.querySelector('#evaluateButton');
                evaluateButton.addEventListener('click', () => {
                    changeColorSemaphore(evaluate());
                });
            </script>

        </div>
        <div class="semaforo">
            <div class="luz-roja"></div>
            <div class="luz-amarilla"></div>
            <div class="luz-verde"></div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>

</html>