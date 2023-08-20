<?php
// Incluir el archivo de configuración de la conexión a la base de datos
require_once __DIR__ . '/../config/config.php';

// Variable para almacenar los mensajes de error o éxito
$message = '';

// Crear un array para almacenar las preguntas
$content = array();

// Verificar si la consulta fue exitosa
if ($stmtm) {
    // Obtener los resultados y almacenarlos en el array
    while ($row = oci_fetch_assoc($stmtm)) {
        $content[] = $row;
    }
    // Liberar los recursos
    oci_free_statement($stmtm);
    if (empty($content)) {
        $message = 'No hay preguntas en la tabla.';
    }
} else {
    $message = 'Error en la consulta: ' . oci_error($stmtm);
}

$actual_url = $_SERVER['REQUEST_URI'];
$links = array(
    "/Proyecto_BD/pages/cuestionary.php",
    "/Proyecto_BD/pages/buffer.php"
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

// Array de submenús correspondientes a cada opción en el menú
$submenus = array(
    array("Objectives Control", "Buffer Control"),
    // Agrega más submenús según sea necesario
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
                <?php
                // Bucle para iterar a través del contenido
                foreach ($content as $index => $item) {
                    $link = isset($links[$index]) ? $links[$index] : "#";
                    $svg = isset($svgs[$index]) ? $svgs[$index] : "#";
                    $submenuOptions = isset($submenus[$index]) ? $submenus[$index] : array();
                ?>
                    <li class="darkerli">
                        <a>
                            <i class="<?php echo $svg ?>"></i>
                            <span class="nav-text"><?php echo $item['CONTENIDO_DESCRIPCION']; ?></span>
                        </a>
                        <?php if (!empty($submenuOptions)) { ?>
                            <!-- Abre el submenú -->
                            <ul class="sub-menu">
                                <?php foreach ($submenuOptions as $subIndex => $submenuOption) { ?>
                                    <!-- Enlace del submenú utilizando el enlace correspondiente -->
                                    <li class="darkerli">
                                        <a href="<?php echo isset($links[$subIndex]) ? $links[$subIndex] : "#"; ?>"><?php echo $submenuOption; ?></a>
                                    </li>
                                <?php } ?>
                            </ul>
                        <?php } ?>
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