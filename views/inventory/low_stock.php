<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Productos con Bajo Stock</h1>
        <a href="<?php echo url('inventory.php'); ?>" class="btn btn-secondary">Regresar al Inventario</a>
    </div>

    <table id="lowStockTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>SKU</th>
                <th>Stock Actual</th>
                <th>Stock Mínimo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lowStockProducts as $product): ?>
                <tr>
                    <td><?php echo $product['id']; ?></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['sku']); ?></td>
                    <td><?php echo $product['stock_quantity']; ?></td>
                    <td><?php echo $product['min_stock']; ?></td>
                    <td>
                        <a href="<?php echo url('purchases.php?action=create&product_id=' . $product['id']); ?>" class="btn btn-sm btn-primary">Crear Orden de Compra</a>
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
    $('#lowStockTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        }
    });
});
</script>