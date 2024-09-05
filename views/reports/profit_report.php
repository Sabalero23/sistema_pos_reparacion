<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/roles.php';
require_once __DIR__ . '/../../includes/report_functions.php';

if (!isLoggedIn() || !hasPermission('reports_view')) {
    $_SESSION['flash_message'] = "No tienes permiso para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('index.php'));
    exit;
}

$pageTitle = "Reporte de Ganancias";
require_once __DIR__ . '/../../includes/header.php';

$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;

$reportData = generateProfitReport($startDate, $endDate);

?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <h1 class="mb-2">Reporte de Ganancias</h1>
        <a href="<?php echo url('reports.php'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver a Reportes
        </a>
    </div>

    <form method="GET" action="" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="start_date">Fecha de inicio:</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo $startDate; ?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="end_date">Fecha de fin:</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo $endDate; ?>">
                </div>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Generar Reporte</button>
            </div>
        </div>
    </form>

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="card-title">Resumen de Ganancias</h2>
            <?php if ($startDate && $endDate): ?>
                <p>Periodo: <?php echo date('d/m/Y', strtotime($startDate)); ?> - <?php echo date('d/m/Y', strtotime($endDate)); ?></p>
            <?php else: ?>
                <p>Periodo: Todos los tiempos</p>
            <?php endif; ?>
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
</div>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>