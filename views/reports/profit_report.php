<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <h1 class="mb-2">Reporte de Ganancias</h1>
        <a href="<?php echo url('reports.php'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver a Reportes
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="card-title">Resumen de Ganancias</h2>
            <p>Periodo: <?php echo date('d/m/Y', strtotime($reportData['start_date'])); ?> - <?php echo date('d/m/Y', strtotime($reportData['end_date'])); ?></p>
            <div class="row">
                <div class="col-md-3">
                    <h5>Total Ventas</h5>
                    <p class="h3">$<?php echo number_format($reportData['total_sales'], 2); ?></p>
                </div>
                <div class="col-md-3">
                    <h5>Total Ingresos en Efectivo</h5>
                    <p class="h3">$<?php echo number_format($reportData['total_cash_in'], 2); ?></p>
                </div>
                <div class="col-md-3">
                    <h5>Total Compras</h5>
                    <p class="h3">$<?php echo number_format($reportData['total_purchases'], 2); ?></p>
                </div>
                <div class="col-md-3">
                    <h5>Ganancia</h5>
                    <p class="h3 <?php echo $reportData['profit'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                        $<?php echo number_format($reportData['profit'], 2); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <button onclick="window.print()" class="btn btn-primary">Imprimir Reporte</button>

    <?php if (isset($_GET['debug']) && $_GET['debug'] == '1'): ?>
    <div class="card mt-4">
        <div class="card-body">
            <h3>Datos de depuración</h3>
            <pre><?php print_r($reportData['debug_info']); ?></pre>
        </div>
    </div>
    <?php endif; ?>
</div>