<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

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
            "buy_order" => "$buy_order",
            "session_id" => "$session_id",
            "amount" => $amount,
            "return_url" => $return_url
        ]);

        $method = 'POST';
        $endpoint = '/rswebpaytransaction/api/webpay/v1.0/transactions';
        $response = get_ws($data, $method, $type, $endpoint);

        $message .= "<pre>" . print_r($response, true) . "</pre>";

       
        if (isset($response->url) && isset($response->token)) {
            $url_tbk = $response->url;
            $token = $response->token;
            $submit = 'Pagar ahora';
        } else {
            $message .= "<p style='color:red;'>Error iniciando la transacción.</p>";
        }
        break;

    case "getResult":
        
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
        break;
       
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Redirigiendo a Webpay</title>
</head>
<body>
    <?= $message ?>
 <?php if (isset($boton_volver)) echo $boton_volver; ?>

    <?php if (!empty($url_tbk) && !empty($token)): ?>
        <form id="webpay-form" method="POST" action="<?= $url_tbk ?>">
            <input type="hidden" name="token_ws" value="<?= $token ?>">
            <button type="submit"><?= $submit ?></button>
        </form>
        <script>
            document.getElementById('webpay-form').submit();
        </script>
    <?php endif; ?>
</body>
</html>
