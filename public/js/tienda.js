document.addEventListener('DOMContentLoaded', function () {
    const contenedorProductos = document.querySelector("#contenedor-productos");
    const botonesCategorias = document.querySelectorAll(".sidebar a");
    const tituloPrincipal = document.querySelector("#titulo-principal");
    const paginacionContainer = document.querySelector("#paginacion");
    let botonesAgregar = document.querySelectorAll(".producto-agregar");
    const numerito = document.querySelector("#numerito");

    let productosEnCarrito = JSON.parse(localStorage.getItem("productos-en-carrito")) || [];

    function cargarProductos(url) {
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (!Array.isArray(data.productos)) {
                    throw new Error('Los datos de productos no son un array válido');
                }
                mostrarProductos(data.productos);
                actualizarPaginacion(data.pagina_actual, data.total_paginas);
            })
            .catch(error => {
                console.error("Error al cargar los productos:", error);
                if (contenedorProductos) {
                    contenedorProductos.innerHTML = `<p>Error al cargar los productos: ${error.message}</p>`;
                }
            });
    }

    function mostrarProductos(productos) {
        if (!contenedorProductos) return;

        contenedorProductos.innerHTML = "";

        productos.forEach(producto => {
            const div = document.createElement("div");
            div.classList.add("producto");
            
            const imagePath = producto.image_path;

            div.innerHTML = `
                <img class="producto-imagen" src="${imagePath}" alt="${producto.name}" onerror="this.src='${baseUrl}/assets/img/producto-sin-imagen.png';">
                <div class="producto-detalles">
                    <h3 class="producto-titulo">${producto.name}</h3>
                    <p class="producto-precio">${producto.price}</p>
                    <button class="producto-agregar" data-id="${producto.id}">Agregar al carrito</button>
                </div>
            `;

            contenedorProductos.append(div);
        });

        actualizarBotonesAgregar();
    }

    function actualizarPaginacion(paginaActual, totalPaginas) {
        if (!paginacionContainer) return;

        let html = '<nav aria-label="Paginación de productos"><ul class="pagination">';

        // Botón "Anterior"
        if (paginaActual > 1) {
            html += `<li class="page-item"><a class="page-link" href="#" data-pagina="${paginaActual - 1}">Anterior</a></li>`;
        } else {
            html += '<li class="page-item disabled"><span class="page-link">Anterior</span></li>';
        }

        // Páginas
        for (let i = 1; i <= totalPaginas; i++) {
            if (i === paginaActual) {
                html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
            } else {
                html += `<li class="page-item"><a class="page-link" href="#" data-pagina="${i}">${i}</a></li>`;
            }
        }

        // Botón "Siguiente"
        if (paginaActual < totalPaginas) {
            html += `<li class="page-item"><a class="page-link" href="#" data-pagina="${paginaActual + 1}">Siguiente</a></li>`;
        } else {
            html += '<li class="page-item disabled"><span class="page-link">Siguiente</span></li>';
        }

        html += '</ul></nav>';

        paginacionContainer.innerHTML = html;

        // Agregar event listeners a los enlaces de paginación
        const enlacesPaginacion = paginacionContainer.querySelectorAll('.page-link');
        enlacesPaginacion.forEach(enlace => {
            enlace.addEventListener('click', function(e) {
                e.preventDefault();
                const nuevaPagina = this.dataset.pagina;
                if (nuevaPagina) {
                    cargarProductos(`${baseUrl}/api/productos.php?categoria=${categoriaActual}&pagina=${nuevaPagina}`);
                }
            });
        });
    }

    if (botonesCategorias) {
        botonesCategorias.forEach(boton => {
            boton.addEventListener("click", (e) => {
                e.preventDefault();
                botonesCategorias.forEach(boton => boton.classList.remove("active"));
                e.currentTarget.classList.add("active");

                categoriaActual = e.currentTarget.dataset.categoriaId;
                
                if (categoriaActual && tituloPrincipal) {
                    tituloPrincipal.innerText = e.currentTarget.innerText;
                    cargarProductos(`${baseUrl}/api/productos.php?categoria=${categoriaActual}&pagina=1`);
                } else if (tituloPrincipal) {
                    tituloPrincipal.innerText = "Todos los productos";
                    cargarProductos(`${baseUrl}/api/productos.php?pagina=1`);
                }
            });
        });
    }

    function actualizarBotonesAgregar() {
        botonesAgregar = document.querySelectorAll(".producto-agregar");

        botonesAgregar.forEach(boton => {
            boton.addEventListener("click", agregarAlCarrito);
        });
    }

    function agregarAlCarrito(e) {
        const idBoton = e.currentTarget.dataset.id;
        const productoAgregado = { id: idBoton, cantidad: 1 };

        const index = productosEnCarrito.findIndex(producto => producto.id === idBoton);

        if (index !== -1) {
            productosEnCarrito[index].cantidad++;
        } else {
            productosEnCarrito.push(productoAgregado);
        }

        actualizarNumerito();
        localStorage.setItem("productos-en-carrito", JSON.stringify(productosEnCarrito));

        Toastify({
            text: "Producto agregado al carrito",
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            style: {
                background: "linear-gradient(to right, #00b09b, #96c93d)",
            },
            stopOnFocus: true
        }).showToast();
    }

    function actualizarNumerito() {
        let nuevoNumerito = productosEnCarrito.reduce((acc, producto) => acc + producto.cantidad, 0);
        if (numerito) {
            numerito.innerText = nuevoNumerito;
        }
    }

    // Cargar productos iniciales
    cargarProductos(`${baseUrl}/api/productos.php?categoria=${categoriaActual}&pagina=${paginaActual}`);
    actualizarNumerito();
});