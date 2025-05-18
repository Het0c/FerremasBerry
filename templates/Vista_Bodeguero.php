<?php
session_start();
require_once '../conexion.php';

// Usuario y sucursal (si aplican) recuperados de la sesión
$current_user = $_SESSION['current_user'] ?? ['nombre' => 'Invitado'];
$sucursal = $_SESSION['sucursal'] ?? null;

// Consulta para obtener productos y su stock
$sql = "SELECT idProducto, nombre, stock FROM producto";
$result = $conexion->query($sql);
$products = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
} else {
    die("Error en la consulta de productos: " . $conexion->error);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ferremas - Panel Bodeguero</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="static/bodeguero.css">
</head>
<body>
  <div class="dashboard-container">
    <header class="dashboard-header">
      <div class="header-left">
        <h1>
          <i class="fas fa-boxes"></i> Panel de Bodega <?php if ($sucursal) echo '- ' . htmlspecialchars($sucursal['nombre']); ?>
        </h1>
      </div>
      <div class="header-right">
        <span class="user-info"><?php echo htmlspecialchars($current_user['nombre']); ?></span>
        <a href="auth/logout.php" class="logout-btn">
          <i class="fas fa-sign-out-alt"></i> Salir
        </a>
      </div>
    </header>

    <div class="content-container">
      <div class="quick-actions">
        <a href="agregar_producto.php" class="btn btn-primary">
          <i class="fas fa-plus"></i> Agregar Producto
        </a>
      </div>

      <div class="tabs-container">
        <div class="tabs">
          <button class="tab-btn active" data-tab="stock">Stock de Productos</button>
        </div>

        <div class="tab-content active" id="stock">
          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>ID Producto</th>
                  <th>Nombre</th>
                  <th>Stock Actual</th>
                  <th>Acción</th>
                </tr>
              </thead>
              <tbody>
                <?php if (count($products) > 0): ?>
                  <?php foreach ($products as $product): ?>
                    <tr>
                      <td>#<?php echo htmlspecialchars($product['idProducto']); ?></td>
                      <td><?php echo htmlspecialchars($product['nombre']); ?></td>
                      <td><?php echo htmlspecialchars($product['stock']); ?></td>
                      <td class="actions-cell">
                        <!-- Formulario para actualizar el stock -->
                        <form action="../actualizar_stock.php" method="POST" style="display:inline;">
                          <input type="hidden" name="producto_id" value="<?php echo $product['idProducto']; ?>">
                          <input type="number" name="nuevo_stock" value="<?php echo $product['stock']; ?>" min="0" style="width: 70px;">
                          <button type="submit" class="btn-action update">
                            <i class="fas fa-sync-alt"></i> Actualizar
                          </button>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="4" class="no-data">No hay productos registrados</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div><!-- .table-container -->
        </div><!-- .tab-content -->
      </div><!-- .tabs-container -->
    </div><!-- .content-container -->
  </div><!-- .dashboard-container -->

  <script src="static/js/bodeguero.js"></script>
  <script>
    // Manejo básico de pestañas
    document.querySelectorAll('.tab-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(tabBtn => tabBtn.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById(btn.dataset.tab).classList.add('active');
      });
    });
  </script>
</body>
</html>
