<div class="container mt-4">
    <h1 class="mb-4">Editar Rol</h1>

    <form action="<?php echo url('roles.php?action=edit&id=' . $role['id']); ?>" method="post" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="name" class="form-label">Nombre del Rol</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($role['name']); ?>" required>
            <div class="invalid-feedback">
                Por favor, ingrese un nombre para el rol.
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Rol</button>
        <a href="<?php echo url('roles.php'); ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.needs-validation');
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
</script>