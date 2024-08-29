<div class="container mt-4">
    <h1 class="mb-4">Añadir Rol</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="<?php echo url('roles.php?action=add'); ?>" method="post" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="name" class="form-label">Nombre del Rol</label>
            <input type="text" class="form-control" id="name" name="name" required>
            <div class="invalid-feedback">
                Por favor, ingrese un nombre para el rol.
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Añadir Rol</button>
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