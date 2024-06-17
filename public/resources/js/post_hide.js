document.addEventListener("DOMContentLoaded", function() {
    var links = document.querySelectorAll(".submenu .option");
    var sections = document.querySelectorAll(".posting");

    links.forEach(function(link) {
        link.addEventListener("click", function(event) {
            event.preventDefault(); // Evita el comportamiento predeterminado del enlace
            var targetId = this.getAttribute("href").substring(1);
            mostrarSeccion(targetId);
            // Removemos la clase active de todos los enlaces y luego la añadimos al enlace clickeado
            links.forEach(function(l) {
                l.classList.remove("active");
            });
            this.classList.add("active");
        });
    });

    function mostrarSeccion(id) {
        sections.forEach(function(posting) {
            if (posting.id === id) {
                posting.classList.remove("hidden");
            } else {
                posting.classList.add("hidden");
            }
        });
    }

    var post = [];

    sections.forEach(function(posting) {
        // Obtenemos los contenedores utilizando clases
        var postdiv = posting.querySelector(".post-container");
        var noPostdiv = posting.querySelector(".nopost-container");

        // Verificamos si hay publicaciones
        if (post.length === 0) {
            // Si no hay publicaciones, mostramos el mensaje
            noPostdiv.classList.remove("hidden");
        } else {
            // Post las mostramos
            post.forEach(function(post) {
                var divPost = document.createElement("div");
                divPost.textContent = divPost;
                postdiv.appendChild(divPost);
            });
            // Mostramos el contenedor de publicaciones
            postdiv.classList.remove("hidden");
        }
    });
});
