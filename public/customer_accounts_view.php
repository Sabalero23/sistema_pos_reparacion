<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/customer_account_functions.php';

$token = $_GET['token'] ?? '';

if (empty($token)) {
    die("Acceso no autorizado.");
}

$account = getAccountByToken($token);

if (!$account) {
    die("Cuenta no encontrada o token inválido.");
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de la Cuenta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Detalles de la Cuenta</h1>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Cliente: <?php echo htmlspecialchars($account['customer_name']); ?></h5>
                <p class="card-text">Monto Total: <?php echo number_format($account['total_amount'], 2); ?> ARS</p>
                <p class="card-text">Saldo Pendiente: <?php echo number_format($account['balance'], 2); ?> ARS</p>
                <p class="card-text">Cuotas Totales: <?php echo $account['num_installments']; ?></p>
                <p class="card-text">Cuotas Pendientes: <?php echo $account['pending_installments'] ?? 'N/A'; ?></p>
                <p class="card-text">Próximo Vencimiento: <?php echo $account['next_due_date'] ? date('d/m/Y', strtotime($account['next_due_date'])) : 'N/A'; ?></p>
                <p class="card-text">Último Pago: <?php echo $account['last_payment_date'] ? date('d/m/Y', strtotime($account['last_payment_date'])) : 'N/A'; ?></p>
                <p class="card-text">Estado: <?php echo ucfirst($account['status'] ?? 'No especificado'); ?></p>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>