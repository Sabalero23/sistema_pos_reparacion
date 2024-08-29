<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Movimientos de Stock</h1>
        <a href="<?php echo url('inventory.php'); ?>" class="btn btn-secondary">Regresar al Inventario</a>
    </div>

    <form action="<?php echo url('inventory.php?action=movements'); ?>" method="get" class="mb-4">
        <input type="hidden" name="action" value="movements">
        <div class="row">
            <div class="col-md-3">
                <label for="product_id" class="form-label">Producto</label>
                <select class="form-control" id="product_id" name="product_id">
                    <option value="">Todos los productos</option>
                    <?php foreach (getAllProducts() as $product): ?>
                       <option value="<?php echo $product['id']; ?>" <?php echo ($productId == $product['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($product['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="start_date" class="form-label">Fecha Inicio</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $startDate; ?>">
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">Fecha Fin</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $endDate; ?>">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </div>
        </div>
    </form>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Tipo de Movimiento</th>
                <th>Usuario</th>
                <th>Notas</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($movements as $movement): ?>
                <tr>
                    <td><?php echo $movement['created_at']; ?></td>
                    <td><?php echo htmlspecialchars($movement['product_name']); ?></td>
                    <td><?php echo $movement['quantity']; ?></td>
                    <td><?php echo ucfirst($movement['movement_type']); ?></td>
                    <td><?php echo htmlspecialchars($movement['user_name']); ?></td>
                    <td><?php echo htmlspecialchars($movement['notes']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>