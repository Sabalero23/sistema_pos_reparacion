document.addEventListener("DOMContentLoaded", function() {
    const botonAgregar = document.querySelector("#agregar-al-carrito");
    if (botonAgregar) {
        botonAgregar.addEventListener("click", agregarAlCarrito);
    }

    const botonCopiar = document.querySelector("#copiar-enlace");
    if (botonCopiar) {
        botonCopiar.addEventListener("click", copiarEnlace);
    }

    actualizarNumerito();
});

function agregarAlCarrito(e) {
    const idProducto = e.currentTarget.dataset.id;
    
    // Actualizar almacenamiento local
    let productosEnCarrito = JSON.parse(localStorage.getItem("productos-en-carrito")) || [];
    
    const productoExistente = productosEnCarrito.find(producto => producto.id === idProducto);
    if (productoExistente) {
        productoExistente.cantidad++;
    } else {
        productosEnCarrito.push({ id: idProducto, cantidad: 1 });
    }

    localStorage.setItem("productos-en-carrito", JSON.stringify(productosEnCarrito));

    // Actualizar en el servidor
    fetch(`${baseUrl}/api/agregar_al_carrito.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ producto_id: idProducto })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Toastify({
                text: "Producto agregado al carrito",
                duration: 3000,
                close: true,
                gravity: "bottom",
                position: "right",
                stopOnFocus: true,
                style: {
                    background: "linear-gradient(to right, #00b09b, #96c93d)",
                },
            }).showToast();
            actualizarNumerito();
        } else {
            throw new Error(data.error || 'Error al agregar el producto al carrito');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Toastify({
            text: "Error al agregar el producto al carrito",
            duration: 3000,
            close: true,
            gravity: "bottom",
            position: "right",
            style: {
                background: "linear-gradient(to right, #ff5f6d, #ffc371)",
            }
        }).showToast();
    });
}

function actualizarNumerito() {
    let numerito = document.querySelector("#numerito");
    if (numerito) {
        // Primero, obtener el conteo del almacenamiento local
        let productosEnCarrito = JSON.parse(localStorage.getItem("productos-en-carrito")) || [];
        let conteoLocal = productosEnCarrito.reduce((total, producto) => total + producto.cantidad, 0);

        // Luego, intentar obtener el conteo del servidor
        fetch(`${baseUrl}/api/obtener_carrito.php`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Si el servidor responde exitosamente, usar ese conteo
                    numerito.textContent = data.total_items;
                } else {
                    // Si hay un error, usar el conteo local
                    numerito.textContent = conteoLocal;
                }
            })
            .catch(error => {
                console.error('Error al actualizar numerito desde el servidor:', error);
                // En caso de error, usar el conteo local
                numerito.textContent = conteoLocal;
            });
    }
}

function copiarEnlace(e) {
    const url = e.currentTarget.dataset.url;
    navigator.clipboard.writeText(url).then(() => {
        Toastify({
            text: "Enlace copiado al portapapeles",
            duration: 3000,
            close: true,
            gravity: "bottom",
            position: "right",
            stopOnFocus: true,
            style: {
                background: "linear-gradient(to right, #00b09b, #96c93d)",
            },
        }).showToast();
    }).catch(err => {
        console.error('Error al copiar el enlace: ', err);
        Toastify({
            text: "Error al copiar el enlace",
            duration: 3000,
            close: true,
            gravity: "bottom",
            position: "right",
            style: {
                background: "linear-gradient(to right, #ff5f6d, #ffc371)",
            }
        }).showToast();
    });
}