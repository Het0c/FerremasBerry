<?php
session_start();
require_once 'conexion.php'; 

// Obtener las marcas y categorías desde la BD
$marcas_result = $conexion->query("SELECT idMarca, descripcion FROM marca");
$categorias_result = $conexion->query("SELECT idCategoria, descripcion FROM categoria");

// Procesar el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $nombre = $_POST["nombre"];
    $descripcion = $_POST["descripcion"];
    $precio = $_POST["precio"];
    $marca = $_POST["marca"];
    $categoria = $_POST["categoria"];

    // Procesar la imagen si se ha subido
    $imagen = NULL;
    if (!empty($_FILES["imagen"]["tmp_name"])) {
        $imagen = file_get_contents($_FILES["imagen"]["tmp_name"]);
    }

     // Insertar producto en la BD usando consulta preparada
    $sql = "INSERT INTO producto (nombre, descripcion, precio, marca, categoria, imagen) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    
    // Cambia 'b' por 's' y usa send_long_data()
    $stmt->bind_param("ssiiis", $nombre, $descripcion, $precio, $marca, $categoria, $imagen);
    $stmt->send_long_data(5, $imagen); // Envía la imagen como BLOB correctamente
    
    if ($stmt->execute()) {
        echo "<p>✅ Producto agregado correctamente.</p>";
    } else {
        echo "<p>❌ Error al agregar producto: " . $stmt->error . "</p>";
    }
    
    $stmt->close();
    $conexion->close();

}


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Producto</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Ingreso de Productos</h2>
        <form action="agregar_producto.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre del Producto</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea name="descripcion" id="descripcion" class="form-control" required></textarea>
            </div>
            <div class="mb-3">
                <label for="precio" class="form-label">Precio</label>
                <input type="number" name="precio" id="precio" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="marca" class="form-label">Marca</label>
                <select name="marca" id="marca" class="form-control" required>
                    <option value="">Selecciona una marca</option>
                    <?php while ($marca = $marcas_result->fetch_assoc()) { ?>
                        <option value="<?= $marca['idMarca'] ?>"><?= $marca['descripcion'] ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="categoria" class="form-label">Categoría</label>
                <select name="categoria" id="categoria" class="form-control" required>
                    <option value="">Selecciona una categoría</option>
                    <?php while ($categoria = $categorias_result->fetch_assoc()) { ?>
                        <option value="<?= $categoria['idCategoria'] ?>"><?= $categoria['descripcion'] ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="imagen" class="form-label">Imagen del Producto</label>
                <input type="file" name="imagen" id="imagen" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Agregar Producto</button>
        </form>
    </div>
</body>
</html>
