<div class="container mt-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-4">Ajustar Inventario</h1>
        <a href="<?php echo url('inventory.php'); ?>" class="btn btn-secondary">Regresar al Inventario</a>
    </div>

    <form action="<?php echo url('inventory.php?action=adjust'); ?>" method="post">
        <div class="mb-3">
            <label for="notes" class="form-label">Notas del Ajuste</label>
            <textarea class="form-control" id="notes" name="notes" rows="3" required></textarea>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Stock Actual</th>
                    <th>Nuevo Stock</th>
                    <th>Razón</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo $product['stock_quantity']; ?></td>
                        <td>
                            <input type="number" class="form-control" name="products[<?php echo $product['id']; ?>][new_quantity]" value="<?php echo $product['stock_quantity']; ?>" required>
                        </td>
                        <td>
                           <select class="form-control" name="products[<?php echo $product['id']; ?>][reason]" required>
    <option value="dañado">Dañado</option>
    <option value="perdido">Perdido</option>
    <option value="correccion">Corrección</option>
    <option value="otro">Otro</option>
</select>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">Guardar Ajuste</button>
        <a href="<?php echo url('inventory.php'); ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>