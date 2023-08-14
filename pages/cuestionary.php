<?php
// Incluir el archivo de configuración de la conexión a la base de datos
require_once '../config/config.php';

// Variable para almacenar los mensajes de error o éxito
$message = '';

// Realizar la consulta SELECT para obtener todas las preguntas de la tabla "cuestionario"

$query = "SELECT c.descripcion AS categoria, cu.id AS cuestionario_id, cu.pregunta
            FROM cuestionario cu
            INNER JOIN contenido cont ON cu.id_contenido = cont.id
            INNER JOIN categoria c ON cu.id_categoria = c.id";
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
?>

<!DOCTYPE html>
<html>

<head>
    <!-- Agregar el enlace al archivo CSS de Bootstrap -->
    <link href="../assets/css/normalize.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.min.css">
    <title>Cuestionary</title>
    <link href="../assets/img/cuestionary_icon.png" type="png" rel="website icon">
</head>

<body>
    <div>
        <?php include '../includes/menu.php'; ?>
    </div>
    <div class="container-main">
        <div class="contenedor">
            <h1 class="mb-3">Cuestionary Evaluation</h1>
            <div class="table-container">
                <table class="container-preguntas">
                    <thead>
                        <tr>
                            <th>
                                <h2>Questions</h2>
                            </th>
                            <th>
                                <h2>S</h2>
                            </th>
                            <th>
                                <h2>N</h2>
                            </th>
                            <th>
                                <h2>N/A</h2>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $currentCategory = '';

                        foreach ($cuestionario as $pregunta) {
                            if ($currentCategory !== $pregunta['categoria']) {
                                $currentCategory = $pregunta['categoria'];
                        ?>
                                <tr class="categoria-row">
                                    <td colspan="5"><strong><?php echo $currentCategory; ?></strong></td>
                                </tr>
                            <?php
                            }
                            ?>
                            <tr>
                                <td class="td-preguntas"><?php echo $pregunta['pregunta']; ?></td>
                                <td><label class="checkbox-btn">
                                        <label for="checkbox"></label>
                                        <input id="checkbox" type="checkbox" class="checkbox-group" value="YES" name="pregunta_<?php echo $pregunta['cuestionario_id']; ?>_s">
                                        <span class="checkmark"></span>
                                    </label></td>
                                <td><label class="checkbox-btn">
                                        <label for="checkbox"></label>
                                        <input id="checkbox" type="checkbox" class="checkbox-group" value="NO" name="pregunta_<?php echo $pregunta['cuestionario_id']; ?>_n">
                                        <span class="checkmark"></span>
                                    </label></td>
                                <td><label class="checkbox-btn">
                                        <label for="checkbox"></label>
                                        <input id="checkbox" type="checkbox" class="checkbox-group" value="N/A" name="pregunta_<?php echo $pregunta['cuestionario_id']; ?>_na">
                                        <span class="checkmark"></span>
                                    </label></td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="button-evaluate">
                <button id="evaluateButton" class="cta">
                    <span class="hover-underline-animation"> Evaluate </span>
                    <svg viewBox="0 0 46 16" height="10" width="30" xmlns="http://www.w3.org/2000/svg" id="arrow-horizontal">
                        <path transform="translate(30)" d="M8,0,6.545,1.455l5.506,5.506H-30V9.039H12.052L6.545,14.545,8,16l8-8Z" data-name="Path 10" id="Path_10"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div class="semaforo-info">
            <div class="semaforo">
                <div class="luz-roja"></div>
                <div class="luz-amarilla"></div>
                <div class="luz-verde"></div>
            </div>
            <div class="percentage-display" id="percentageDisplay"></div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.all.min.js"></script>
    <script src="../assets/js/cuestionary.js"></script>
</body>

</html>