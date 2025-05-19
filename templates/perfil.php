<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: index.html");
    exit();
}

$usuario = $_SESSION["usuario"];
// Se asume que el arreglo $usuario contiene además el campo "telefono" si existe.
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

    <!-- Formulario para actualizar datos -->
    <form action="update_profile.php" method="post">
        <div>
            <label for="nombre">Nombre:</label>
            <input 
                type="text" 
                id="nombre" 
                name="nombre" 
                value="<?= isset($usuario["nombre"]) ? htmlspecialchars($usuario["nombre"]) : '' ?>" 
                required>
        </div>
        <br>
        <div>
            <label for="password">Nueva Contraseña:</label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                placeholder="Dejar en blanco para mantener la actual">
        </div>
        <br>
        <div>
            <label for="telefono">Teléfono:</label>
            <input 
                type="text" 
                id="telefono" 
                name="telefono" 
                value="<?= isset($usuario["telefono"]) ? htmlspecialchars($usuario["telefono"]) : '' ?>">
        </div>
        <br>
        <button type="submit">Actualizar Datos</button>
    </form>
    <br>
    <a href="../logout.php">Cerrar sesión</a>
</body>
</html>
