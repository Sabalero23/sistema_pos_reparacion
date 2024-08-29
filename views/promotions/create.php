<div class="container mt-4">
    <h1 class="mb-4">Crear Nueva Promoción</h1>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="post" action="<?php echo url('promotions.php?action=create'); ?>">
        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Descripción</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
        </div>
        <div class="mb-3">
            <label for="discount_type" class="form-label">Tipo de Descuento</label>
            <select class="form-select" id="discount_type" name="discount_type" required>
                <option value="">Selecciona un tipo</option>
                <option value="percentage">Porcentaje</option>
                <option value="fixed">Valor Fijo</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="discount_value" class="form-label">Valor de Descuento</label>
            <input type="number" step="0.01" class="form-control" id="discount_value" name="discount_value" required>
        </div>
        <div class="mb-3">
            <label for="start_date" class="form-label">Fecha de Inicio</label>
            <input type="date" class="form-control" id="start_date" name="start_date" required>
        </div>
        <div class="mb-3">
            <label for="end_date" class="form-label">Fecha de Fin</label>
            <input type="date" class="form-control" id="end_date" name="end_date" required>
        </div>
        <div class="mb-3">
            <label for="product_id" class="form-label">Producto</label>
            <select class="form-select" id="product_id" name="product_id">
                <option value="">Selecciona un producto (opcional)</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Crear Promoción</button>
        <a href="<?php echo url('promotions.php'); ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>