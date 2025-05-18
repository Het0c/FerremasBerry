document.addEventListener("DOMContentLoaded", function () {
    fetch("session_status.php")
        .then(response => response.text())
        .then(data => {
            let userGreeting = document.getElementById("userGreeting");
            let userPopup = document.getElementById("userPopup");

            userGreeting.innerHTML = data;

            // Si el usuario está logueado, desactivar el popup
            if (data.includes("Hola,")) {
                userPopup.style.display = "none"; // Oculta el popup si hay sesión
                document.getElementById("userIcon").addEventListener("click", function () {
                    window.location.href = "templates/perfil.php"; // Redirige al perfil
                });
            } else {
                // Si no hay sesión, mantener el funcionamiento original del popup
                document.getElementById("userIcon").addEventListener("click", function () {
                    userPopup.style.display = userPopup.style.display === "block" ? "none" : "block";
                });
            }
        });
});

    // Opcional: cerrar el popup si se hace clic fuera de él
    document.addEventListener('click', function (e) {
      var popup = document.getElementById('userPopup');
      var icon    = document.getElementById('userIcon');
      // Si se hace clic fuera del ícono y del popup, se cierra
      if (!icon.contains(e.target) && !popup.contains(e.target)) {
        popup.style.display = 'none';
      }
    });