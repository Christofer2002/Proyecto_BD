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

// Cierra la conexión a la base de datos al finalizar

?>

<!-- Component Start -->
<div class="flex flex-col items-center w-40 primer-columna overflow-hidden text-gray-400 bg-gray-900 rounded">
    <a class="flex items-center w-full px-3 mt-3" href="/Proyecto_BD/index.php">
        <svg class="w-8 h-8 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path d="M11 17a1 1 0 001.447.894l4-2A1 1 0 0017 15V9.236a1 1 0 00-1.447-.894l-4 2a1 1 0 00-.553.894V17zM15.211 6.276a1 1 0 000-1.788l-4.764-2.382a1 1 0 00-.894 0L4.789 4.488a1 1 0 000 1.788l4.764 2.382a1 1 0 00.894 0l4.764-2.382zM4.447 8.342A1 1 0 003 9.236V15a1 1 0 00.553.894l4 2A1 1 0 009 17v-5.764a1 1 0 00-.553-.894l-4-2z" />
        </svg>
        <span class="ml-2 text-sm font-bold">Database Administrator</span>
    </a>
    <div class="w-full px-2">
        <div class="flex flex-col items-center w-full mt-3 border-t border-gray-700">
            <?php
            // Array paralelo con enlaces correspondientes para cada elemento
            $links = array(
                "/Proyecto_BD/pages/cuestionary.php",
            );
            foreach ($content as $index => $item) {
                $link = isset($links[$index]) ? $links[$index] : "#";
            ?>
                <a class="flex items-center w-full h-auto px-3 mt-4 rounded hover:bg-gray-700 hover:text-gray-300" href="<?php echo $link; ?>">
                    <svg class="w-6 h-auto stroke-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                    </svg>
                    <span class="ml-2 text-sm font-medium "><?php echo $item['contenido_descripcion']; ?></span>
                </a>
            <?php } ?>
        </div>
        <div class="flex flex-col items-center w-full mt-2 mb-2 border-t border-gray-700">
            <a class="flex items-center w-full h-12 px-3 mt-2 rounded hover:bg-gray-700 hover:text-gray-300" href="#">
                <svg class="w-6 h-6 stroke-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                </svg>
                <span class="ml-2 text-sm font-medium">Settings</span>
            </a>
        </div>
    </div>
    <a class="flex items-center justify-center w-full h-16 mt-auto bg-gray-700 hover:bg-gray-800 hover:text-gray-300" href="#">
        <svg class="w-6 h-6 stroke-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span class="ml-2 text-sm font-medium">Account</span>
    </a>
</div>
<!-- Component End  -->