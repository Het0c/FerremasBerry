<?php
require_once 'conexion.php';

$sql = "SELECT idSubcategoria, nombre, slug FROM subcategoria ORDER BY nombre";
$result = $conexion->query($sql);

$subcategorias = [];
while ($row = $result->fetch_assoc()) {
    $subcategorias[] = [
        "id" => $row["idSubcategoria"],
        "nombre" => $row["nombre"],
        "slug" => $row["slug"]
    ];
}

// Devolver datos en JSON para consumir desde el frontend
header('Content-Type: application/json');
echo json_encode($subcategorias);
?>
