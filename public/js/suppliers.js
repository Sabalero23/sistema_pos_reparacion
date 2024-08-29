document.addEventListener('DOMContentLoaded', function() {
    // Inicializar DataTables
    if ($.fn.DataTable) {
        $('#suppliersTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
            }
        });
    }

    // Manejar la eliminación de proveedores
    const deleteButtons = document.querySelectorAll('.delete-supplier');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const supplierId = this.getAttribute('data-id');
            Swal.fire({
                title: '¿Estás seguro?',
                text: "No podrás revertir esta acción",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteSupplier(supplierId);
                }
            });
        });
    });

    // Función para eliminar proveedor via AJAX
function deleteSupplier(supplierId) {
    fetch(`${baseUrl}/suppliers.php?action=delete&id=${supplierId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            Swal.fire('Eliminado!', data.message, 'success');
            // Remover la fila de la tabla
            const table = $('#suppliersTable').DataTable();
            table.row($(`button[data-id="${supplierId}"]`).parents('tr')).remove().draw();
        } else {
            Swal.fire('Error!', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error!', 'Hubo un problema al eliminar el proveedor.', 'error');
    });
}

    // Validación de formularios
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
});