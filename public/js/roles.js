document.addEventListener('DOMContentLoaded', function() {
    // Inicializar DataTables
    if ($.fn.DataTable) {
        $('#rolesTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
            }
        });
    }

    // Manejar la eliminación de roles
    const deleteButtons = document.querySelectorAll('.delete-role');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const roleId = this.getAttribute('data-id');
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
                    deleteRole(roleId);
                }
            });
        });
    });

    // Función para eliminar rol via AJAX
    function deleteRole(roleId) {
        fetch(`${baseUrl}/roles.php?action=delete&id=${roleId}`, {
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
                const table = $('#rolesTable').DataTable();
                table.row($(`button[data-id="${roleId}"]`).parents('tr')).remove().draw();
            } else {
                Swal.fire('Error!', data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error!', 'Hubo un problema al eliminar el rol.', 'error');
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

    // Manejo de permisos en la vista de permisos
    const permissionForm = document.querySelector('form[action*="action=permissions"]');
    if (permissionForm) {
        permissionForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Éxito', data.message, 'success').then(() => {
                        window.location.href = `${baseUrl}/roles.php`;
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Hubo un problema al actualizar los permisos.', 'error');
            });
        });
    }
});