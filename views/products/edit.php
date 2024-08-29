<?php
// Asegúrate de que $product, $categories y $suppliers estén disponibles en este punto
if (!isset($product) || !isset($categories) || !isset($suppliers)) {
    die('Error: Datos del producto no disponibles.');
}
?>

<div class="container mt-4">
    <h1 class="mb-4">Editar Producto</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="<?php echo url('products.php?action=edit&id=' . $product['id']); ?>" method="post" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            <div class="invalid-feedback">
                Por favor, ingrese un nombre para el producto.
            </div>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Descripción</label>
            <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="sku" class="form-label">SKU</label>
            <input type="text" class="form-control" id="sku" name="sku" value="<?php echo htmlspecialchars($product['sku']); ?>" required>
            <div class="invalid-feedback">
                Por favor, ingrese un SKU para el producto.
            </div>
        </div>
        <div class="mb-3">
            <label for="category_id" class="form-label">Categoría</label>
            <select class="form-select" id="category_id" name="category_id" required>
                <option value="">Seleccione una categoría</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>" <?php echo ($category['id'] == $product['category_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">
                Por favor, seleccione una categoría.
            </div>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Precio de Venta</label>
            <input type="number" class="form-control" id="price" name="price" step="0.01" value="<?php echo number_format($product['price'], 2, '.', ''); ?>" required>
            <div class="invalid-feedback">
                Por favor, ingrese un precio válido.
            </div>
        </div>
        <div class="mb-3">
            <label for="cost_price" class="form-label">Precio de Costo</label>
            <input type="number" class="form-control" id="cost_price" name="cost_price" step="0.01" value="<?php echo number_format($product['cost_price'], 2, '.', ''); ?>" required>
            <div class="invalid-feedback">
                Por favor, ingrese un precio de costo válido.
            </div>
        </div>
        <div class="mb-3">
            <label for="stock_quantity" class="form-label">Cantidad en Stock</label>
            <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" value="<?php echo $product['stock_quantity']; ?>" required>
            <div class="invalid-feedback">
                Por favor, ingrese una cantidad válida.
            </div>
        </div>
        <div class="mb-3">
            <label for="reorder_level" class="form-label">Nivel de Reorden</label>
            <input type="number" class="form-control" id="reorder_level" name="reorder_level" value="<?php echo $product['reorder_level']; ?>" required>
            <div class="invalid-feedback">
                Por favor, ingrese un nivel de reorden válido.
            </div>
        </div>
        <div class="mb-3">
            <label for="supplier_id" class="form-label">Proveedor</label>
            <select class="form-select" id="supplier_id" name="supplier_id">
                <option value="">Seleccione un proveedor</option>
                <?php foreach ($suppliers as $supplier): ?>
                    <option value="<?php echo $supplier['id']; ?>" <?php echo ($supplier['id'] == $product['supplier_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($supplier['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Producto</button>
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