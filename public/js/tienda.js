document.addEventListener('DOMContentLoaded', function() {
    const contenedorProductos = document.querySelector('#contenedor-productos');
    if (contenedorProductos) {
        const urlParams = new URLSearchParams(window.location.search);
        const categoriaId = urlParams.get('categoria');
        cargarProductos('', 1, categoriaId);
    }
    actualizarBotonCarrito();

    const formularioBusqueda = document.querySelector('#formulario-busqueda');
    if (formularioBusqueda) {
        formularioBusqueda.addEventListener('submit', function(e) {
            e.preventDefault();
            const terminoBusqueda = document.querySelector('#termino-busqueda').value;
            cargarProductos(terminoBusqueda);
        });
    }

    if (contenedorProductos) {
        contenedorProductos.addEventListener('click', function(e) {
            if (e.target.classList.contains('producto-agregar')) {
                const productoId = e.target.dataset.id;
                agregarAlCarrito(productoId);
            }
        });
    }

    const menuToggle = document.querySelector('.menu-toggle');
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    }

    // Añadir event listener para los enlaces de categoría
    document.querySelectorAll('.sidebar a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const categoriaId = this.getAttribute('data-categoria-id');
            cargarProductos('', 1, categoriaId);
            // Actualizar la URL sin recargar la página
            const newUrl = categoriaId ? `${window.baseUrl}/tienda.php?categoria=${categoriaId}` : `${window.baseUrl}/tienda.php`;
            window.history.pushState({}, '', newUrl);
        });
    });
});

function cargarProductos(terminoBusqueda = '', pagina = 1, categoriaId = null) {
    const contenedorProductos = document.querySelector('#contenedor-productos');
    if (!contenedorProductos) return;

    const url = new URL(`${window.baseUrl}/../includes/tienda_functions.php`);
    url.searchParams.append('action', terminoBusqueda ? 'buscar_productos' : 'get_productos');
    if (terminoBusqueda) {
        url.searchParams.append('q', terminoBusqueda);
    }
    url.searchParams.append('pagina', pagina);
    if (categoriaId) {
        url.searchParams.append('categoria_id', categoriaId);
    }

    mostrarCargando();

    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error HTTP! estado: ${response.status}`);
            }
            return response.text(); // Cambiamos esto de response.json() a response.text()
        })
        .then(text => {
            console.log('Respuesta del servidor:', text); // Agregamos este log
            try {
                return JSON.parse(text);
            } catch (error) {
                console.error('Error al parsear JSON:', error);
                throw new Error('Respuesta del servidor no es JSON válido');
            }
        })
        .then(data => {
            if (data.success && Array.isArray(data.productos)) {
                actualizarContenedorProductos(data.productos);
                actualizarTituloPrincipal(data.categoria_nombre);
            } else {
                throw new Error(data.error || 'No se recibieron productos válidos del servidor');
            }
        })
        .catch(error => {
            console.error('Error al cargar los productos:', error);
            contenedorProductos.innerHTML = `<p class="error-mensaje">Error al cargar los productos: ${error.message}. Por favor, intenta de nuevo más tarde.</p>`;
            mostrarNotificacion(`Error al cargar los productos. Por favor, intenta de nuevo.`, 'error');
        })
        .finally(() => {
            ocultarCargando();
        });
}

function actualizarContenedorProductos(productos) {
    const contenedorProductos = document.querySelector('#contenedor-productos');
    contenedorProductos.innerHTML = '';

    if (productos.length > 0) {
        productos.forEach(producto => {
            const productoElement = crearElementoProducto(producto);
            contenedorProductos.appendChild(productoElement);
        });
    } else {
        contenedorProductos.innerHTML = '<p class="no-productos">No se encontraron productos.</p>';
    }
}

function crearElementoProducto(producto) {
    const divProducto = document.createElement('div');
    divProducto.className = 'producto';
    divProducto.innerHTML = `
        <img src="${producto.image_path}" alt="${producto.name}" class="producto-imagen">
        <div class="producto-detalles">
            <h3 class="producto-titulo">${producto.name}</h3>
            <p class="producto-precio">$${producto.price_formatted}</p>
            <a href="${window.baseUrl}/producto.php?id=${producto.id}" class="ver-producto">Ver producto</a>
            <button class="producto-agregar" id="producto-${producto.id}" data-id="${producto.id}">Agregar al carrito</button>
        </div>
    `;
    return divProducto;
}

function agregarAlCarrito(productoId) {
    fetch(`${window.baseUrl}/api/agregar_al_carrito.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ producto_id: productoId })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Error HTTP! estado: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            mostrarNotificacion(data.message, 'success');
            actualizarBotonCarrito(data.total_items);
        } else {
            throw new Error(data.message || 'Error desconocido al agregar el producto al carrito');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarNotificacion(`Error al agregar el producto al carrito: ${error.message}`, 'error');
    });
}

function actualizarBotonCarrito(totalItems) {
    const numerito = document.querySelector('#numerito');
    if (numerito) {
        numerito.textContent = totalItems;
    }
}

function mostrarNotificacion(mensaje, tipo) {
    Toastify({
        text: mensaje,
        duration: 3000,
        gravity: "top",
        position: 'right',
        style: {
            background: tipo === 'error' ? "#e74c3c" : "#2ecc71",
        },
        onClick: function(){} // Callback después del clic
    }).showToast();
}

function mostrarCargando() {
    const cargando = document.createElement('div');
    cargando.id = 'cargando';
    cargando.innerHTML = '<div class="spinner"></div><p>Cargando productos...</p>';
    document.body.appendChild(cargando);
}

function ocultarCargando() {
    const cargando = document.getElementById('cargando');
    if (cargando) {
        cargando.remove();
    }
}

function actualizarTituloPrincipal(categoriaNombre) {
    const tituloPrincipal = document.querySelector('#titulo-principal');
    if (tituloPrincipal) {
        tituloPrincipal.textContent = categoriaNombre || 'Todos los productos';
    }
}

function cambiarPagina(pagina) {
    cargarProductos('', pagina);
}

// Event listeners para la paginación
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.pagination .page-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const pagina = this.getAttribute('data-pagina');
            cambiarPagina(pagina);
        });
    });
});