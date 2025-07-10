<?php
session_start();
require_once 'conexion.php';

$monedaSeleccionada = $_GET['moneda'] ?? 'CLP';
$subcategory = $_GET['subcategory'] ?? '';

$tasasCambio = [
  'USD' => ['tasa' => 800],
  'EUR' => ['tasa' => 900],
  'BRL' => ['tasa' => 150],
  'COP' => ['tasa' => 0.22],
  'MXN' => ['tasa' => 40]
];

$fechaActualizacion = date("H:i");

// Filtrar productos por slug de subcategoría
if ($subcategory != '') {
  $stmt = $conexion->prepare("SELECT p.* FROM producto p
                              INNER JOIN subcategoria s ON p.Subcategoria = s.idSubcategoria
                              WHERE s.slug = ?");
  $stmt->bind_param("s", $subcategory);
  $stmt->execute();
  $result = $stmt->get_result();
  $products = $result->fetch_all(MYSQLI_ASSOC);
} else {
  $products = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Ferremas - Herramientas</title>
  <link rel="stylesheet" href="templates/static/estilos.css">
  <link rel="stylesheet" href="templates/static/styles.css">
  <link rel="stylesheet" href="templates/static/estiloo2.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
</head>
<body>

<!-- 🧱 Encabezado con carrito -->
<div class="container-hero">
  <div class="container hero">
    <div class="container-logo">
      <i class="fa-solid fa-person-digging"></i>
      <h1 class="logo"><a href="/FERREMAS">FERREMAS</a></h1>
    </div>

    <div class="container-icon">
      <div class="container-cart-icon">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon-cart" viewBox="0 0 24 24" stroke="currentColor">
          <path d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5..."/>
        </svg>
      </div>

      <div class="carrito" id="carrito">
        <div class="header-carrito"><h2>Tu Carrito</h2></div>
        <div class="carrito-items"></div>
        <p class="cart-empty">El carrito está vacío</p>
        <div class="cart-total">
          <div class="fila"><strong>Total:</strong><span class="total-pagar">$0</span></div>
        </div>
        <div class="resumen-line">
          <span>Envío a domicilio</span>
          <div class="entrega-opciones">
            <input type="checkbox" id="envio"><label for="envio">Seleccionar envío</label>
          </div>
        </div>
        <form action="../pago.php" method="POST" id="form-pago">
          <input type="hidden" name="monto-total" id="monto-total">
          <button type="submit" class="btn-pagar">Pagar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- 💱 Indicador de moneda -->
<div class="divisa-selector-container" style="margin: 20px; text-align: center;">
  <span class="tasa-actualizacion">
    <?php if ($monedaSeleccionada != 'CLP'): ?>
      Tasa: 1 <?= $monedaSeleccionada ?> =
      <?= number_format($tasasCambio[$monedaSeleccionada]['tasa'], 2, '.', ',') ?> CLP
    <?php endif; ?> | Actualizado: <?= $fechaActualizacion ?>
  </span>
</div>

<!-- 🛒 Mostrador de herramientas -->
<section class="contenido">
  <div class="mostrador" id="mostrador">
    <div class="fila">
      <?php foreach ($products as $product):
        $imgSrc = !empty($product['imagen'])
          ? "data:image/jpeg;base64," . base64_encode($product['imagen'])
          : "img/placeholder.jpg";

        if ($monedaSeleccionada == 'CLP') {
          $precioMostrado = "$ " . number_format($product['precio'], 0, ",", ".");
        } else {
          $precioConvertido = $product['precio'] / $tasasCambio[$monedaSeleccionada]['tasa'];
          $precioMostrado = "$ " . number_format($precioConvertido, 2, ",", ".") . " " . $monedaSeleccionada;
          $precioMostrado .= "<br><small>($ " . number_format($product['precio'], 0, ",", ".") . " CLP)</small>";
        }
      ?>
      <div class="item" onclick="cargar(this)">
        <div class="contenedor-foto">
          <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($product['nombre']) ?>">
        </div>
        <p class="descripcion"><?= htmlspecialchars($product['nombre']) ?></p>
        <span class="precio"><?= $precioMostrado ?></span>
      </div>
      <?php endforeach; ?>
    </div>
    <?php if (empty($products)): ?>
      <p style="text-align:center;">No se encontraron herramientas para esta categoría.</p>
    <?php endif; ?>
  </div>

  <!-- Vista emergente de selección -->
  <div class="seleccion" id="seleccion">
    <div class="cerrar" onclick="cerrar()">&#x2715</div>
    <div class="info">
      <img src="img/1.png" alt="" id="img">
      <h2 id="modelo">Modelo ejemplo</h2>
      <p id="descripcion">Descripción del producto</p>
      <span class="precio" id="precio">$130</span>
      <div class="fila"><button id="btnAgregarAlCarrito">AGREGAR AL CARRITO</button></div>
    </div>
  </div>
</section>

<!-- 🎯 Scripts para carrito y visual -->
<script src="script/scripts.js"></script>
<script src="script/carrito.js"></script>
<script>
  document.getElementById('envio')?.addEventListener('change', function() {
    if (this.checked) window.open('ingreso_direccion.html', 'AgregarDireccion', 'width=400,height=300');
  });

  document.getElementById('form-pago')?.addEventListener('submit', function (e) {
    const total = document.querySelector('.total-pagar').textContent
      .replace('$', '')
      .replace(/\./g, '')
      .replace(',', '.');

    if (!total || isNaN(parseFloat(total))) {
      alert("Total inválido");
      e.preventDefault();
      return;
    }

    document.getElementById('monto-total').value = parseFloat(total);
  });

  document.getElementById('btnAgregarAlCarrito')?.addEventListener('click', function () {
    const titulo = document.getElementById('descripcion').innerText;
    const precio = document.getElementById('precio').innerText;
    const imagenSrc = document.getElementById('img').src;
    agregarItemAlCarrito(titulo, precio, imagenSrc);
  });
</script>
</body>
</html>
