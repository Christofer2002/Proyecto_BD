<?php
session_start(); // Inicia la sesión al comienzo del script

// Si el usuario no está logueado, redirigir a login.php
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_logueado'] !== true) {
  header('Location: ../login.php');
  exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="/Proyecto_BD/assets/css/footer.css" rel="stylesheet">
  <link href="/Proyecto_BD/assets/css/header.css" rel="stylesheet">
  <link href="/Proyecto_BD/assets/css/main.css" rel="stylesheet">
  <title>Home</title>
  <link href="/Proyecto_BD/assets/img/database_icon.png" type="png" rel="website icon">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet">
</head>

<body>
  <div class="contenedor-inicio">
    <?php include '../includes/menu.php'; ?>
    <?php include '../includes/content.php'; ?>
  </div>
</body>

</html>