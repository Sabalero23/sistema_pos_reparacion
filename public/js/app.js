// Función para mostrar notificaciones con SweetAlert2
function showNotification(type, message) {
    Swal.fire({
        icon: type,
        title: type.charAt(0).toUpperCase() + type.slice(1),
        text: message,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
}

// Función para confirmar acciones
function confirmAction(title, text, confirmButtonText, cancelButtonText) {
    return Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: confirmButtonText,
        cancelButtonText: cancelButtonText
    });
}

// Manejar errores de AJAX
$(document).ajaxError(function(event, jqXHR, ajaxSettings, thrownError) {
    showNotification('error', 'Ha ocurrido un error: ' + thrownError);
});

// Inicializar tooltips de Bootstrap
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});

// Manejar la confirmación de eliminación para elementos con la clase .delete-confirm
$(document).on('click', '.delete-confirm', function(e) {
    e.preventDefault();
    var url = $(this).attr('href');
    
    confirmAction('¿Estás seguro?', 'Esta acción no se puede deshacer', 'Sí, eliminar', 'Cancelar')
    .then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
});

// Manejar la confirmación de formularios con la clase .confirm-form
$(document).on('submit', '.confirm-form', function(e) {
    e.preventDefault();
    var form = $(this);
    
    confirmAction('¿Estás seguro?', 'Se guardarán los cambios', 'Sí, guardar', 'Cancelar')
    .then((result) => {
        if (result.isConfirmed) {
            form.off('submit').submit();
        }
    });
});