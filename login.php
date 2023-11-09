<?php
// Inicia sesión en la parte superior de tu script PHP
session_start();

// Incluye el archivo de configuración que establece la conexión con la base de datos
include './config/config.php';

$query_usuarios_dblink = "SELECT DISTINCT username FROM ALL_DB_LINKS WHERE username IS NOT NULL";
$stmt = oci_parse($conn, $query_usuarios_dblink);
oci_execute($stmt);

// Aquí podrías hacer un fetch de los resultados y almacenarlos en un array, por ejemplo
$usuarios_dblink = array();
while (($row = oci_fetch_assoc($stmt)) != false) {
    $usuarios_dblink[] = $row['USERNAME']; // Agrega el nombre de usuario al array
}

// Cierra la conexión cuando ya no sea necesaria
oci_close($conn);

// Revisa si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Suponiendo que 'user' es el nombre del campo en tu formulario
    if (!empty($_POST['user'])) {
        // Establece la variable de sesión con el nombre de usuario seleccionado
        $_SESSION['usuario_logueado'] = true;
        $_SESSION['nombre_usuario'] = $_POST['user'];

        // Redirige a la página de inicio
        header('Location: /Proyecto_BD/pages/inicio.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Asegúrate de tener la siguiente línea para incluir Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="text-center mb-4">
                    <img src="./assets/img/logo.jpg" alt="Logo" class="mb-2" width="200" height="200">
                </div>
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-center mb-4">Iniciar sesión</h5>
                        <form action="login.php" method="POST">
                            <div class="form-group">
                                <label for="user">Usuarios</label>
                                <select class="form-control" id="user" name="user" <?php echo empty($usuarios_dblink) ? 'disabled' : ''; ?>>
                                    <?php if (empty($usuarios_dblink)) : ?>
                                        <option>No hay Database Links configurados actualmente.</option>
                                    <?php else : ?>
                                        <?php foreach ($usuarios_dblink as $username) : ?>
                                            <option value="<?php echo htmlspecialchars($username); ?>">
                                                <?php echo htmlspecialchars($username); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Entrar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Opcional: Incluir los JS de Bootstrap y dependencias -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>