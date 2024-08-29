document.addEventListener('DOMContentLoaded', function() {
    // Inicializar DataTables
    initDataTable();

    // Manejar la confirmación de reservas
    initConfirmReservationButtons();

    // Inicializar Select2 para los dropdowns
    initSelect2();

    // Inicializar el formulario de creación/edición de reservas si existe
    initReservationForm();
});

function initDataTable() {
    if ($.fn.DataTable) {
        $('#reservationsTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
            },
            "responsive": true,
            "order": [[1, "desc"]] // Ordenar por fecha de reserva (asumiendo que es la segunda columna)
        });
    }
}

function initConfirmReservationButtons() {
    const confirmButtons = document.querySelectorAll('.confirm-reservation');
    confirmButtons.forEach(button => {
        button.addEventListener('click', function() {
            const reservationId = this.getAttribute('data-id');
            showConfirmationDialog(reservationId);
        });
    });
}

function showConfirmationDialog(reservationId) {
    Swal.fire({
        title: '¿Confirmar esta reserva?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, confirmar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            confirmReservation(reservationId);
        }
    });
}

function confirmReservation(reservationId) {
    Swal.fire({
        title: 'Procesando...',
        text: 'Por favor espere mientras se confirma la reserva.',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });

    fetch(`${baseUrl}/reservations.php?action=confirm&id=${reservationId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            throw new TypeError("Oops, we haven't got JSON!");
        }
        return response.text();  // Get the response as text first
    })
    .then(text => {
        try {
            return JSON.parse(text);  // Try to parse it as JSON
        } catch (e) {
            console.error('Response was not valid JSON:', text);
            throw new Error('La respuesta del servidor no es JSON válido');
        }
    })
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Confirmada!',
                text: data.message,
                confirmButtonText: 'OK'
            }).then(() => {
                location.reload();
            });
        } else {
            throw new Error(data.message || 'Error desconocido al confirmar la reserva');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un problema al confirmar la reserva: ' + error.message
        });
    });
}

function initSelect2() {
    if ($.fn.select2) {
        $('.form-select').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    }
}

function initReservationForm() {
    const form = document.getElementById('reservationForm');
    if (form) {
        form.addEventListener('submit', function(event) {
            if (!validateReservationForm()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });

        // Inicializar los campos de productos
        initProductFields();
    }
}

function validateReservationForm() {
    let isValid = true;
    // Aquí puedes añadir validaciones específicas para tu formulario de reservas
    // Por ejemplo, verificar que se haya seleccionado al menos un producto, etc.
    return isValid;
}

function initProductFields() {
    const addProductButton = document.getElementById('addProductBtn');
    const productList = document.getElementById('productList');

    if (addProductButton && productList) {
        addProductButton.addEventListener('click', function() {
            addProductField();
        });

        productList.addEventListener('change', function(event) {
            if (event.target.classList.contains('product-select') || event.target.classList.contains('quantity')) {
                updateTotals();
            }
        });

        // Inicializar los campos de productos existentes
        updateTotals();
    }
}

function addProductField() {
    const productTemplate = document.getElementById('productTemplate');
    if (productTemplate) {
        const newProduct = productTemplate.content.cloneNode(true);
        document.getElementById('productList').appendChild(newProduct);
        initSelect2(); // Reinicializar Select2 para el nuevo campo
        updateTotals();
    }
}

function updateTotals() {
    let total = 0;
    document.querySelectorAll('.product-item').forEach(function(item) {
        const quantity = parseFloat(item.querySelector('.quantity').value) || 0;
        const price = parseFloat(item.querySelector('.product-select option:checked').getAttribute('data-price')) || 0;
        const subtotal = quantity * price;
        item.querySelector('.subtotal').textContent = subtotal.toFixed(2);
        total += subtotal;
    });
    document.getElementById('total_amount').value = total.toFixed(2);
}