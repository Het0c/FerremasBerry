<?php
session_start();
require_once '../conexion.php';

// Consulta para obtener todos los productos
$result = $conexion->query("SELECT * FROM producto");
$products = $result->fetch_all(MYSQLI_ASSOC);

// Obtener la moneda seleccionada; por defecto CLP
$monedaSeleccionada = 'CLP';
if (isset($_GET['moneda'])) {
    $monedaSeleccionada = $_GET['moneda'];
}

// Definir tasas de cambio (ejemplo; en producción, obtener de una API externa)
$tasasCambio = [
    'USD' => ['tasa' => 800],
    'EUR' => ['tasa' => 900],
    'BRL' => ['tasa' => 150],
    'COP' => ['tasa' => 0.22],
    'MXN' => ['tasa' => 40]
];

// Obtener la hora de actualización (para mostrar información en la vista)
$fechaActualizacion = date("H:i");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="static/estilos.css">
  <link rel="stylesheet" href="static/styles.css" />
  <link rel="stylesheet" href="static/estiloo2.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  <title>Ferremas - Productos</title>
</head>
<body>

  <div class="container-hero">
    <div class="container hero">
      <div class="container-logo">
        <i class="fa-solid fa-person-digging"></i>
        <h1 class="logo"><a href="/FERREMAS">FERREMAS</a></h1>
      </div>

      <div class="container-icon">
        <!-- Icono del carrito -->
        <div class="container-cart-icon">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none"
               viewBox="0 0 24 24" stroke-width="1.5"
               stroke="currentColor" class="icon-cart">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993
                     l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125
                     1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0
                     015.513 7.5h12.974c.576 0 1.059.435 1.119
                     1.007zM8.625 10.5a.375.375 0 11-.75 0
                     .375.375 0 01.75 0zm7.5 0a.375.375 0
                     11-.75 0 .375.375 0 01.75 0z" />
          </svg>
        </div>
        
        <!-- Contenedor del carrito -->
        <div class="carrito" id="carrito">
          <div class="header-carrito">
            <h2>Tu Carrito</h2>
          </div>

          <div class="carrito-items">
            <!-- Se insertarán los productos del carrito -->
          </div>

          <p class="cart-empty">El carrito está vacío</p>

          <div class="cart-total">
            <div class="fila">
              <strong>Total:</strong>
              <span class="total-pagar">$0</span>
            </div>
          </div>
          
          <div class="resumen-line">
            <span>Envío a domicilio</span>
            <div class="entrega-opciones" id="entrega-opciones">
              <!-- Checkbox para seleccionar el envío -->
              <input type="checkbox" id="envio" name="envio_domicilio" value="envio">
              <label for="envio">Seleccionar envío</label>
            </div>
          </div>

          <form action="../pago.php" method="POST" id="form-pago">
            <input type="hidden" name="monto-total" id="monto-total" value="">
            <button type="submit" class="btn-pagar">Pagar</button>
          </form>

          <script>
            // Abre un popup para ingresar la dirección al marcar envío a domicilio
            document.getElementById('envio').addEventListener('change', function(){
              if (this.checked) {
                window.open('ingreso_direccion.html', 'AgregarDireccion', 'width=400,height=300');
              }
            });

            // Validación del monto total al enviar el formulario de pago
            document.querySelector('#form-pago')?.addEventListener('submit', function (e) {
              const total = document.querySelector('.total-pagar').textContent
                .replace('$', '')
                .replace(/\./g, '')
                .replace(',', '.');

              if (!total || isNaN(parseFloat(total))) {
                alert("Total inválido");
                e.preventDefault();
                return;
              }

              document.querySelector('#monto-total').value = parseFloat(total);
            });
          </script>
          
        </div>
      </div>
    </div>
  </div>

  <!-- Selector de divisa -->
  <div class="divisa-selector-container" style="margin: 20px; text-align: center;">
    <form method="get" action="">
      <label for="moneda">Mostrar precios en:</label>
      <select name="moneda" id="moneda" onchange="this.form.submit()">
         <option value="CLP" <?= ($monedaSeleccionada == 'CLP') ? 'selected' : '' ?>>Pesos Chilenos (CLP)</option>
         <option value="USD" <?= ($monedaSeleccionada == 'USD') ? 'selected' : '' ?>>Dólares (USD)</option>
         <option value="EUR" <?= ($monedaSeleccionada == 'EUR') ? 'selected' : '' ?>>Euros (EUR)</option>
         <option value="BRL" <?= ($monedaSeleccionada == 'BRL') ? 'selected' : '' ?>>Reales (BRL)</option>
         <option value="COP" <?= ($monedaSeleccionada == 'COP') ? 'selected' : '' ?>>Pesos Colombianos (COP)</option>
         <option value="MXN" <?= ($monedaSeleccionada == 'MXN') ? 'selected' : '' ?>>Pesos Mexicanos (MXN)</option>
      </select>
    </form>
    <span class="tasa-actualizacion">
      <?php if ($monedaSeleccionada != 'CLP' && isset($tasasCambio[$monedaSeleccionada])): ?>
          Tasa: 1 <?= $monedaSeleccionada ?> = <?= number_format($tasasCambio[$monedaSeleccionada]['tasa'], 2, ".", ",") ?> CLP
      <?php elseif ($monedaSeleccionada != 'CLP'): ?>
          Tasa no disponible para <?= htmlspecialchars($monedaSeleccionada) ?>
      <?php endif; ?>
       | Actualizado: <?= $fechaActualizacion ?>
    </span>
  </div>
  
  <section class="contenido">
    <!-- Mostrador de productos dinámico -->
    <div class="mostrador" id="mostrador">
      <div class="fila">
        <?php foreach ($products as $product):
          // Si existe imagen, conviértela a base64; de lo contrario usa un placeholder.
          if (!empty($product['imagen'])) {
            $imgSrc = "data:image/jpeg;base64," . base64_encode($product['imagen']);
          } else {
            $imgSrc = "img/placeholder.jpg";
          }
          
          // Formatear el precio según la moneda seleccionada.
          if ($monedaSeleccionada == 'CLP') {
            $precioMostrado = "$ " . number_format($product['precio'], 0, ",", ".");
          } else {
            if (isset($tasasCambio[$monedaSeleccionada])) {
              $precioConvertido = $product['precio'] / $tasasCambio[$monedaSeleccionada]['tasa'];
              $precioMostrado = "$ " . number_format($precioConvertido, 2, ",", ".") . " " . $monedaSeleccionada;
              $precioMostrado .= "<br><small>($ " . number_format($product['precio'], 0, ",", ".") . " CLP)</small>";
            } else {
              $precioMostrado = "<span class='error'>Tasa no disponible</span>";
            }
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
    </div>

    <!-- Sección de selección, por ejemplo para mostrar detalles de un producto -->
    <div class="seleccion" id="seleccion">
      <div class="cerrar" onclick="cerrar()">&#x2715</div>
      <div class="info">
        <img src="img/1.png" alt="" id="img">
        <h2 id="modelo">NIKE MODEL 1</h2>
        <p id="descripcion">Descripción Modelo 1</p>
        <span class="precio" id="precio">$ 130</span>
        <div class="fila">
          <button id="btnAgregarAlCarrito">AGREGAR AL CARRITO</button>
        </div>
      </div>
    </div>
  </section>

  <nav aria-label="Page navigation example">
    <ul class="pagination">
      <li class="page-item">
        <a class="page-link" href="#" aria-label="Previous">
          <span aria-hidden="true">&laquo;</span>
        </a>
      </li>
      <li class="page-item"><a class="page-link" href="#">1</a></li>
      <li class="page-item"><a class="page-link" href="#">2</a></li>
      <li class="page-item"><a class="page-link" href="#">3</a></li>
      <li class="page-item">
        <a class="page-link" href="#" aria-label="Next">
          <span aria-hidden="true">&raquo;</span>
        </a>
      </li>
    </ul>
  </nav>

  <section class="banner">
    <div class="content-banner">
      <a href="index.html" class="btn-volver">Volver al menú</a>
    </div>
  </section>

  <script>
    // Ejemplo: al hacer clic en "AGREGAR AL CARRITO" se captura la información del producto
    document.addEventListener('DOMContentLoaded', function () {
      const btn = document.getElementById('btnAgregarAlCarrito');
      btn.addEventListener('click', function () {
        const titulo = document.getElementById('descripcion').innerText;
        const precio = document.getElementById('precio').innerText;
        const imagenSrc = document.getElementById('img').src;
        agregarItemAlCarrito(titulo, precio, imagenSrc);
      });
    });
  </script>

  <script src="script/scripts.js"></script>
  <script src="script/carrito.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>
</html>
