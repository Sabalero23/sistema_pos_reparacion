document.addEventListener('DOMContentLoaded', function () {
    const contenedorCarrito = document.querySelector("#contenedor-carrito");
    const totalCarrito = document.querySelector("#total-carrito");
    const vaciarCarritoBtn = document.querySelector("#vaciar-carrito");
    const comprarCarritoBtn = document.querySelector("#comprar-carrito");
    const contadorCarrito = document.querySelector("#contador-carrito");
    const modal = document.querySelector("#modal-cliente");
    const closeModal = document.querySelector(".close");
    const formCliente = document.querySelector("#form-cliente");

    let productosEnCarrito = JSON.parse(localStorage.getItem("productos-en-carrito")) || [];

    function mostrarProductosCarrito() {
        if (!contenedorCarrito) return;

        contenedorCarrito.innerHTML = "";

        if (productosEnCarrito.length === 0) {
            contenedorCarrito.innerHTML = "<p class='carrito-vacio'>El carrito está vacío</p>";
            return;
        }

        productosEnCarrito.forEach(producto => {
            const div = document.createElement("div");
            div.classList.add("producto-carrito");
            div.innerHTML = `
                <img src="${producto.image_path}" alt="${producto.name}" class="imagen-producto-carrito">
                <div class="detalles-producto">
                    <h3>${producto.name}</h3>
                    <p class="precio-producto">Precio: $${parseFloat(producto.price).toFixed(2)}</p>
                    <div class="cantidad-producto">
                        <button class="btn-cantidad" data-id="${producto.id}" data-action="decrease">-</button>
                        <span>${producto.cantidad}</span>
                        <button class="btn-cantidad" data-id="${producto.id}" data-action="increase">+</button>
                    </div>
                    <p class="subtotal-producto">Subtotal: $${(parseFloat(producto.price) * producto.cantidad).toFixed(2)}</p>
                </div>
                <button class="btn-eliminar" data-id="${producto.id}">Eliminar</button>
            `;
            contenedorCarrito.appendChild(div);
        });

        actualizarTotal();
    }

    function actualizarTotal() {
        if (!totalCarrito) return;

        const total = productosEnCarrito.reduce((acc, producto) => acc + (parseFloat(producto.price) * producto.cantidad), 0);
        totalCarrito.textContent = `Total: $${total.toFixed(2)}`;
    }

    function eliminarProducto(id) {
        productosEnCarrito = productosEnCarrito.filter(producto => producto.id !== parseInt(id));
        localStorage.setItem("productos-en-carrito", JSON.stringify(productosEnCarrito));
        mostrarProductosCarrito();
        actualizarContadorCarrito();
    }

    function vaciarCarrito() {
        productosEnCarrito = [];
        localStorage.setItem("productos-en-carrito", JSON.stringify(productosEnCarrito));
        mostrarProductosCarrito();
        actualizarContadorCarrito();
    }

    function actualizarContadorCarrito() {
        if (!contadorCarrito) return;

        const cantidad = productosEnCarrito.reduce((acc, producto) => acc + producto.cantidad, 0);
        contadorCarrito.textContent = cantidad;
    }

    function actualizarCantidad(id, action) {
        const index = productosEnCarrito.findIndex(producto => producto.id === parseInt(id));
        if (index !== -1) {
            if (action === 'increase') {
                productosEnCarrito[index].cantidad++;
            } else if (action === 'decrease' && productosEnCarrito[index].cantidad > 1) {
                productosEnCarrito[index].cantidad--;
            }
            localStorage.setItem("productos-en-carrito", JSON.stringify(productosEnCarrito));
            mostrarProductosCarrito();
            actualizarContadorCarrito();
        }
    }

    if (contenedorCarrito) {
        contenedorCarrito.addEventListener('click', (e) => {
            if (e.target.classList.contains('btn-eliminar')) {
                eliminarProducto(e.target.dataset.id);
            } else if (e.target.classList.contains('btn-cantidad')) {
                actualizarCantidad(e.target.dataset.id, e.target.dataset.action);
            }
        });
    }

    if (vaciarCarritoBtn) {
        vaciarCarritoBtn.addEventListener('click', vaciarCarrito);
    }

    if (comprarCarritoBtn) {
        comprarCarritoBtn.addEventListener('click', abrirModal);
    }

    if (closeModal) {
        closeModal.addEventListener('click', cerrarModal);
    }

    if (formCliente) {
        formCliente.addEventListener('submit', enviarPedido);
    }

    function abrirModal() {
        if (productosEnCarrito.length === 0) {
            alert("El carrito está vacío. Agrega productos antes de comprar.");
            return;
        }
        modal.style.display = "block";
    }

    function cerrarModal() {
        modal.style.display = "none";
    }

    function enviarPedido(e) {
        e.preventDefault();
        const nombre = document.querySelector("#nombre").value;
        const telefono = document.querySelector("#telefono").value;
        const email = document.querySelector("#email").value;
        const direccion = document.querySelector("#direccion").value;

        let mensaje = `Nuevo pedido de ${nombre}%0A%0A`;
        mensaje += `Teléfono: ${telefono}%0A`;
        mensaje += `Email: ${email}%0A`;
        mensaje += `Dirección: ${direccion}%0A%0A`;
        mensaje += `Productos:%0A`;

        productosEnCarrito.forEach(producto => {
            mensaje += `${producto.name} x${producto.cantidad} - $${(parseFloat(producto.price) * producto.cantidad).toFixed(2)}%0A`;
        });

        const total = productosEnCarrito.reduce((acc, producto) => acc + (parseFloat(producto.price) * producto.cantidad), 0);
        mensaje += `%0ATotal: $${total.toFixed(2)}`;

        const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${mensaje}`;
        window.open(whatsappUrl, '_blank');

        cerrarModal();
        vaciarCarrito();
    }

    mostrarProductosCarrito();
    actualizarContadorCarrito();
});