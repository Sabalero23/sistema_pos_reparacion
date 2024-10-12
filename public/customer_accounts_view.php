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
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 800px; }
        .card { box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); }
        .card-title { color: #007bff; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4 text-center">Detalles de la Cuenta</h1>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">Cliente: <?php echo htmlspecialchars($account['customer_name']); ?></h5>
                <div class="row">
                    <div class="col-md-6">
                        <p class="card-text"><strong>Monto Total:</strong> <?php echo number_format($account['total_amount'], 2); ?> ARS</p>
                        <p class="card-text"><strong>Saldo Pendiente:</strong> <?php echo number_format($account['balance'], 2); ?> ARS</p>
                        <p class="card-text"><strong>Cuotas Totales:</strong> <?php echo $account['num_installments']; ?></p>
                        <p class="card-text"><strong>Cuotas Pendientes:</strong> <?php echo $account['pending_installments'] ?? 'N/A'; ?></p>
                    </div>
                    <div class="col-md-6">
                        <p class="card-text"><strong>Próximo Vencimiento:</strong> <?php echo $account['next_due_date'] ? date('d/m/Y', strtotime($account['next_due_date'])) : 'N/A'; ?></p>
                        <p class="card-text"><strong>Último Pago:</strong> <?php echo $account['last_payment_date'] ? date('d/m/Y', strtotime($account['last_payment_date'])) : 'N/A'; ?></p>
                        <p class="card-text"><strong>Estado:</strong> <?php echo ucfirst($account['status'] ?? 'No especificado'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>