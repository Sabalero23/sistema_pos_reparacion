<div class="container mt-4">
    <h1 class="mb-4">Editar Categoría</h1>

    <form action="<?php echo url('categories.php?action=edit&id=' . $category['id']); ?>" method="post" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" required maxlength="50">
            <div class="invalid-feedback">
                Por favor, ingrese un nombre para la categoría.
            </div>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Descripción</label>
            <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($category['description']); ?></textarea>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="active_in_store" name="active_in_store" value="1" <?php echo $category['active_in_store'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="active_in_store">Activa en la tienda online</label>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Categoría</button>
        <a href="<?php echo url('categories.php'); ?>" class="btn btn-secondary">Cancelar</a>
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