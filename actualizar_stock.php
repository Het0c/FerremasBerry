<?php
session_start();
require_once 'conexion.php'; // Ajusta la ruta según la estructura de tu proyecto

// Verifica que se hayan enviado los datos necesarios
if (!isset($_POST['producto_id']) || !isset($_POST['nuevo_stock'])) {
    die("Datos incompletos.");
}

$producto_id = $_POST['producto_id'];
$nuevo_stock = $_POST['nuevo_stock'];

// Asegúrate de que los valores sean numéricos
if (!is_numeric($producto_id) || !is_numeric($nuevo_stock)) {
    die("Datos no válidos.");
}

// Prepara la consulta para actualizar el stock
$sql = "UPDATE producto SET stock = ? WHERE idProducto = ?";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    die("Error al preparar la consulta: " . $conexion->error);
}

$stmt->bind_param("ii", $nuevo_stock, $producto_id);

if ($stmt->execute()) {
    // Después de actualizar, redirige al panel de bodega
    header("Location: templates/vista_bodeguero.php");
    exit();
} else {
    die("Error al actualizar el stock: " . $stmt->error);
}
