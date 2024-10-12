document.addEventListener('DOMContentLoaded', function () {
    const contenedorCarrito = document.querySelector("#contenedor-carrito");
    const totalCarrito = document.querySelector("#total-carrito");
    const vaciarCarritoBtn = document.querySelector("#vaciar-carrito");
    const comprarCarritoBtn = document.querySelector("#comprar-carrito");
    const contadorCarrito = document.querySelector("#contador-carrito");
    const modal = document.querySelector("#modal-cliente");
    const closeModal = document.querySelector(".close");
    const formCliente = document.querySelector("#form-cliente");

    let productosEnCarrito = [];

function cargarProductosCarrito() {
    console.log('Iniciando carga de productos del carrito...');
    fetch(`${window.baseUrl}/api/obtener_carrito.php`)
        .then(response => {
            console.log('Respuesta recibida:', response);
            return response.text(); // Cambiamos a .text() para ver el contenido real
        })
        .then(text => {
            console.log('Texto de la respuesta:', text);
            try {
                return JSON.parse(text);
            } catch (error) {
                console.error('Error al parsear JSON:', error);
                throw new Error(`Respuesta no válida del servidor: ${text}`);
            }
        })
        .then(data => {
            console.log('Datos del carrito:', data);
            if (data.success) {
                productosEnCarrito = data.productos;
                mostrarProductosCarrito();
                actualizarContadorCarrito(data.total_items);
            } else {
                throw new Error(data.error || 'Error desconocido al cargar el carrito');
            }
        })
        .catch(error => {
            console.error('Error al cargar el carrito:', error);
            mostrarError(`No se pudo cargar el carrito. Error: ${error.message}`);
        });
}

    function mostrarProductosCarrito() {
        if (!contenedorCarrito) return;

        contenedorCarrito.innerHTML = "";

        if (productosEnCarrito.length === 0) {
            contenedorCarrito.innerHTML = "<p class='carrito-vacio'>El carrito está vacío</p>";
            actualizarTotal(0);
            return;
        }

        let totalCarritoValor = 0;

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
                    <p class="subtotal-producto">Subtotal: $${producto.subtotal.toFixed(2)}</p>
                </div>
                <button class="btn-eliminar" data-id="${producto.id}">Eliminar</button>
            `;
            contenedorCarrito.appendChild(div);
            totalCarritoValor += producto.subtotal;
        });

        actualizarTotal(totalCarritoValor);
    }

    function actualizarTotal(total) {
        if (totalCarrito) {
            totalCarrito.textContent = `Total: $${total.toFixed(2)}`;
        }
    }

    function actualizarContadorCarrito(totalItems) {
        if (contadorCarrito) {
            contadorCarrito.textContent = totalItems;
        }
    }

    function eliminarProducto(id) {
        console.log(`Eliminando producto: ${id}`);
        fetch(`${window.baseUrl}/api/eliminar_del_carrito.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ producto_id: id })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Respuesta de eliminación:', data);
            if (data.success) {
                cargarProductosCarrito(); // Recargar el carrito completo
            } else {
                throw new Error(data.error || 'Error al eliminar el producto');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarError('No se pudo eliminar el producto. Por favor, intenta de nuevo.');
        });
    }

function actualizarCantidad(id, action) {
    console.log(`Actualizando cantidad: producto ${id}, acción ${action}`);
    fetch(`${window.baseUrl}/api/actualizar_cantidad_carrito.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ producto_id: id, action: action })
    })
    .then(response => {
        console.log('Respuesta recibida:', response);
        return response.text();
    })
    .then(text => {
        console.log('Texto de la respuesta:', text);
        try {
            return JSON.parse(text);
        } catch (error) {
            console.error('Error al parsear JSON:', error);
            throw new Error(`Respuesta no válida del servidor: ${text}`);
        }
    })
    .then(data => {
        console.log('Datos de actualización:', data);
        if (data.success) {
            cargarProductosCarrito(); // Recargar todo el carrito
        } else {
            throw new Error(data.error || 'Error al actualizar la cantidad');
        }
    })
    .catch(error => {
        console.error('Error al actualizar cantidad:', error);
        mostrarError('No se pudo actualizar la cantidad. Por favor, intenta de nuevo.');
    });
}

    function vaciarCarrito() {
        fetch(`${window.baseUrl}/api/vaciar_carrito.php`, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                productosEnCarrito = [];
                mostrarProductosCarrito();
                actualizarContadorCarrito(0);
            } else {
                throw new Error(data.error || 'Error al vaciar el carrito');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarError('No se pudo vaciar el carrito. Por favor, intenta de nuevo.');
        });
    }

    function abrirModal() {
        if (productosEnCarrito.length === 0) {
            mostrarError("El carrito está vacío. Agrega productos antes de comprar.");
            return;
        }
        if (modal) {
            modal.style.display = "block";
        }
    }

    function cerrarModal() {
        if (modal) {
            modal.style.display = "none";
        }
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
            mensaje += `${producto.name} x${producto.cantidad} - $${producto.subtotal.toFixed(2)}%0A`;
        });

        const total = productosEnCarrito.reduce((acc, producto) => acc + producto.subtotal, 0);
        mensaje += `%0ATotal: $${total.toFixed(2)}`;

        const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${mensaje}`;
        window.open(whatsappUrl, '_blank');

        cerrarModal();
        vaciarCarrito();
    }

    function mostrarError(mensaje) {
        console.error(mensaje);
        if (typeof Toastify !== 'undefined') {
            Toastify({
                text: mensaje,
                duration: 3000,
                gravity: "top",
                position: 'right',
                backgroundColor: "#e74c3c",
                stopOnFocus: true
            }).showToast();
        } else {
            alert(mensaje);
        }
    }

    // Event Listeners
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

    // Cargar productos del carrito al iniciar
    cargarProductosCarrito();
});