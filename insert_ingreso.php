<?php
// insert_ingreso.php
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir datos del formulario con comprobación básica.
    $nombre   = $_POST["nombre"] ?? '';
    $apellido = $_POST["apellido"] ?? '';
    $correo   = $_POST["correo"] ?? '';
    $telefono = $_POST["telefono"] ?? '';
    $mensaje  = $_POST["mensaje"] ?? '';

    // Preparamos la consulta usando prepared statements (más seguro contra inyecciones).
    $instruccion_SQL = "INSERT INTO ingreso (nombre, apellido, correo, telefono, mensaje) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($instruccion_SQL);
    if (!$stmt) {
        die("Error al preparar la consulta: " . $conexion->error);
    }

    // Suponiendo que el teléfono es numérico; en caso contrario, cámbialo a 's' (string)
    $stmt->bind_param("sssis", $nombre, $apellido, $correo, $telefono, $mensaje);

    if ($stmt->execute()) {
        echo "<p>Datos insertados correctamente.</p>";
    } else {
        echo "<p>Error al insertar los datos: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

$conexion->close();
?>
<a href="index.html">Volver Atrás</a>
