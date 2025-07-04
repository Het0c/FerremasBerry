<?php
session_start();
require_once __DIR__ . "/conexion.php"; // Asegúrate de que este archivo defina la variable $conexion

// Procesamos el formulario únicamente si se envía mediante el método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recoger y sanitizar los datos del formulario
    $nombre   = isset($_POST['nombre'])   ? trim($_POST['nombre'])   : "";
    $apellido = isset($_POST['apellido']) ? trim($_POST['apellido']) : "";
    $correo   = isset($_POST['correo'])   ? trim($_POST['correo'])   : "";
    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : "";
    $mensaje  = isset($_POST['mensaje'])  ? trim($_POST['mensaje'])  : "";

    // Verificar que ningún campo obligatorio esté vacío
    if (empty($nombre) || empty($apellido) || empty($correo) || empty($telefono) || empty($mensaje)) {
        die("Por favor, complete todos los campos.");
    }

    // Preparar la consulta SQL para insertar la queja en la tabla "queja"
    $sql = "INSERT INTO queja (nombre, apellido, correo, telefono, mensaje) VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        die("Error al preparar la consulta: " . $conexion->error);
    }

    // "sssss": indica que los 5 parámetros son cadenas
    $stmt->bind_param("sssss", $nombre, $apellido, $correo, $telefono, $mensaje);

    // Ejecutar la consulta. Si se inserta correctamente, se redirige al index.
    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error al enviar la queja: " . $stmt->error;
    }

    // Cerrar la sentencia y la conexión
    $stmt->close();
    $conexion->close();
} else {
    echo "Acceso no autorizado.";
}
?>
