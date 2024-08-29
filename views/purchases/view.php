<div class="container mt-4">
    <h1 class="mb-4">Detalles de Compra #<?php echo $purchase['id']; ?></h1>

    <a href="<?php echo url('purchases.php'); ?>" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Volver a la lista
    </a>

    <?php if (hasPermission('purchases_view_movements')): ?>
        <a href="<?php echo url('purchases.php?action=movements&id=' . $purchase['id']); ?>" class="btn btn-info mb-3 ml-2">
            <i class="fas fa-history"></i> Ver Movimientos
        </a>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            Información de la Compra
        </div>
        <div class="card-body">
            <p><strong>Proveedor:</strong> <?php echo htmlspecialchars($purchase['supplier_name']); ?></p>
            <p><strong>Fecha de Compra:</strong> <?php echo $purchase['purchase_date']; ?></p>
            <p><strong>Total:</strong> $<?php echo number_format($purchase['total_amount'], 2); ?></p>
            <p><strong>Estado:</strong> <?php echo ucfirst($purchase['status']); ?></p>
            <?php if ($purchase['status'] === 'recibido'): ?>
                <p><strong>Fecha de Recepción:</strong> <?php echo $purchase['received_date']; ?></p>
            <?php endif; ?>
        </div>
    </div>

    <h2 class="mb-3">Ítems de la Compra</h2>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                    <?php if ($purchase['status'] === 'recibido'): ?>
                        <th>Cantidad Recibida</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($purchaseItems as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td>$<?php echo number_format($item['quantity'] * $item['price'], 2); ?></td>
                        <?php if ($purchase['status'] === 'recibido'): ?>
                            <td><?php echo $item['received_quantity']; ?></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($purchase['status'] === 'pendiente' && hasPermission('purchases_receive')): ?>
        <a href="<?php echo url('purchases.php?action=receive&id=' . $purchase['id']); ?>" class="btn btn-success mt-3">
            <i class="fas fa-check"></i> Recibir Compra
        </a>
    <?php endif; ?>

    <?php if ($purchase['status'] === 'pendiente' && hasPermission('purchases_cancel')): ?>
        <button class="btn btn-danger mt-3 cancel-purchase" data-id="<?php echo $purchase['id']; ?>">
            <i class="fas fa-times"></i> Cancelar Compra
        </button>
    <?php endif; ?>
</div>

<script src="<?php echo url('js/purchases.js'); ?>"></script>