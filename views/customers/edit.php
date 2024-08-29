<div class="container mt-4">
    <h1 class="mb-4">Editar Cliente</h1>

    <form action="<?php echo url('customers.php?action=edit&id=' . $customer['id']); ?>" method="post" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="name" class="form-label">Nombre del Cliente</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($customer['name']); ?>" required>
            <div class="invalid-feedback">
                Por favor, ingrese el nombre del cliente.
            </div>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>">
            <div class="invalid-feedback">
                Por favor, ingrese un email válido.
            </div>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Teléfono</label>
            <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>" pattern="[0-9]{1,12}">
            <div class="invalid-feedback">
                Por favor, ingrese un número de teléfono válido (hasta 12 dígitos).
            </div>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Dirección</label>
            <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($customer['address']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Cliente</button>
        <a href="<?php echo url('customers.php'); ?>" class="btn btn-secondary">Cancelar</a>
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