<?php
require_once 'conexion.php';

// Consulta para obtener categorías con sus respectivas subcategorías
$categories = [];
$sql = "SELECT c.idCategoria, c.nombre AS nombreCategoria, s.idSubcategoria, s.descripcion AS nombreSub, s.slug 
        FROM categoria c 
        LEFT JOIN subcategoria s ON c.idCategoria = s.idCategoria 
        ORDER BY c.nombre, s.descripcion";
$result = $conexion->query($sql);

while ($row = $result->fetch_assoc()) {
    $catId = $row["idCategoria"];
    if (!isset($categories[$catId])) {
        $categories[$catId] = [
            "nombre" => $row["nombreCategoria"],
            "subcategorias" => []
        ];
    }
    // Si existe una subcategoría, se agrega
    if ($row["idSubcategoria"]) {
        $categories[$catId]["subcategorias"][] = [
            "nombre" => $row["nombreSub"],
            "slug"   => $row["slug"] ? $row["slug"] : $row["idSubcategoria"]
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Menú Dinámico</title>
    <link rel="stylesheet" href="static/estilos.css">
    <link rel="stylesheet" href="static/styles.css" />
    <link rel="stylesheet" href="static/estiloo2.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" 
          integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" 
          crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="container-navbar">
        <nav class="navbar container">
            <i class="fa-solid fa-bars"></i>
            <ul class="menu">
                <li><a href="index.php">Inicio</a></li>

                <?php foreach ($categories as $cat): ?>
                    <li class="menu__item menu__item--show">
                        <a href="#" class="menu__link">
                            <?= htmlspecialchars($cat["nombre"]) ?>
                            <span class="menu__arrow"></span>
                        </a>
                        <?php if (!empty($cat["subcategorias"])): ?>
                            <ul class="menu__nesting">
                                <?php foreach ($cat["subcategorias"] as $sub): ?>
                                    <li class="menu__inside">
                                        <a href="products.php?subcategory=<?= urlencode($sub["slug"]) ?>" 
                                           class="menu__link menu__link--inside">
                                            <?= htmlspecialchars($sub["nombre"]) ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <form class="search-form" action="products.php" method="GET">
                <input type="search" name="q" placeholder="Buscar productos..." required />
                <button type="submit" class="btn-search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </nav>
    </div>
</body>
</html>
