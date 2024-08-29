<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-2">Reporte de Compras</h1>
            <p class="mb-0">Período: <?php echo date('d/m/Y', strtotime($startDate)); ?> - <?php echo date('d/m/Y', strtotime($endDate)); ?></p>
        </div>
        <a href="<?php echo url('reports.php'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver a Reportes
        </a>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total de Compras</h5>
                    <p class="card-text">
                        <?php echo number_format($reportData['summary']['total_purchases'], 2); ?> ARS
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Número de Compras</h5>
                    <p class="card-text">
                        <?php echo $reportData['summary']['number_of_purchases']; ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Compra Promedio</h5>
                    <p class="card-text">
                        <?php echo number_format($reportData['summary']['average_purchase'], 2); ?> ARS
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Compra Más Alta</h5>
                    <p class="card-text">
                        <?php echo number_format($reportData['summary']['highest_purchase'], 2); ?> ARS
                    </p>
                </div>
            </div>
        </div>
    </div>

    <h3>Detalle de Compras</h3>
    <?php if (!empty($reportData['purchases'])): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Proveedor</th>
                    <th>Total</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reportData['purchases'] as $purchase): ?>
                <tr>
                    <td><?php echo htmlspecialchars($purchase['id']); ?></td>
                    <td><?php echo htmlspecialchars($purchase['purchase_date']); ?></td>
                    <td><?php echo htmlspecialchars($purchase['supplier_name'] ?? 'N/A'); ?></td>
                    <td><?php echo number_format($purchase['total_amount'], 2); ?> ARS</td>
                    <td><?php echo htmlspecialchars($purchase['status']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No se encontraron compras en el período seleccionado.</p>
    <?php endif; ?>

    <h3>Top 10 Productos Más Comprados</h3>
    <?php if (!empty($reportData['top_products'])): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad Total</th>
                    <th>Monto Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reportData['top_products'] as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                    <td><?php echo $product['total_quantity']; ?></td>
                    <td><?php echo number_format($product['total_amount'], 2); ?> ARS</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay datos de productos comprados disponibles.</p>
    <?php endif; ?>
    
    <button onclick="window.print()" class="btn btn-primary mt-3">Imprimir Reporte</button>
</div>