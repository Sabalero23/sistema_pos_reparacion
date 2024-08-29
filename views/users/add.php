<div class="container mt-4">
    <h1 class="mb-4">Añadir Usuario</h1>

    <form action="<?php echo url('users.php?action=add'); ?>" method="post" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="name" name="name" required>
            <div class="invalid-feedback">
                Por favor, ingrese un nombre.
            </div>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
            <div class="invalid-feedback">
                Por favor, ingrese un email válido.
            </div>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="password" name="password" required>
            <div class="invalid-feedback">
                Por favor, ingrese una contraseña.
            </div>
        </div>
        <div class="mb-3">
            <label for="role_id" class="form-label">Rol</label>
            <select class="form-select" id="role_id" name="role_id" required>
                <option value="">Seleccione un rol</option>
                <?php foreach ($roles as $role): ?>
                    <option value="<?php echo $role['id']; ?>"><?php echo htmlspecialchars($role['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">
                Por favor, seleccione un rol.
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Añadir Usuario</button>
        <a href="<?php echo url('users.php'); ?>" class="btn btn-secondary">Cancelar</a>
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