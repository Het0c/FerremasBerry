<?php


// Asegúrate de que la ruta sea correcta según la estructura de directorios
require_once 'conexion.php';



ini_set('display_errors', 1);
error_reporting(E_ALL);

// Función para llamar al WS, igual que en tu ejemplo.
function get_ws($data, $method, $type, $endpoint) {
    $curl = curl_init();
    $TbkApiKeyId = '597055555532';
    $TbkApiKeySecret = '579B532A7440BB0C9079DED94D31EA1615BACEB56610332264630D42D0A36B1C';
    $url = ($type == 'live' ? "https://webpay3g.transbank.cl" : "https://webpay3gint.transbank.cl") . $endpoint;

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => [
            "Tbk-Api-Key-Id: $TbkApiKeyId",
            "Tbk-Api-Key-Secret: $TbkApiKeySecret",
            "Content-Type: application/json"
        ],
    ]);

    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response);
}

$baseurl = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
$action = $_GET["action"] ?? 'init';
$message = '';
$submit = '';
$url_tbk = '';
$token = '';
$response = null;

// En una integración real, es probable que inicies la transacción en "init"
// y que recibas los resultados (incluido el estado) en "getResult".
switch ($action) {

    case "init":
        $buy_order = rand();
        $session_id = rand();

        $amount = 0;
        if (isset($_POST['monto-total']) && is_numeric($_POST['monto-total'])) {
            $amount = round(floatval($_POST['monto-total']));
        }
        $message .= "<p><strong>Monto recibido:</strong> $amount</p>";

        if ($amount <= 0) {
            $message .= "<p style='color:red;'>Error: monto inválido.</p>";
            break;
        }

        $return_url = $baseurl . "?action=getResult";
        $type = "sandbox";
        $data = json_encode([
            "buy_order"    => "$buy_order",
            "session_id"   => "$session_id",
            "amount"       => $amount,
            "return_url"   => $return_url
        ]);

        $method = 'POST';
        $endpoint = '/rswebpaytransaction/api/webpay/v1.0/transactions';
        $response = get_ws($data, $method, $type, $endpoint);

        $message .= "<pre>" . print_r($response, true) . "</pre>";

        if (isset($response->url) && isset($response->token)) {
            $url_tbk = $response->url;
            $token   = $response->token;
            $submit  = 'Pagar ahora';
        } else {
            $message .= "<p style='color:red;'>Error iniciando la transacción.</p>";
        }
        break;

case "getResult":
    // Verificamos si se recibió token_ws
    if (isset($_POST['token_ws'])) {
        $token = $_POST['token_ws'];
        
        // Llamada a la API de Transbank para obtener el resultado de la transacción.
        $endpoint = "/rswebpaytransaction/api/webpay/v1.0/transactions/{$token}";
        $response = get_ws('', 'PUT', 'sandbox', $endpoint);
        
        // Imprimimos la respuesta para depurar
        $message .= "<pre>" . print_r($response, true) . "</pre>";
        
        if (isset($response->status) && $response->status === "AUTHORIZED") {            
            $totalPrice = $response->amount ?? 'N/A'; // Total de la transacción
            $orderDate  = date("Y-m-d H:i:s");
            
            // Iniciar la sesión para obtener datos del usuario
            session_start();
            // Se asume que el usuario ya está almacenado en la sesión (por ejemplo, en $_SESSION["usuario"])
            $idusuario = isset($_SESSION['usuario']['id']) ? intval($_SESSION['usuario']['id']) : 0;
            $username  = $_SESSION['usuario']['nombre'] ?? 'Usuario desconocido';
            
            // Obtenemos la dirección de envío (puede venir de un campo oculto o por POST)
            $tempShippingAddress = $_POST['tempShippingAddress'] ?? '';
            
            // Insertar el pedido en la base de datos
            // Se asume que la conexión ya está establecida en $conexion (incluida en la cabecera)
            $idsucursal = "1"; // Puedes asignar un valor o variable según corresponda
            $sql_insert = "INSERT INTO pedido (idusuario, fechapedido, total, direccionenvio, idsucursal) VALUES (?, ?, ?, ?, ?)";
            
            if ($stmt = $conexion->prepare($sql_insert)) {
                // Bind: 
                // "i" para idusuario (entero)
                // "s" para fechapedido, total, direccionenvio y idsucursal (cadenas)
                $stmt->bind_param("issss", $idusuario, $orderDate, $totalPrice, $tempShippingAddress, $idsucursal);
                
                if ($stmt->execute()) {
                    $message .= "<p style='color:green;'>El pedido se ha guardado en la base de datos.</p>";
                } else {
                    $message .= "<p style='color:red;'>Error al guardar el pedido: " . $stmt->error . "</p>";
                }
                
                $stmt->close();
            } else {
                $message .= "<p style='color:red;'>Error preparando la consulta: " . $conexion->error . "</p>";
            }
            
            // Mostrar la información de la transacción
            $message .= "<p style='color:green;'>Pago exitoso.</p>";
            $message .= "<p><strong>Total:</strong> $totalPrice</p>";
            $message .= "<p><strong>Fecha del pedido:</strong> $orderDate</p>";
            $message .= "<p><strong>Usuario:</strong> $username</p>";
            if (!empty($tempShippingAddress)) {
                $message .= "<p><strong>Dirección de envío:</strong> $tempShippingAddress</p>";
            }
            
            // Puedes continuar con otras acciones, como redirigir al usuario o mostrar un botón para volver al inicio, etc.
            $url_inicio = "/FERREMAS/index.html";
            $boton_volver = '
                <div style="text-align:center; margin-top:40px;">
                    <h2 style="color:green;">✅ ¡Compra exitosa!🎉🎉</h2>
                    <a href="'.$url_inicio.'" 
                       style="display:inline-block;margin-top:20px;padding:12px 25px;
                              background:#28a745;color:white;border:none;
                              border-radius:5px;text-decoration:none;font-size:16px;
                              font-weight:bold;transition:.3s;">
                        Volver al inicio
                    </a>
                </div>';
            
        } else {
            // En caso de que la transacción no esté autorizada o se haya rechazado     
            $message .= "<p style='color:red;'>Pago rechazado o error en la transacción.</p>";
        }
    } else {
        // Si no se recibió token_ws, no se tiene información del pago.
        $message .= "<p style='color:red;'>No se recibió token_ws del pago.</p>";
    }
    break;

try {
    $stmt = $conexion->prepare($sql_insert);
    if (!$stmt) {
        throw new Exception("Error preparando la consulta: " . $conexion->error);
    }
    $stmt->bind_param("issss", $idusuario, $orderDate, $totalPrice, $tempShippingAddress, $idsucursal);
    if (!$stmt->execute()) {
        throw new Exception("Error al guardar el pedido: " . $stmt->error);
    }
    $stmt->close();
    $message .= "<p style='color:green;'>El pedido se ha guardado en la base de datos.</p>";
} catch (Exception $e) {
    $message .= "<p style='color:red;'>Error en el proceso: " . $e->getMessage() . "</p>";
    // Aquí podrías opcionalmente redirigir al usuario a una página segura o al index sin cerrar la sesión.
}

}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo ($action == "init" ? "Redirigiendo a Webpay" : "Resultado de la transacción"); ?></title>
</head>
<body>
    <?= $message ?>

    <?php if (isset($boton_volver)) echo $boton_volver; ?>

    <?php if (!empty($url_tbk) && !empty($token)): ?>
        <!-- Formulario que redirige a Webpay en el flujo de pago -->
        <form id="webpay-form" method="POST" action="<?= $url_tbk ?>">
            <input type="hidden" name="token_ws" value="<?= $token ?>">
            <button type="submit"><?= $submit ?></button>
        </form>
        <script>
            // Redirigimos automáticamente el formulario a Webpay
            document.getElementById('webpay-form').submit();
        </script>
    <?php endif; ?>

    <?php
    // En el caso de pago exitoso, queremos enviar al servidor (o usar en la vista)
    // la dirección de envío que se encuentra en el localStorage (clave "tempShippingAddress").
    // Debido a que PHP se ejecuta en el servidor y no puede acceder al localStorage,
    // usamos JavaScript para leer dicho valor y reenviarlo mediante un formulario oculto.
    if ($action == "getResult" && isset($_POST['tbk_status']) && $_POST['tbk_status'] === 'AUTHORIZED'): ?>
        <form id="shipping-form" method="POST" action="guardarDireccion.php">
            <input type="hidden" name="tempShippingAddress" id="tempShippingAddress" value="">
            <!-- Puedes agregar también otros campos (como el ID de pedido, usuario, etc.) -->
            <button type="submit" style="display:none;">Enviar dirección</button>
        </form>
        <script>
            let tempShippingAddress = localStorage.getItem("tempShippingAddress");
            if (tempShippingAddress) {
                document.getElementById("tempShippingAddress").value = tempShippingAddress;
                // Si deseas enviarlo automáticamente, puedes hacerlo:
                // document.getElementById("shipping-form").submit();
                // O bien, esperar a la acción del usuario.
            }
        </script>
    <?php endif; ?>
</body>
</html>
