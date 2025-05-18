<?php
session_start();
session_unset(); // Elimina todas las variables de sesión
session_destroy(); // Borra la sesión completamente

header("Location: index.php"); // Redirige al inicio
exit();
?>
