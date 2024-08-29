<div class="container mt-4">
    <h1 class="mb-4">Añadir Producto</h1>

    <form action="<?php echo url('products.php?action=add'); ?>" method="post" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="name" name="name" required>
            <div class="invalid-feedback">
                Por favor, ingrese un nombre para el producto.
            </div>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Descripción</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
        </div>
        <div class="mb-3">
            <label for="sku" class="form-label">SKU</label>
            <input type="text" class="form-control" id="sku" name="sku" required>
            <div class="invalid-feedback">
                Por favor, ingrese un SKU para el producto.
            </div>
        </div>
        <div class="mb-3">
            <label for="category_id" class="form-label">Categoría</label>
            <select class="form-select" id="category_id" name="category_id" required>
                <option value="">Seleccione una categoría</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">
                Por favor, seleccione una categoría.
            </div>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Precio de Venta</label>
            <input type="number" class="form-control" id="price" name="price" step="0.01" required>
            <div class="invalid-feedback">
                Por favor, ingrese un precio válido.
            </div>
        </div>
        <div class="mb-3">
            <label for="cost_price" class="form-label">Precio de Costo</label>
            <input type="number" class="form-control" id="cost_price" name="cost_price" step="0.01" required>
            <div class="invalid-feedback">
                Por favor, ingrese un precio de costo válido.
            </div>
        </div>
        <div class="mb-3">
            <label for="stock_quantity" class="form-label">Cantidad en Stock</label>
            <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" required>
            <div class="invalid-feedback">
                Por favor, ingrese una cantidad válida.
            </div>
        </div>
        <div class="mb-3">
            <label for="reorder_level" class="form-label">Nivel de Reorden</label>
            <input type="number" class="form-control" id="reorder_level" name="reorder_level" required>
            <div class="invalid-feedback">
                Por favor, ingrese un nivel de reorden válido.
            </div>
        </div>
        <div class="mb-3">
            <label for="supplier_id" class="form-label">Proveedor</label>
            <select class="form-select" id="supplier_id" name="supplier_id">
                <option value="">Seleccione un proveedor</option>
                <?php foreach ($suppliers as $supplier): ?>
                    <option value="<?php echo $supplier['id']; ?>"><?php echo htmlspecialchars($supplier['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Añadir Producto</button>
        <a href="<?php echo url('products.php'); ?>" class="btn btn-secondary">Cancelar</a>
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