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

$pageTitle = "Reporte de Cuentas por Cobrar";
require_once __DIR__ . '/../../includes/header.php';

$reportData = generateAccountsReceivableReport();
$totalReceivable = array_sum(array_column($reportData, 'balance'));

?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="mb-2">Reporte de Cuentas por Cobrar</h1>
            <p class="mb-0">Total por cobrar: <?php echo number_format($totalReceivable, 2); ?> ARS</p>
        </div>
        <a href="<?php echo url('reports.php'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver a Reportes
        </a>
    </div>

    <?php if (empty($reportData)): ?>
        <div class="alert alert-info">No hay cuentas por cobrar en este momento.</div>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Monto Total</th>
                    <th>Entrega Inicial</th>
                    <th>Saldo Pendiente</th>
                    <th>Cuotas Pendientes</th>
                    <th>Próximo Vencimiento</th>
                    <th>Último Pago</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reportData as $account): ?>
                <tr>
                    <td><?php echo htmlspecialchars($account['customer_name']); ?></td>
                    <td><?php echo number_format($account['total_amount'], 2); ?> ARS</td>
                    <td><?php echo number_format($account['down_payment'], 2); ?> ARS</td>
                    <td><?php echo number_format($account['balance'], 2); ?> ARS</td>
                    <td><?php echo $account['pending_installments']; ?> / <?php echo $account['num_installments']; ?></td>
                    <td><?php echo $account['next_due_date'] ? date('d/m/Y', strtotime($account['next_due_date'])) : 'N/A'; ?></td>
                    <td><?php echo $account['last_payment_date'] ? date('d/m/Y', strtotime($account['last_payment_date'])) : 'N/A'; ?></td>
                    <td><?php echo ucfirst($account['status']); ?></td>
                    <td>
                        <a href="<?php echo url('customer_accounts.php?action=view&id=' . $account['account_id']); ?>" class="btn btn-sm btn-info">Ver Detalle</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    
        <button onclick="window.print()" class="btn btn-primary">Imprimir Reporte</button>
    <?php endif; ?>
</div>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>