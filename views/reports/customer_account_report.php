<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/report_functions.php';

session_start();

if (!isLoggedIn() || !hasPermission('reports_view')) {
    $_SESSION['flash_message'] = "No tienes permiso para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('index.php'));
    exit;
}

$customerId = $_GET['id'] ?? null;

if (!$customerId) {
    $_SESSION['flash_message'] = "ID de cliente no proporcionado.";
    $_SESSION['flash_type'] = 'error';
    header('Location: ' . url('reports.php'));
    exit;
}

$pageTitle = "Reporte de Cuenta de Cliente";
require_once __DIR__ . '/../includes/header.php';

$customer = getCustomerById($customerId);
$accountSummary = getCustomerAccountSummary($customerId);
$transactions = getCustomerTransactions($customerId);

?>

<div class="container mt-4">
    <h1 class="mb-4">Reporte de Cuenta de Cliente</h1>
    <h2><?php echo htmlspecialchars($customer['name']); ?></h2>
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Saldo Actual</h5>
                    <p class="card-text"><?php echo number_format($accountSummary['balance'], 2); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total de Ventas</h5>
                    <p class="card-text"><?php echo number_format($accountSummary['total_sales'], 2); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total de Pagos</h5>
                    <p class="card-text"><?php echo number_format($accountSummary['total_payments'], 2); ?></p>
                </div>
            </div>
        </div>
    </div>

    <h3>Historial de Transacciones</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Monto</th>
                <th>Saldo</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $transaction): ?>
            <tr>
                <td><?php echo htmlspecialchars($transaction['date']); ?></td>
                <td><?php echo htmlspecialchars($transaction['type']); ?></td>
                <td><?php echo number_format($transaction['amount'], 2); ?></td>
                <td><?php echo number_format($transaction['balance'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <button onclick="window.print()" class="btn btn-primary">Imprimir Reporte</button>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>