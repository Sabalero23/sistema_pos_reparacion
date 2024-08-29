<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-2">Reporte de Ventas</h1>
        <p class="mb-0">Período: <?php echo date('d/m/Y', strtotime($startDate)); ?> - <?php echo date('d/m/Y', strtotime($endDate . ' -1 day')); ?></p>
    </div>
    <a href="<?php echo url('reports.php'); ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver a Reportes
    </a>
</div>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total de Ventas</h5>
                    <p class="card-text">
                        <?php echo number_format($reportData['summary']['total_sales'] ?? 0, 2); ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Número de Ventas</h5>
                    <p class="card-text">
                        <?php echo $reportData['summary']['number_of_sales'] ?? 0; ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Venta Promedio</h5>
                    <p class="card-text">
                        <?php echo number_format($reportData['summary']['average_sale'] ?? 0, 2); ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Venta Más Alta</h5>
                    <p class="card-text">
                        <?php echo number_format($reportData['summary']['highest_sale'] ?? 0, 2); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <h3>Detalle de Ventas</h3>
    <?php if (!empty($reportData['sales'])): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Método de Pago</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reportData['sales'] as $sale): ?>
                <tr>
                    <td><?php echo htmlspecialchars($sale['id']); ?></td>
                    <td><?php echo htmlspecialchars($sale['sale_date']); ?></td>
                    <td><?php echo htmlspecialchars($sale['customer_name'] ?? 'N/A'); ?></td>
                    <td><?php echo number_format($sale['total_amount'], 2); ?></td>
                    <td><?php echo htmlspecialchars($sale['payment_method']); ?></td>
                    <td><?php echo htmlspecialchars($sale['status']); ?></td>
                    <td>
                        <a href="<?php echo url('sales.php?action=view&id=' . $sale['id']); ?>" class="btn btn-sm btn-info">Ver</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No se encontraron ventas en el período seleccionado.</p>
    <?php endif; ?>
    
    <h3>Ventas por Método de Pago</h3>
    <?php if (!empty($reportData['payment_methods'])): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Método de Pago</th>
                    <th>Número de Ventas</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reportData['payment_methods'] as $method): ?>
                <tr>
                    <td><?php echo htmlspecialchars($method['payment_method']); ?></td>
                    <td><?php echo $method['count']; ?></td>
                    <td><?php echo number_format($method['total'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay datos de métodos de pago disponibles.</p>
    <?php endif; ?>
    
    <button onclick="window.print()" class="btn btn-primary">Imprimir Reporte</button>
</div>