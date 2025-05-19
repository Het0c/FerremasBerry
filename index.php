<?php
session_start();
require_once "conexion.php"; // Establece la conexión con la BD

// ────────────── Consulta: Productos con Descuento ──────────────
$sqlDiscount = "SELECT 
                    p.idProducto,
                    p.nombre,
                    p.precio,
                    p.imagen,
                    d.porcentaje
                FROM producto p
                INNER JOIN descuento d ON p.idDescuento = d.idDescuento";
$resultDiscount = $conexion->query($sqlDiscount);
$discountProducts = [];
if ($resultDiscount) {
    while ($row = $resultDiscount->fetch_assoc()) {
        $discountProducts[] = $row;
    }
} else {
    die("Error en la consulta de productos con descuento: " . $conexion->error);
}

// ────────────── Consulta: Categorías con Subcategorías ──────────────
$categories = [];
$sqlCategories = "SELECT 
                    c.idCategoria, 
                    c.nombre AS nombreCategoria, 
                    s.idSubcategoria, 
                    s.descripcion AS nombreSub, 
                    s.slug 
                  FROM categoria c 
                  LEFT JOIN subcategoria s ON c.idCategoria = s.idCategoria 
                  ORDER BY c.nombre, s.descripcion";
$resultCategories = $conexion->query($sqlCategories);
if ($resultCategories) {
    while ($row = $resultCategories->fetch_assoc()) {
        $catId = $row["idCategoria"];
        if (!isset($categories[$catId])) {
            $categories[$catId] = [
                "nombre"        => $row["nombreCategoria"],
                "subcategorias" => []
            ];
        }
        // Agregar la subcategoría si existe
        if ($row["idSubcategoria"]) {
            $categories[$catId]["subcategorias"][] = [
                "nombre" => $row["nombreSub"],
                "slug"   => !empty($row["slug"]) ? $row["slug"] : $row["idSubcategoria"]
            ];
        }
    }
} else {
    die("Error en la consulta de categorías: " . $conexion->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Ferremas</title>
	<link rel="stylesheet" href="templates/static/styles.css" />
	<link rel="stylesheet" href="templates/static/estiloo2.css" />
	<link rel="stylesheet" href="templates/static/estilo3.css" />
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
		integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
		integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
		crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
	<header>
		<div class="container-hero">
			<div class="container hero">


				<div class="container-logo">
					<i class="fa-solid fa-person-digging"></i>
					<h1 class="logo"><a href="/FERREMAS">FERREMAS</a></h1>
				</div>

				<div class="container-user">
				    <i class="fa-solid fa-user" id="userIcon" style="cursor: pointer;"></i>
				    <span id="userGreeting"></span> <!-- Aquí se insertará el saludo dinámico -->
				
				    <div class="user-popup" id="userPopup">
				        <h3>Iniciar Sesión</h3>
				        <form id="loginForm" action="login.php" method="POST">
				            <label for="loginEmail">Email:</label>
				            <input type="email" name="email" id="loginEmail" placeholder="Introduce tu email" required>
						
				            <label for="loginPassword">Contraseña:</label>
				            <input type="password" name="password" id="loginPassword" placeholder="Contraseña" required>
						
				            <button type="submit">Entrar</button>
				        </form>
				        <p style="margin-top:10px; text-align: center;">
				            ¿No tiene cuenta? <a href="registro.php">Regístrese</a>
				        </p>
				    </div>
				</div>



				<!-- Carrito de Compras -->


				<div class="container-icon">

					<div class="container-cart-icon">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
							stroke="currentColor" class="icon-cart">
							<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993
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
    <!-- Aquí se insertan dinámicamente los productos del carrito -->
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
      <!-- Usamos un checkbox. Al cambiar su estado se dispara la apertura del popup -->
      <input type="checkbox" id="envio" name="envio_domicilio" value="envio">
      <label for="envio">Seleccionar envío</label>
    </div>
  </div>

  <!-- Formulario de Pago -->
  <form action="pago.php" method="POST" id="form-pago">
    <input type="hidden" name="monto-total" id="monto-total" value="">
    <!-- Campo oculto para almacenar la dirección ingresada en el popup -->
    <input type="hidden" name="direccion" id="direccion-field" value="">
    <button type="submit" class="btn-pagar">Pagar</button>
  </form>
</div>

<script>
// Al marcar el checkbox, se abre un popup para ingresar la dirección
document.getElementById('envio').addEventListener('change', function(){
    if (this.checked) {
         // Abre una ventana emergente pequeña para agregar la dirección
         window.open('templates/ingreso_direccion.html', 'AgregarDireccion', 'width=400,height=300');
    }
});

// Procesa el formulario de pago
document.getElementById('form-pago').addEventListener('submit', function(e) {
    const totalStr = document.querySelector('.total-pagar').textContent
        .replace('$', '')
        .replace(/\./g, '')
        .replace(/,/g, '');
  
    if (!totalStr || isNaN(parseFloat(totalStr))) {
         alert("Total inválido");
         e.preventDefault();
         return;
    }
  
    // Asegura que el valor tenga dos decimales y se asigne al campo oculto
    document.getElementById('monto-total').value = parseFloat(totalStr).toFixed(2);
});
</script>

					</div>
				</div>



			</div>


		</div>
		</div>



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
	</header>

	<section class="banner">
		<div class="content-banner">
			<p>Calidad y Precio</p>
			<h2>Mira nuestros productos<br />Para toda mano de obra</h2>
			<a href="templates/herramientas.php" target="_blank">Comprar ahora</a>

		</div>
	</section>

	<main class="main-content">
		<section class="container container-features">
			<div class="card-feature">
				<i class="fa-solid fa-plane-up"></i>
				<div class="feature-content">
					<span>Envío gratuito en tus tres primeros pedidos</span>
					<p>En pedido superior a $150</p>
				</div>
			</div>
			<div class="card-feature">
				<i class="fa-solid fa-wallet"></i>
				<div class="feature-content">
					<span>Contrareembolso</span>
					<p>100% garantía de devolución de dinero</p>
				</div>
			</div>
			<div class="card-feature">
				<i class="fa-solid fa-gift"></i>
				<div class="feature-content">
					<span>Tarjeta regalo especial</span>
					<p>Ofrece bonos especiales con regalo</p>
				</div>
			</div>

			</div>
		</section>

		<section class="container top-categories">
			<h1 class="heading-1">LANZAMIENTOS RECIENTES</h1>
			<div class="container-categories">
				<div class="card-category category-moca">
					<p>NUEVOS UNIFORME</p>
					<span>Ver más</span>
				</div>
				<div class="card-category category-expreso">
					<p>NUEVAS HERRAMIENTAS</p>
					<span>Ver más</span>
				</div>
				<div class="card-category category-capuchino">
					<p>VARIEDAD DE CALZADO</p>
					<span>Ver más</span>
				</div>
			</div>
		</section>

<section class="container top-products">
    <h1 class="heading-1">PRODUCTOS EN PROMOCIÓN</h1>
    <div class="container-products">
        <?php foreach ($discountProducts as $producto): ?>
            <div class="card-product">
                <div class="container-img">
                    <?php if (!empty($producto['imagen'])): ?>
                        <img src="data:image/jpeg;base64,<?= base64_encode($producto['imagen']) ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>" />
                    <?php else: ?>
                        <!-- En caso de que no haya imagen, se muestra un placeholder -->
                        <img src="img/placeholder.jpg" alt="Sin imagen disponible">
                    <?php endif; ?>
                    <span class="discount">-<?= $producto['porcentaje'] ?>%</span>
                    <div class="button-group">
                        <span><i class="fa-regular fa-eye"></i></span>
                        <span><i class="fa-regular fa-heart"></i></span>
                        <span><i class="fa-solid fa-code-compare"></i></span>
                    </div>
                </div>
                <div class="content-card-product">
                    <div class="stars">
                        <!-- Se muestran 4 estrellas llenas y una vacía de forma estática -->
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-regular fa-star"></i>
                    </div>
                    <h3><?= htmlspecialchars($producto['nombre']) ?></h3>
                    <?php 
                        // Calcula el precio final aplicando el descuento
                        $precio_original = $producto['precio'];
                        $porcentaje = $producto['porcentaje'];
                        $descuento = ($precio_original * $porcentaje) / 100;
                        $precio_final = $precio_original - $descuento;
                    ?>
                    <p class="price">$<?= number_format($precio_final) ?></p>
                    <button class="add-cart">Agregar al carrito</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>



		<section class="container specials">
			<h1 class="heading-1">LANZAMIENTOS ESPECIALES</h1>

			<div class="container-products">
				<!-- Producto 1 -->
				<div class="card-product">
					<div class="container-img">
						<img src="img/ABRIGO.avif" alt="Cafe Irish" />

						<div class="button-group">
							<span><i class="fa-regular fa-eye"></i></span>
							<span><i class="fa-regular fa-heart"></i></span>
							<span><i class="fa-solid fa-code-compare"></i></span>
						</div>
					</div>
					<div class="content-card-product">
						<div class="stars">
							<i class="fa-solid fa-star"></i>
							<i class="fa-solid fa-star"></i>
							<i class="fa-solid fa-star"></i>
							<i class="fa-solid fa-star"></i>
							<i class="fa-regular fa-star"></i>
						</div>
						<h3>ABRIGO IMPERMEABLE</h3>
						<p class="price">$4.600 </p>
						<button class="add-cart">Agregar al carrito</button>
					</div>
				</div>
				<!-- Producto 2 -->
				<div class="card-product">
					<div class="container-img">
						<img src="img/TIRANTES.webp" alt="Cafe incafe-ingles.jpg" />

						<div class="button-group">
							<span><i class="fa-regular fa-eye"></i></span>
							<span><i class="fa-regular fa-heart"></i></span>
							<span><i class="fa-solid fa-code-compare"></i></span>
						</div>
					</div>
					<div class="content-card-product">
						<div class="stars">
							<i class="fa-solid fa-star"></i>
							<i class="fa-solid fa-star"></i>
							<i class="fa-solid fa-star"></i>
							<i class="fa-solid fa-star"></i>
							<i class="fa-regular fa-star"></i>
						</div>
						<h3>VESTUARIO LABORAL</h3>
						<p class="price">$5.700</p>
						<button class="add-cart">Agregar al carrito</button>
					</div>
				</div>
				<!--  -->
				<div class="card-product">
					<div class="container-img">
						<img src="img/ALGODON.avif" alt="Cafe Viena" />

						<div class="button-group">
							<span><i class="fa-regular fa-eye"></i></span>
							<span><i class="fa-regular fa-heart"></i></span>
							<span><i class="fa-solid fa-code-compare"></i></span>
						</div>
					</div>
					<div class="content-card-product">
						<div class="stars">
							<i class="fa-solid fa-star"></i>
							<i class="fa-solid fa-star"></i>
							<i class="fa-solid fa-star"></i>
							<i class="fa-solid fa-star"></i>
							<i class="fa-regular fa-star"></i>
						</div>
						<h3>ROPA DE TRABAJO DE ALGODÓN</h3>
						<p class="price">$3.850 </p>
						<button class="add-cart">Agregar al carrito</button>
					</div>
				</div>
				<!--  -->
				<div class="card-product">
					<div class="container-img">
						<img src="img/TRTRTRTR.jpg" alt="Cafe Liqueurs" />
						<div class="button-group">
							<span><i class="fa-regular fa-eye"></i></span>
							<span><i class="fa-regular fa-heart"></i></span>
							<span><i class="fa-solid fa-code-compare"></i></span>
						</div>
					</div>
					<div class="content-card-product">
						<div class="stars">
							<i class="fa-solid fa-star"></i>
							<i class="fa-solid fa-star"></i>
							<i class="fa-solid fa-star"></i>
							<i class="fa-solid fa-star"></i>
							<i class="fa-regular fa-star"></i>
						</div>
						<h3>UNIFROME DE TRABAJO</h3>
						<p class="price">$5.600</p>
						<button class="add-cart">Agregar al carrito</button>
					</div>
				</div>
			</div>
		</section>


	</main>

	<footer class="footer">

		<div class="formulario-contacto contenedor">

			<div class="informacion-contacto">
				<h3>Contactanos</h3>
				<p><i class="fas fa-map-marker-alt"></i>SANTIAGO EU 74631</p>
				<p><i class="fas fa-envelope"></i> FERREMAS@GMAIL.COM</p>
				<p><i class="fas fa-phone-alt"></i>+5698743521</p>
				<div class="redes-sociales">
					<i class="fab fa-facebook-square"></i>
					<i class="fab fa-twitter-square"></i>
					<i class="fab fa-instagram"></i>
				</div>
			</div>

			<form class="formulario" action="conexion.php" method="POST">
				<div class="input-formulario">
					<label for="nombre">Nombre</label>
					<input type="text" placeholder="Pedro" id="nombre" name="nombre" required>
				</div>
				<div class="input-formulario">
					<label for="apellido">Apellido</label>
					<input type="text" placeholder="Pérez" id="apellido" name="apellido" required>
				</div>
				<div class="input-formulario">
					<label for="correo">Correo</label>
					<input type="email" placeholder="ejemplo@ejemplo.com" id="correo" name="correo" required>
				</div>
				<div class="input-formulario">
					<label for="telefono">Teléfono</label>
					<input type="tel" placeholder="+569 6327 6109" id="telefono" name="telefono" required>
				</div>
				<div class="input-formulario">
					<label for="mensaje">Mensaje</label>
					<textarea placeholder="Por favor, indique estos datos del producto. Ejem:
Código:FER-12345,
Marca:Bosch,
Código: BOS-67890,
Nombre: Taladro Percutor Bosch,
Precio: 7.000
Fecha: 2023-05-10T03,
						" id="mensaje" name="mensaje" required></textarea>
				</div>
				<div class="btn-formulario">
					<button name="submit" type="submit" id="contact-submit">Enviar</button>
				</div>
			</form>

		</div>

		<div class="container container-footer">
			<div class="menu-footer">
				<div class="contact-info">
					<p class="title-footer">Información de Contacto</p>
					<ul>
						<li>
							Dirección: 71 Pennington Lane Vernon Rockville, CT
							06066
						</li>
						<li>Teléfono: 123-456-7890</li>
						<li>Fax: 55555300</li>
						<li>EmaiL: OLABUENAS@GMAIL.COM</li>
					</ul>
					<div class="social-icons">
						<span class="facebook">
							<i class="fa-brands fa-facebook-f"></i>
						</span>
						<span class="twitter">
							<i class="fa-brands fa-twitter"></i>
						</span>
						<span class="youtube">
							<i class="fa-brands fa-youtube"></i>
						</span>
						<span class="pinterest">
							<i class="fa-brands fa-pinterest-p"></i>
						</span>
						<span class="instagram">
							<i class="fa-brands fa-instagram"></i>
						</span>
					</div>
				</div>

				<div class="information">
					<p class="title-footer">Información</p>
					<ul>
						<li><a href="#">Acerca de Nosotros</a></li>
						<li><a href="#">Información Delivery</a></li>
						<li><a href="#">Politicas de Privacidad</a></li>
						<li><a href="#">Términos y condiciones</a></li>
						<li><a href="#">Contactános</a></li>
					</ul>
				</div>

				<div class="my-account">
					<p class="title-footer">Mi cuenta</p>

					<ul>
						<li><a href="#">Mi cuenta</a></li>
						<li><a href="#">Historial de ordenes</a></li>
						<li><a href="#">Lista de deseos</a></li>
						<li><a href="#">Boletín</a></li>
						<li><a href="#">Reembolsos</a></li>
					</ul>
				</div>


			</div>

			<div class="carrito" id="carrito">
				<div class="header-carrito">
					<h2>Tu Carrito</h2>
				</div>
				<div class="carrito-items">

				</div>
				<div class="carrito-total">
					<div class="fila">
						<strong>Tu Total</strong>
						<span class="carrito-precio-total">$0,00</span>
					</div>
					<button class="btn-pagar">Pagar <i class="fa-solid fa-bag-shopping"></i></button>
				</div>
			</div>


			<div class="copyright">


				<img src="img/payment.png" alt="Pagos">
			</div>
		</div>

	</footer>
	<script src="templates/script/carrito.js"></script>
	<script src="templates/script/popupUser.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
		crossorigin="anonymous"></script>

	<div id="userStatus"></div>


</body>

</html>