<?php
$user = "root";
$pass = "";
$host = "localhost";
$db = "FERREMAS";

$connection = mysqli_connect($host, $user, $pass, $db);

if (!$connection) {
    echo "<b><h3>No se ha podido conectar con el servidor</h3></b>";
    exit;
} else {
    echo "<b><h3>Hemos conectado al servidor</h3></b>";
}

// Verificar si se han recibido datos del formulario para insertar
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir datos del formulario
    $nombre = $_POST["nombre"] ?? '';
    $apellido = $_POST["apellido"] ?? '';
    $correo = $_POST["correo"] ?? '';
    $telefono = $_POST["telefono"] ?? '';
    $mensaje = $_POST["mensaje"] ?? '';

    // Inserción en la base de datos (6 columnas y 6 valores)
    $instruccion_SQL = "INSERT INTO ingreso (nombre, apellido, correo, telefono, mensaje) 
                        VALUES ('$nombre','$apellido','$correo','$telefono','$mensaje')";
    
    $resultado = mysqli_query($connection, $instruccion_SQL);
    
    if ($resultado) {
        echo "<p>Datos insertados correctamente.</p>";
      
    } else {
        echo "<p>Error al insertar los datos: " . mysqli_error($connection) . "</p>";
    }
}

mysqli_close($connection);
?>

<a href="index.html">Volver Atrás</a>


