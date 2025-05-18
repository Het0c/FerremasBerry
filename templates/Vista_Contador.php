<?php
session_start();

// Supón que ya tienes cargado el usuario actual en la sesión.
$current_user = $_SESSION['current_user'] ?? ['nombre' => 'Invitado'];

// Supón también que ya obtuviste la lista de pedidos desde la base de datos y la asignaste a $pedidos.
// Por ejemplo, podrías hacerlo mediante una consulta SQL y asignarlo a $pedidos como un arreglo asociativo.
// Para efectos de ejemplo, se usa $_SESSION['pedidos'] si existe; de lo contrario, se asume un arreglo vacío.
$pedidos = $_SESSION['pedidos'] ?? [];

// Definimos el margen de ganancia (en este ejemplo, el 30%)
$margen = 0.30;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ferremas - Panel Contador</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Asegúrate de tener el CSS en la carpeta static -->
    <link rel="stylesheet" href="static/bodeguero.css">
    <style>
      /* Ejemplo de estilos, ajusta según tus necesidades */
      body { font-family: 'Roboto', sans-serif; background-color: #f6f6f6; margin: 0; }
      .dashboard-container { max-width: 1200px; margin: 20px auto; background: #fff; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
      .dashboard-header { display: flex; justify-content: space-between; align-items: center; padding: 20px; background: #2d3e50; color: #fff; }
      .header-left h1 { margin: 0; font-size: 24px; }
      .header-right { font-size: 16px; }
      .header-right .logout-btn { color: #fff; text-decoration: none; margin-left: 15px; }
      .content-container { padding: 20px; }
      .data-table { width: 100%; border-collapse: collapse; }
      .data-table th, .data-table td { padding: 12px; border: 1px solid #ddd; text-align: center; }
      .data-table th { background-color: #f0f0f0; }
      .no-data { text-align: center; padding: 20px; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <div class="header-left">
                <h1><i class="fas fa-file-invoice-dollar"></i> Panel del Contador - Ganancias</h1>
            </div>
            <div class="header-right">
                <span class="user-info"><?php echo htmlspecialchars($current_user['nombre']); ?></span>
                <a href="auth/logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Salir
                </a>
            </div>
        </header>

        <div class="content-container">
            <div class="tabs-container">
                <div class="tabs">
                    <!-- Sólo una pestaña, ya que nos centramos en las ganancias -->
                    <button class="tab-btn active" data-tab="pedidos">Ganancias por Compras</button>
                </div>

                <div class="tab-content active" id="pedidos">
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>N° Pedido</th>
                                    <th>Cliente</th>
                                    <th>Productos</th>
                                    <th>Fecha</th>
                                    <th>Total Venta</th>
                                    <th>Ganancia (<?php echo ($margen*100)." %"; ?>)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $encontrado = false;
                                    foreach ($pedidos as $pedido):
                                        // Verificamos que exista el total de la venta.
                                        if (!empty($pedido['totalVenta'])):
                                            $encontrado = true;
                                            // Calculamos la ganancia
                                            $ganancia = $pedido['totalVenta'] * $margen;
                                ?>
                                    <tr>
                                        <td>#<?php echo $pedido['idPedido']; ?></td>
                                        <td><?php echo (isset($pedido['cliente']['nombre']) ? htmlspecialchars($pedido['cliente']['nombre']) : 'N/A'); ?></td>
                                        <td><?php echo (is_array($pedido['detalles']) ? count($pedido['detalles']) : 0); ?></td>
                                        <td>
                                            <?php 
                                                echo !empty($pedido['fechaPedido'])
                                                    ? date("d/m/Y H:i", strtotime($pedido['fechaPedido']))
                                                    : 'N/A'; 
                                            ?>
                                        </td>
                                        <td>$<?php echo number_format($pedido['totalVenta'], 2, ",", "."); ?></td>
                                        <td>$<?php echo number_format($ganancia, 2, ",", "."); ?></td>
                                    </tr>
                                <?php 
                                        endif;
                                    endforeach;
                                    if (!$encontrado):
                                ?>
                                <tr>
                                    <td colspan="6" class="no-data">No hay pedidos para calcular ganancias</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div><!-- .table-container -->
                </div><!-- .tab-content -->
            </div><!-- .tabs-container -->
        </div><!-- .content-container -->
    </div><!-- .dashboard-container -->

    <!-- Se enlaza el JS, asumiendo que se encuentra en static/js -->
    <script src="static/js/bodeguero.js"></script>
    <script>
      // Manejo de pestañas (aunque en este ejemplo hay sólo una pestaña)
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
