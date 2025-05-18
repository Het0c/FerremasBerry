<?php
session_start();
require_once 'conexion.php';

// Obtener los productos desde la BD
$sql = "SELECT idProducto, nombre, descripcion, precio, imagen FROM producto";
$result = $conexion->query($sql);






?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Productos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Lista de Productos</h2>
        <div class="row">
            <?php while ($producto = $result->fetch_assoc()) { ?>
                <div class="col-md-4">
                    <div class="card">
                        <?php if (!empty($producto['imagen'])) { ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($producto['imagen']); ?>" class="card-img-top" alt="<?= htmlspecialchars($producto['nombre']); ?>">
                        <?php } else { ?>
                            <img src="placeholder.jpg" class="card-img-top" alt="Imagen no disponible">
                        <?php } ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($producto['nombre']); ?></h5>
                            <p class="card-text"><?= htmlspecialchars($producto['descripcion']); ?></p>
                            <p class="card-text"><strong>Precio:</strong> $<?= number_format($producto['precio'], 0, ',', '.'); ?></p>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>
