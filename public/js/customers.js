document.addEventListener('DOMContentLoaded', function() {
    // Inicializar DataTables
    if ($.fn.DataTable) {
        $('#customersTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
            }
        });
    }

    // Manejar la eliminación de clientes
    const deleteButtons = document.querySelectorAll('.delete-customer');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const customerId = this.getAttribute('data-id');
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
                    deleteCustomer(customerId);
                }
            });
        });
    });

    // Función para eliminar cliente via AJAX
    function deleteCustomer(customerId) {
        fetch(`${baseUrl}/customers.php?action=delete&id=${customerId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Eliminado!', data.message, 'success');
                // Remover la fila de la tabla
                const table = $('#customersTable').DataTable();
                table.row($(`button[data-id="${customerId}"]`).parents('tr')).remove().draw();
            } else {
                Swal.fire('Error!', data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error!', 'Hubo un problema al eliminar el cliente.', 'error');
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