<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/roles.php';
require_once __DIR__ . '/../../includes/utils.php';
require_once __DIR__ . '/../../includes/customer_account_functions.php';

if (!isLoggedIn() || !hasPermission('customer_accounts_view')) {
    $_SESSION['flash_message'] = "No tienes permiso para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('index.php'));
    exit;
}

$paymentId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if (!$paymentId) {
    die("ID de pago no proporcionado");
}

$payment = getPaymentById($paymentId);
$customer = getCustomerById($payment['customer_id']);

if (!$payment) {
    die("Pago no encontrado");
}

$companyInfo = getCompanyInfo();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Pago - <?php echo $payment['id']; ?></title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        body {
            font-family: Arial, sans-serif;
            line-height: 1.3;
            color: #333;
            margin: 0;
            padding: 10mm;
            font-size: 10pt;
        }
        .header {
            text-align: center;
            margin-bottom: 5mm;
        }
        .header img {
            max-width: 50mm;
            max-height: 20mm;
        }
        .header h1 {
            color: #2c3e50;
            margin: 2mm 0;
            font-size: 14pt;
        }
        .info-section {
            margin-bottom: 5mm;
        }
        .info-section h2 {
            color: #2c3e50;
            border-bottom: 1px solid #eee;
            padding-bottom: 1mm;
            font-size: 12pt;
            margin: 2mm 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5mm;
        }
        th, td {
            padding: 2mm;
            border: 0.5pt solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .total {
            font-weight: bold;
            text-align: right;
        }
        .footer {
            text-align: center;
            margin-top: 5mm;
            font-size: 8pt;
            color: #777;
        }
        @media print {
            body {
                width: 210mm;
                height: 297mm;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <?php if ($companyInfo['logo_path']): ?>
            <img src="<?php echo htmlspecialchars($companyInfo['logo_path']); ?>" alt="Logo de la empresa">
        <?php endif; ?>
        <h1><?php echo htmlspecialchars($companyInfo['name']); ?></h1>
        <p><?php echo htmlspecialchars($companyInfo['address']); ?></p>
        <p>Tel: <?php echo htmlspecialchars($companyInfo['phone']); ?> | Email: <?php echo htmlspecialchars($companyInfo['email']); ?></p>
        <?php if ($companyInfo['website']): ?>
            <p>Web: <?php echo htmlspecialchars($companyInfo['website']); ?></p>
        <?php endif; ?>
    </div>

    <div class="info-section">
        <h2>Recibo de Pago</h2>
        <p><strong>Número de Recibo:</strong> <?php echo $payment['id']; ?></p>
        <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($payment['payment_date'])); ?></p>
        <p><strong>Cliente:</strong> <?php echo htmlspecialchars($customer['name']); ?></p>
    </div>

    <div class="info-section">
        <h2>Detalles del Pago</h2>
        <table>
            <tr>
                <th>Monto</th>
                <td>$<?php echo number_format($payment['amount'], 2); ?></td>
            </tr>
            <tr>
                <th>Método de Pago</th>
                <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
            </tr>
            <?php if ($payment['notes']): ?>
            <tr>
                <th>Notas</th>
                <td><?php echo htmlspecialchars($payment['notes']); ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>

    <div class="footer">
        <p><?php echo htmlspecialchars($companyInfo['receipt_footer']); ?></p>
        <p><?php echo htmlspecialchars($companyInfo['legal_info']); ?></p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>