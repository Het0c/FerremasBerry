<?php
// Aquí se asume que estas variables han sido definidas previamente, probablemente mediante consultas a la BD.
if (!isset($hoy)) {
    $hoy = date("d/m/Y");
}
if (!isset($current_user)) {
    $current_user = ['nombre' => 'Admin'];
}
if (!isset($ventas_hoy)) {
    $ventas_hoy = 123456; // Ejemplo numérico
}
if (!isset($pedidos_hoy)) {
    $pedidos_hoy = 42;
}
if (!isset($total_usuarios)) {
    $total_usuarios = 100;
}
if (!isset($pedidos_recientes)) {
    $pedidos_recientes = []; // O bien, un array con pedidos
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel Admin - Ferremas</title>
  <link rel="stylesheet" href="static/admin.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
  <div class="admin-container">
    <aside class="sidebar">
      <h2><i class="fas fa-tools"></i> Panel Admin</h2>
      <nav>
        <ul>
          <li><a href="/admin/dashboard" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
          <li><a href="/admin/productos"><i class="fas fa-boxes"></i> Registrar Productos</a></li>
          <li><a href="/registra"><i class="fas fa-user-plus"></i> Registro De Empleados</a></li>
          <li><a href="/logout"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
        </ul>
      </nav>
    </aside>

    <main class="content">
      <div class="content-header">
        <h1>Dashboard Administrador - <?php echo htmlspecialchars($hoy); ?></h1>
        <div class="user-info">
          <span><?php echo htmlspecialchars($current_user['nombre']); ?></span>
          <i class="fas fa-user-circle"></i>
        </div>
      </div>

      <!-- Estadísticas principales -->
      <div class="stats">
        <div class="stat-card">
          <h3>Ventas del Mes</h3>
          <p>$<?php echo number_format($ventas_hoy, 0, ",", "."); ?></p>
        </div>
        <div class="stat-card">
          <h3>Pedidos del mes</h3>
          <p><?php echo $pedidos_hoy; ?></p>
        </div>
        <div class="stat-card">
          <h3>Usuarios Registrados</h3>
          <p><?php echo $total_usuarios; ?></p>
        </div>
      </div>

      <!-- Tabla de pedidos recientes -->
      <div class="recent-orders">
        <h2><i class="fas fa-history"></i> Últimos Pedidos</h2>
        <table>
          <thead>
            <tr>
              <th>ID Pedido</th>
              <th>Cliente</th>
              <th>Productos</th>
              <th>Fecha</th>
              <th>Total</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($pedidos_recientes)): ?>
              <?php foreach ($pedidos_recientes as $pedido): ?>
                <tr>
                  <td>#<?php echo $pedido['idPedido']; ?></td>
                  <td>
                    <?php
                      echo (isset($pedido['cliente']) && isset($pedido['cliente']['nombre']))
                        ? htmlspecialchars($pedido['cliente']['nombre'])
                        : 'N/A';
                    ?>
                  </td>
                  <td>
                    <ul class="productos-list">
                      <?php if (!empty($pedido['detalles'])): ?>
                        <?php foreach ($pedido['detalles'] as $detalle): ?>
                          <li>
                            <?php
                              echo htmlspecialchars($detalle['producto']['nombreProducto']) .
                                   " (x" . $detalle['cantidad'] . ")";
                            ?>
                          </li>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </ul>
                  </td>
                  <td>
                    <?php
                      if (!empty($pedido['fechaPedido'])) {
                        echo date("d/m/Y H:i", strtotime($pedido['fechaPedido']));
                      } else {
                        echo 'N/A';
                      }
                    ?>
                  </td>
                  <td>
                    $<?php echo !empty($pedido['total']) ? number_format($pedido['total'], 0, ",", ".") : '0'; ?>
                  </td>
                  <td>
                    <?php 
                      $estado = (isset($pedido['etapa']) && !empty($pedido['etapa']['descripcion']))
                                  ? $pedido['etapa']['descripcion']
                                  : 'Sin estado';
                      $status_class = "";
                      if (isset($pedido['etapa']) && !empty($pedido['etapa']['descripcion'])) {
                        $status_class = strtolower(str_replace(' ', '-', $pedido['etapa']['descripcion']));
                      }
                    ?>
                    <span class="status <?php echo $status_class; ?>">
                      <?php echo htmlspecialchars($estado); ?>
                    </span>
                  </td>
                  <td>
                    <a href="pedidos/detalle_pedido.php?pedido_id=<?php echo $pedido['idPedido']; ?>" class="btn-action">
                      <i class="fas fa-eye"></i> Ver
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7">No hay pedidos recientes</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</body>
</html>
