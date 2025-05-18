<?php
session_start();

if (isset($_SESSION["usuario"])) {
    // Devuelve el saludo como HTML para que se inserte en la página
    echo '<span id="userGreeting">Hola, ' . htmlspecialchars($_SESSION["usuario"]["nombre"]) . '</span>';

    // Modifica la funcionalidad del ícono para redirigir al perfil
    echo '<script>
        document.addEventListener("DOMContentLoaded", function () {
            let userIcon = document.getElementById("userIcon");
            userIcon.style.cursor = "pointer";
            userIcon.addEventListener("click", function () {
                window.location.href = "perfil.php";
            });

            // Oculta el popup de login
            let popup = document.getElementById("userPopup");
            if (popup) {
                popup.style.display = "none";
            }
        });
    </script>';
} else {
    // Si el usuario no está logueado, mantiene la lógica del popup
    echo '<script>
        document.addEventListener("DOMContentLoaded", function () {
            let userIcon = document.getElementById("userIcon");
            userIcon.style.cursor = "pointer";
            userIcon.addEventListener("click", function () {
                let popup = document.getElementById("userPopup");
                popup.style.display = popup.style.display === "block" ? "none" : "block";
            });
        });
    </script>';
}
?>
