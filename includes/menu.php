<?php
// Incluir el archivo de configuración de la conexión a la base de datos
require_once __DIR__ . '/../config/config.php';

// Variable para almacenar los mensajes de error o éxito
$message = '';

// Realizar la consulta SELECT para obtener todas las preguntas del cuestionario con sus descripciones de contenido

$query = "SELECT c.descripcion AS contenido_descripcion FROM contenido c";
$result = $mysqli->query($query);

// Crear un array para almacenar las preguntas
$content = array();

// Verificar si la consulta fue exitosa
if ($result) {
    // Obtener todos los resultados y almacenarlos en el array
    $content = $result->fetch_all(MYSQLI_ASSOC);
    // Liberar los resultados de la memoria
    $result->free();
    if (empty($cuestionario)) {
        $message = 'No hay preguntas en la tabla.';
    }
} else {
    $message = 'Error en la consulta: ' . $mysqli->error;
}

$actual_url = $_SERVER['REQUEST_URI'];
$links = array(
    "/Proyecto_BD/pages/cuestionary.php",
);

$svgs = array(
    "fa fa-file-circle-check fa-lg",
    "fa fa-chart-pie fa-lg",
    "fa fa-network-wired fa-lg",
    "fa fa-memory fa-lg",
    "fa fa-hard-drive fa-lg",
    "fa fa-user-shield fa-lg",
    "fa fa-hammer fa-lg",
    "fa fa-eye fa-lg"
);

// Cierra la conexión a la base de datos al finalizar

?>

<!DOCTYPE html>
<html class="menu">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content=="IE=edge" />
    <meta name="google" value="notranslate" />
    <title>Side Menu</title>
    <link rel="stylesheet" type="text/css" href="/Proyecto_BD/assets/css/menu.css">
    <link rel="stylesheet" type="text/css" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
</head>

<body>
    </div>
    <nav class="main-menu">
        <?php
        if ($actual_url == ("/Proyecto_BD/index.php")) {
        ?>
            <div>
                <a class="logo" href="/Proyecto_BD/index.php">
                    <i class="fa-solid fa-database fa-lg"></i>
                    <span class="nav-text">Database Administrator</span>
                </a>
            </div>
        <?php
        }
        ?>
        <div class="settings"></div>
        <div class="scrollbar" id="style-1">
            <ul>
                <li>
                    <a href="/Proyecto_BD/index.php">
                        <i class="fa fa-home fa-lg"></i>
                        <span class="nav-text">Home</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-user fa-lg"></i>
                        <span class="nav-text">Login</span>
                    </a>
                </li>
                </li>
                <?php
                foreach ($content as $index => $item) {
                    $link = isset($links[$index]) ? $links[$index] : "#";
                    $svg = isset($svgs[$index]) ? $svgs[$index] : "#";
                ?>
                    <li class="darkerli">
                        <a href="<?php echo $link; ?>">
                            <i class="<?php echo $svg ?>"></i>
                            <span class="nav-text"><?php echo $item['contenido_descripcion']; ?></span>
                        </a>
                    </li>
                <?php } ?>
            </ul>
            <li>
                <a href="http://startific.com">
                    <i class="fa fa-question-circle fa-lg"></i>
                    <span class="nav-text">Help</span>
                </a>
            </li>
            <ul class="logout">
                <li>
                    <a href="http://startific.com">
                        <i class="fa fa-lightbulb-o fa-lg"></i>
                        <span class="nav-text">
                            Blog
                        </span>
                    </a>
                </li>
            </ul>
    </nav>
</body>
<script src="https://kit.fontawesome.com/e426259eb0.js" crossorigin="anonymous"></script>

</html>