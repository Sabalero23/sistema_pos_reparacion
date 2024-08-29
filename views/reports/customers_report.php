<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="mb-2">Reporte de Clientes</h1>
            <p class="mb-0">Período: <?php echo htmlspecialchars($startDate); ?> - <?php echo htmlspecialchars($endDate); ?></p>
        </div>
        <a href="<?php echo url('reports.php'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver a Reportes
        </a>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total de Clientes</h5>
                    <p class="card-text"><?php echo $reportData['summary']['total_customers']; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Clientes Activos</h5>
                    <p class="card-text"><?php echo $reportData['summary']['active_customers']; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total de Ventas</h5>
                    <p class="card-text"><?php echo number_format($reportData['summary']['total_sales'], 2); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Promedio por Cliente</h5>
                    <p class="card-text"><?php echo number_format($reportData['summary']['average_per_customer'], 2); ?></p>
                </div>
            </div>
        </div>
    </div>

    <h3>Top 10 Clientes</h3>
    <?php if (!empty($reportData['top_customers'])): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Total de Compras</th>
                    <th>Monto Total</th>
                    <th>Última Compra</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reportData['top_customers'] as $customer): ?>
                <tr>
                    <td><?php echo htmlspecialchars($customer['id']); ?></td>
                    <td><?php echo htmlspecialchars($customer['name']); ?></td>
                    <td><?php echo htmlspecialchars($customer['email']); ?></td>
                    <td><?php echo $customer['total_purchases']; ?></td>
                    <td><?php echo number_format($customer['total_amount'], 2); ?></td>
                    <td><?php echo $customer['last_purchase_date']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay datos de top clientes disponibles.</p>
    <?php endif; ?>

    <h3>Distribución de Clientes por Monto de Compra</h3>
    <?php if (!empty($reportData['customer_distribution'])): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Rango de Compra</th>
                    <th>Número de Clientes</th>
                    <th>Porcentaje</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reportData['customer_distribution'] as $range): ?>
                <tr>
                    <td><?php echo htmlspecialchars($range['purchase_range']); ?></td>
                    <td><?php echo htmlspecialchars($range['customer_count']); ?></td>
                    <td><?php echo number_format($range['percentage'], 2); ?>%</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay datos de distribución de clientes disponibles.</p>
    <?php endif; ?>

    <h3>Clientes Inactivos (Sin compras en el período)</h3>
    <?php if (!empty($reportData['inactive_customers'])): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Última Compra</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reportData['inactive_customers'] as $customer): ?>
                <tr>
                    <td><?php echo htmlspecialchars($customer['id']); ?></td>
                    <td><?php echo htmlspecialchars($customer['name']); ?></td>
                    <td><?php echo htmlspecialchars($customer['email']); ?></td>
                    <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                    <td><?php echo $customer['last_purchase_date'] ? htmlspecialchars($customer['last_purchase_date']) : 'Nunca'; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay clientes inactivos en el período seleccionado.</p>
    <?php endif; ?>
    
    <button onclick="window.print()" class="btn btn-primary">Imprimir Reporte</button>
</div>