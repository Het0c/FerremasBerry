<?php
require_once 'conexion.php';

$subcategory = isset($_GET["subcategory"]) ? $_GET["subcategory"] : '';

$sql = "SELECT p.* FROM producto p
        INNER JOIN subcategoria s ON p.idSubcategoria = s.idSubcategoria
        WHERE s.idSubcategoria = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $subcategory);
$stmt->execute();
$result = $stmt->get_result();
$productos = $result->fetch_all(MYSQLI_ASSOC);
?>
<!-- Aquí muestras los productos filtrados -->
