<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: index.html");
    exit();
}

$usuario = $_SESSION["usuario"];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil de Usuario</title>
</head>
<body>
    <h2>Bienvenido, <?= htmlspecialchars($usuario["nombre"]); ?></h2>
    <p>ID de usuario: <?= htmlspecialchars($usuario["id"]); ?></p>
    <a href="../logout.php">Cerrar sesión</a>
</body>
</html>
