// script.js

window.addEventListener('beforeunload', function(event) {
    // Verificar si la página se está descargando y no hay errores
    if (sessionStorage.getItem('page_status') !== 'error') {
        // Enviar una solicitud al servidor para eliminar el correo electrónico de la sesión
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'borrar_email.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send('action=borrar_email_on_exit');
    }
});

// Función para redirigir a la página de inicio de sesión y cerrar sesiones
function redirectToLoginAndCloseSessions() {
    // Redirigir a la página de inicio de sesión
    window.location.href = "../login/login.php";
}

// Verificar si hay un parámetro de correo electrónico
window.addEventListener('DOMContentLoaded', function() {
    var email = getEmailFromSession(); // Obtener el correo electrónico de la sesión PHP
    if (email === '') {
        // Mostrar el mensaje de alerta y redirigir después de unos segundos
        alert("Tu verificación ha caducado. Por favor, inicia sesión para intentarlo de nuevo. Presiona OK para redirigirte a la página de inicio de sesión.");
        setTimeout(redirectToLoginAndCloseSessions, 2000); // Redirigir después de 3 segundos
    }
});

// Función para obtener el correo electrónico de la sesión PHP
function getEmailFromSession() {
    // Definir una variable global de JavaScript y asignar el valor de PHP
    var email = "<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>";
    return email;
}
