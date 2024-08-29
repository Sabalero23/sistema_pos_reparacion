document.addEventListener('DOMContentLoaded', function() {
    // Inicializar DataTables
    if ($.fn.DataTable) {
        $('#categoriesTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
            }
        });
    }

    // Manejar la eliminación de categorías
    const deleteButtons = document.querySelectorAll('.delete-category');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const categoryId = this.getAttribute('data-id');
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
                    deleteCategory(categoryId);
                }
            });
        });
    });

    // Función para eliminar categoría via AJAX
    function deleteCategory(categoryId) {
        fetch(`${baseUrl}/categories.php?action=delete&id=${categoryId}`, {
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
                const table = $('#categoriesTable').DataTable();
                table.row($(`button[data-id="${categoryId}"]`).parents('tr')).remove().draw();
            } else {
                Swal.fire('Error!', data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error!', 'Hubo un problema al eliminar la categoría.', 'error');
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