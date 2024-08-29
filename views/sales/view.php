<div class="container mt-4">
    <h1 class="mb-4">Detalles de la Venta</h1>

    <div class="card mb-4">
        <div class="card-header">
            Información General
        </div>
        <div class="card-body">
            <p><strong>ID de Venta:</strong> <?php echo $sale['id']; ?></p>
            <p><strong>Fecha:</strong> <?php echo $sale['sale_date']; ?></p>
            <p><strong>Cliente:</strong> <?php echo htmlspecialchars($sale['customer_name'] ?? 'N/A'); ?></p>
            <p><strong>Vendedor:</strong> <?php echo htmlspecialchars($sale['user_name']); ?></p>
            <p><strong>Estado:</strong> <?php echo ucfirst($sale['status']); ?></p>
            <p><strong>Método de Pago:</strong> <?php echo ucfirst($sale['payment_method']); ?></p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            Productos Vendidos
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($saleItems as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td><?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo number_format($item['quantity'] * $item['price'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Total:</th>
                        <th><?php echo number_format($sale['total_amount'], 2); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="mb-3">
        <a href="<?php echo url('/../views/sales/sale_receipt.php?id=' . $sale['id']); ?>" class="btn btn-primary" target="_blank">
            <i class="fas fa-print"></i> Imprimir Comprobante
        </a>
        <?php if ($sale['status'] === 'completed' && hasPermission('sales_cancel')): ?>
            <button id="cancelSale" class="btn btn-danger" data-id="<?php echo $sale['id']; ?>">Cancelar Venta</button>
        <?php endif; ?>
        <a href="<?php echo url('sales.php'); ?>" class="btn btn-secondary">Volver a la Lista</a>
    </div>
</div>

<script src="<?php echo url('js/sales.js'); ?>"></script>