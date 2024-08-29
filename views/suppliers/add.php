<div class="container mt-4">
    <h1 class="mb-4">Añadir Proveedor</h1>

    <form action="<?php echo url('suppliers.php?action=add'); ?>" method="post" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="name" class="form-label">Nombre del Proveedor</label>
            <input type="text" class="form-control" id="name" name="name" required>
            <div class="invalid-feedback">
                Por favor, ingrese el nombre del proveedor.
            </div>
        </div>
        <div class="mb-3">
            <label for="contact_person" class="form-label">Persona de Contacto</label>
            <input type="text" class="form-control" id="contact_person" name="contact_person">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email">
            <div class="invalid-feedback">
                Por favor, ingrese un email válido.
            </div>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Teléfono</label>
            <input type="tel" class="form-control" id="phone" name="phone" pattern="[0-9]{10}">
            <div class="invalid-feedback">
                Por favor, ingrese un número de teléfono válido (10 dígitos).
            </div>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Dirección</label>
            <textarea class="form-control" id="address" name="address" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Añadir Proveedor</button>
        <a href="<?php echo url('suppliers.php'); ?>" class="btn btn-secondary">Cancelar</a>
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