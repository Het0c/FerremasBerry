<?php
session_start();
require_once 'conexion.php';  // Ahora $conexion está definido

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = $_POST["email"];
    $password = $_POST["password"];

    $query = "SELECT idUsuario, nombre, contrasena FROM usuario WHERE email = ?";
    $stmt  = $conexion->prepare($query);

    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conexion->error);
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        //en caso de contraseña hasheada
        //if (password_verify($password, $usuario["contrasena"])) {
        if ($password == $usuario["contrasena"]) {
            $_SESSION["usuario"] = ["id" => $usuario["idUsuario"], "nombre" => $usuario["nombre"]];
            header("Location: index.php");
            exit();
        } else {
            echo "Contraseña incorrecta.";
        }
    } else {
        echo "Usuario no encontrado.";
    }
    
    $stmt->close();
}
?>
