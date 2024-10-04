let productosEncontrados = [];
let categoriaActual = '';
let tabla;

document.addEventListener('DOMContentLoaded', function () {
    const botonesCategorias = document.querySelectorAll(".sidebar a");
    const tituloPrincipal = document.querySelector("#titulo-principal");
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
                productosEncontrados = data.productos;
                inicializarTabla(data.productos);
            })
            .catch(error => {
                console.error("Error al cargar los productos:", error);
                alert(`Error al cargar los productos: ${error.message}`);
            });
    }

    function inicializarTabla(productos) {
        if (tabla) {
            tabla.destroy();
        }

        tabla = $('#tabla-productos').DataTable({
            data: productos,
            columns: [
                { 
                    data: 'image_path', 
                    render: function(data, type, row) {
                        return `<img src="${data}" alt="${row.name}" class="producto-imagen" onerror="this.src='${baseUrl}/assets/img/producto-sin-imagen.png';">`;
                    }
                },
                { data: 'name' },
                { data: 'price_formatted' },
                { 
                    data: null,
                    render: function(data, type, row) {
                        return `<button class="producto-agregar" data-id="${row.id}">Agregar al carrito</button>`;
                    }
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
            }
        });

        $('#tabla-productos').on('click', '.producto-agregar', agregarAlCarrito);
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
                    cargarProductos(`${baseUrl}/api/productos.php?categoria=${categoriaActual}`);
                } else if (tituloPrincipal) {
                    tituloPrincipal.innerText = "Todos los productos";
                    cargarProductos(`${baseUrl}/api/productos.php`);
                }
            });
        });
    }

    function agregarAlCarrito(e) {
        const idBoton = $(this).data('id');
        const productoAgregado = productosEncontrados.find(producto => producto.id.toString() === idBoton.toString());

        if (productoAgregado) {
            const index = productosEnCarrito.findIndex(producto => producto.id.toString() === idBoton.toString());

            if (index !== -1) {
                productosEnCarrito[index].cantidad++;
            } else {
                productoAgregado.cantidad = 1;
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
    }

    function actualizarNumerito() {
        let nuevoNumerito = productosEnCarrito.reduce((acc, producto) => acc + (producto.cantidad || 0), 0);
        numerito.innerText = nuevoNumerito;
    }

    // Inicialización: cargar todos los productos al inicio
    cargarProductos(`${baseUrl}/api/productos.php`);
    actualizarNumerito();

    // Toggle para el menú en dispositivos móviles
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');

    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });

        document.addEventListener('click', function(event) {
            if (!sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
                sidebar.classList.remove('active');
            }
        });
    }
});