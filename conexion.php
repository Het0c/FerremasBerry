<?php
// Conexion tiene que estar separada del resto de funciones
$host     = "localhost";
$user     = "root";
$pass     = "";
$db       = "FERREMAS";

$conexion = mysqli_connect($host, $user, $pass, $db);

if (!$conexion) {
    die("<b><h3>No se ha podido conectar con el servidor: " . mysqli_connect_error() . "</h3></b>");
}
?>
