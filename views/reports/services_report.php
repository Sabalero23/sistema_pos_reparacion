<?php
// views/reports/services_report.php

$pageTitle = "Reporte de Servicios";
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1>Reporte de Servicios</h1>
            <p class="mb-0">Periodo: <?php echo htmlspecialchars($startDate); ?> - <?php echo htmlspecialchars($endDate); ?></p>
        </div>
        <div class="col-auto">
            <a href="<?php echo url('reports.php'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver a Reportes
        </a>
    </div>
    </div>
    

    <h2>Resumen</h2>
    <table class="table table-bordered">
        <tr>
            <th>Total de órdenes</th>
            <td><?php echo $reportData['summary']['total_orders']; ?></td>
        </tr>
        <tr>
            <th>Órdenes abiertas</th>
            <td><?php echo $reportData['summary']['open_orders']; ?></td>
        </tr>
        <tr>
            <th>Órdenes en progreso</th>
            <td><?php echo $reportData['summary']['in_progress_orders']; ?></td>
        </tr>
        <tr>
            <th>Órdenes cerradas</th>
            <td><?php echo $reportData['summary']['closed_orders']; ?></td>
        </tr>
        <tr>
            <th>Órdenes canceladas</th>
            <td><?php echo $reportData['summary']['cancelled_orders']; ?></td>
        </tr>
        <tr>
            <th>Tiempo promedio de resolución (días)</th>
            <td><?php echo number_format($reportData['summary']['avg_resolution_time'], 2); ?></td>
        </tr>
        <tr>
            <th>Ingreso total</th>
            <td><?php echo number_format($reportData['summary']['total_revenue'], 2); ?></td>
        </tr>
        <tr>
            <th>Valor promedio por orden</th>
            <td><?php echo number_format($reportData['summary']['avg_order_value'], 2); ?></td>
        </tr>
    </table>

    <h2>Servicios más comunes</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Servicio</th>
                <th>Cantidad</th>
                <th>Ingreso total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reportData['top_services'] as $service): ?>
            <tr>
                <td><?php echo htmlspecialchars($service['description']); ?></td>
                <td><?php echo $service['count']; ?></td>
                <td><?php echo number_format($service['total_revenue'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Detalles de órdenes</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Número de orden</th>
                <th>Cliente</th>
                <th>Estado</th>
                <th>Fecha de creación</th>
                <th>Última actualización</th>
                <th>Monto total</th>
                <th>Monto prepagado</th>
                <th>Dispositivo</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reportData['orders'] as $order): ?>
            <tr>
                <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                <td><?php echo htmlspecialchars($order['status']); ?></td>
                <td><?php echo $order['created_at']; ?></td>
                <td><?php echo $order['updated_at']; ?></td>
                <td><?php echo number_format($order['total_amount'], 2); ?></td>
                <td><?php echo number_format($order['prepaid_amount'], 2); ?></td>
                <td><?php echo htmlspecialchars($order['brand'] . ' ' . $order['model']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>