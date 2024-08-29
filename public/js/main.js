// main.js - Funcionalidades principales del sistema POS

// Asegurarse de que el DOM esté completamente cargado antes de ejecutar el script
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar componentes
    initializeComponents();

    // Configurar manejadores de eventos globales
    setupGlobalEventHandlers();
});

// Función para inicializar componentes de la interfaz
function initializeComponents() {
    // Inicializar tooltips de Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Inicializar popovers de Bootstrap
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Inicializar datepickers si existen
    if ($.fn.datepicker) {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });
    }

    // Inicializar select2 si existe
    if ($.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap-5'
        });
    }
}

// Configurar manejadores de eventos globales
function setupGlobalEventHandlers() {
    // Manejar clics en botones de eliminación
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('delete-btn')) {
            event.preventDefault();
            confirmDelete(event.target.href);
        }
    });

    // Manejar envío de formularios
    document.addEventListener('submit', function(event) {
        if (event.target.classList.contains('needs-validation')) {
            if (!validateForm(event.target)) {
                event.preventDefault();
                event.stopPropagation();
            }
        }
    });

    // Manejar cambios en campos de cantidad
    document.addEventListener('change', function(event) {
        if (event.target.classList.contains('quantity-input')) {
            updateLineTotal(event.target);
        }
    });
}

// Función para confirmar eliminación
function confirmDelete(url) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}

// Función para validar formularios
function validateForm(form) {
    var isValid = form.checkValidity();
    form.classList.add('was-validated');
    return isValid;
}

// Función para actualizar el total de una línea en un formulario de venta
function updateLineTotal(quantityInput) {
    var row = quantityInput.closest('tr');
    var price = parseFloat(row.querySelector('.price').textContent);
    var quantity = parseFloat(quantityInput.value);
    var totalElement = row.querySelector('.line-total');
    var total = price * quantity;
    totalElement.textContent = total.toFixed(2);
    updateGrandTotal();
}

// Función para actualizar el total general en un formulario de venta
function updateGrandTotal() {
    var total = 0;
    document.querySelectorAll('.line-total').forEach(function(element) {
        total += parseFloat(element.textContent);
    });
    document.getElementById('grand-total').textContent = total.toFixed(2);
}

// Función para cargar datos via AJAX
function loadData(url, targetElement) {
    fetch(url)
        .then(response => response.text())
        .then(data => {
            document.querySelector(targetElement).innerHTML = data;
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al cargar los datos', 'error');
        });
}

// Función para mostrar notificaciones
function showNotification(message, type = 'info') {
    Swal.fire({
        title: message,
        icon: type,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
}

// Función para formatear números como moneda
function formatCurrency(amount) {
    return new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' }).format(amount);
}

// Función para manejar la paginación AJAX
function setupAjaxPagination() {
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('ajax-pagination')) {
            event.preventDefault();
            var url = event.target.href;
            var targetElement = event.target.dataset.target;
            loadData(url, targetElement);
        }
    });
}

// Función para manejar búsquedas en tiempo real
function setupLiveSearch(inputSelector, url, targetSelector) {
    let timer;
    document.querySelector(inputSelector).addEventListener('input', function() {
        clearTimeout(timer);
        timer = setTimeout(() => {
            const searchTerm = this.value;
            fetch(`${url}?search=${searchTerm}`)
                .then(response => response.text())
                .then(data => {
                    document.querySelector(targetSelector).innerHTML = data;
                })
                .catch(error => console.error('Error:', error));
        }, 300);
    });
}

// Exportar funciones para uso global
window.POS = {
    confirmDelete: confirmDelete,
    validateForm: validateForm,
    updateLineTotal: updateLineTotal,
    loadData: loadData,
    showNotification: showNotification,
    formatCurrency: formatCurrency,
    setupAjaxPagination: setupAjaxPagination,
    setupLiveSearch: setupLiveSearch
};