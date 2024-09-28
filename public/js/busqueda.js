document.addEventListener('DOMContentLoaded', function () {
    const contenedorProductos = document.querySelector("#contenedor-productos");
    const numerito = document.querySelector("#numerito");

    let productosEnCarrito = JSON.parse(localStorage.getItem("productos-en-carrito")) || [];

    function mostrarProductos(productos) {
        contenedorProductos.innerHTML = "";

        if (productos.length === 0) {
            contenedorProductos.innerHTML = "<p class='no-resultados'>No se encontraron productos que coincidan con tu búsqueda.</p>";
            return;
        }

        productos.forEach(producto => {
            const div = document.createElement("div");
            div.classList.add("producto");
            
            const imagePath = producto.image_path || `${baseUrl}assets/img/producto-sin-imagen.png`;

            div.innerHTML = `
                <img class="producto-imagen" src="${imagePath}" alt="${producto.name}" onerror="this.src='${baseUrl}assets/img/producto-sin-imagen.png';">
                <div class="producto-detalles">
                    <h3 class="producto-titulo">${producto.name}</h3>
                    <p class="producto-precio">${formatearPrecio(producto.price)}</p>
                    <p class="producto-id">ID: ${producto.id}</p>
                    <p class="producto-categoria">Categoría: ${producto.category_name}</p>
                    <button class="producto-agregar" data-id="${producto.id}">Agregar al carrito</button>
                </div>
            `;

            contenedorProductos.appendChild(div);
        });

        actualizarBotonesAgregar();
    }

    function actualizarBotonesAgregar() {
        const botonesAgregar = document.querySelectorAll(".producto-agregar");
        botonesAgregar.forEach(boton => {
            boton.addEventListener("click", agregarAlCarrito);
        });
    }

    function agregarAlCarrito(e) {
        const idBoton = e.currentTarget.dataset.id;
        const productoAgregado = productosEncontrados.find(producto => producto.id.toString() === idBoton);

        if (productoAgregado) {
            const index = productosEnCarrito.findIndex(producto => producto.id.toString() === idBoton);

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

    function formatearPrecio(precio) {
        return '$ ' + parseFloat(precio).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }

    // Inicialización
    mostrarProductos(productosEncontrados);
    actualizarNumerito();
});