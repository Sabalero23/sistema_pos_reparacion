<div class="container mt-4">
    <h1 class="mb-4">Inventario</h1>
    
    <div class="mb-3">
        <a href="<?php echo url('inventory.php?action=adjust'); ?>" class="btn btn-primary">Ajustar Inventario</a>
        <a href="<?php echo url('inventory.php?action=movements'); ?>" class="btn btn-secondary">Ver Movimientos</a>
        <a href="<?php echo url('inventory.php?action=low_stock'); ?>" class="btn btn-warning">Productos con Bajo Stock</a>
    </div>

    <table id="inventoryTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>SKU</th>
                <th>Categoría</th>
                <th>Stock Actual</th>
                <th>Stock Mínimo</th>
                <th>Stock Máximo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo $product['id']; ?></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['sku']); ?></td>
                    <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                    <td><?php echo $product['stock_quantity']; ?></td>
                    <td><?php echo $product['min_stock']; ?></td>
                    <td><?php echo $product['max_stock']; ?></td>
                    <td>
                        <a href="<?php echo url('inventory.php?action=movements&product_id=' . $product['id']); ?>" class="btn btn-sm btn-info">Movimientos</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css">

<script>
$(document).ready(function() {
    $('#inventoryTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        }
    });
});
</script>