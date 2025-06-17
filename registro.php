<?php
session_start();
require_once "conexion.php"; // Asegúrate de que este archivo define la variable $conexion

// Se procesa el formulario solo si se usa el método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recoger y validar los datos del formulario
    $nombre    = isset($_POST['nombre'])   ? trim($_POST['nombre'])   : "";
    $apellido  = isset($_POST['apellido']) ? trim($_POST['apellido']) : "";
    $email     = isset($_POST['email'])    ? trim($_POST['email'])    : "";
    $telefono  = isset($_POST['telefono']) ? trim($_POST['telefono']) : "";
    $password  = isset($_POST['password']) ? $_POST['password']       : "";

    // Comprobar que ningún campo obligatorio esté vacío
    if (empty($nombre) || empty($apellido) || empty($email) || empty($telefono) || empty($password)) {
        die("Por favor, complete todos los campos.");
    }

    // Forzar que el tipo de usuario sea 1 (cliente)
    $tipoUsuario = 1;
    
    // Encriptar la contraseña
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Preparar la sentencia SQL para insertar el nuevo usuario
    $sql = "INSERT INTO usuario (nombre, apellido, email, telefono, contrasena, tipoUsuario) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conexion->prepare($sql);
    
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conexion->error);
    }

    // "sssssi" indica que los primeros 5 parámetros son cadenas y el último es entero
    $stmt->bind_param("sssssi", $nombre, $apellido, $email, $telefono, $hashedPassword, $tipoUsuario);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Redirigir al index.php si la inserción fue exitosa
        header("Location: index.php");
        exit();
    } else {
        echo "Error al registrar el usuario: " . $stmt->error;
    }

    // Cerrar la sentencia y la conexión
    $stmt->close();
    $conexion->close();
} else {
    echo "Acceso no autorizado.";
}
?>
