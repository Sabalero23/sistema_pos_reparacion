<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/roles.php';
require_once __DIR__ . '/../../includes/utils.php';
require_once __DIR__ . '/../../includes/customer_account_functions.php';
require_once __DIR__ . '/../../includes/payment_functions.php';

// Verificar autenticación y permisos
if (!isLoggedIn() || !hasPermission('payments_view')) {
    die("No tienes permiso para acceder a esta página.");
}

// Obtener el ID del pago y verificar que sea válido
$paymentId = filter_input(INPUT_GET, 'payment_id', FILTER_SANITIZE_NUMBER_INT);
if (!$paymentId) {
    die("ID de pago no proporcionado");
}

// Obtener los detalles del pago
$payment = getPaymentDetails($paymentId);
if (!$payment) {
    die("Pago no encontrado");
}

// Obtener información de la empresa
$companyInfo = getCompanyInfo();

// Función para formatear el método de pago
function formatPaymentMethod($method) {
    $methods = [
        'efectivo' => 'Efectivo',
        'tarjeta' => 'Tarjeta',
        'transferencia' => 'Transferencia Bancaria'
    ];
    return $methods[$method] ?? ucfirst($method);
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Pago - <?php echo htmlspecialchars($payment['id']); ?></title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        body {
            font-family: Arial, sans-serif;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 10mm;
            font-size: 10pt;
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            margin-bottom: 5mm;
        }
        .header img {
            max-width: 50mm;
            max-height: 25mm;
        }
        .header h1 {
            color: #2c3e50;
            margin: 3mm 0;
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
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5mm;
            page-break-inside: avoid;
        }
        th, td {
            padding: 2mm;
            border: 1pt solid #ddd;
            text-align: left;
            font-size: 9pt;
        }
        th {
            background-color: #f8f9fa;
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
            @page {
                size: A5;
                margin: 0;
            }
            body {
                padding: 5mm;
                font-size: 9pt;
            }
            .header {
                margin-bottom: 3mm;
            }
            .header img {
                max-width: 40mm;
                max-height: 20mm;
            }
            .header h1 {
                font-size: 12pt;
                margin: 2mm 0;
            }
            .info-section {
                margin-bottom: 3mm;
            }
            .info-section h2 {
                font-size: 10pt;
                padding-bottom: 0.5mm;
            }
            th, td {
                padding: 1.5mm;
                font-size: 8pt;
            }
            .footer {
                margin-top: 3mm;
                font-size: 7pt;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <?php if (!empty($companyInfo['logo_path'])): ?>
            <img src="<?php echo htmlspecialchars($companyInfo['logo_path']); ?>" alt="Logo de la empresa">
        <?php endif; ?>
        <h1><?php echo htmlspecialchars($companyInfo['name']); ?></h1>
        <p><?php echo htmlspecialchars($companyInfo['address']); ?></p>
        <p>Tel: <?php echo htmlspecialchars($companyInfo['phone']); ?> | Email: <?php echo htmlspecialchars($companyInfo['email']); ?></p>
        <?php if (!empty($companyInfo['website'])): ?>
            <p>Web: <?php echo htmlspecialchars($companyInfo['website']); ?></p>
        <?php endif; ?>
    </div>

    <div class="info-section">
        <h2>Recibo de Pago</h2>
        <p><strong>Número de Recibo:</strong> <?php echo htmlspecialchars($payment['id']); ?></p>
        <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($payment['payment_date'])); ?></p>
        <p><strong>Cliente:</strong> <?php echo htmlspecialchars($payment['customer_name']); ?></p>
    </div>

    <div class="info-section">
        <h2>Detalles del Pago</h2>
        <table>
            <tr>
                <th>Concepto</th>
                <td>Pago de cuota</td>
            </tr>
            <tr>
                <th>Monto Total de la Cuenta</th>
                <td>$<?php echo number_format($payment['total_amount'], 2, ',', '.'); ?></td>
            </tr>
            <tr>
                <th>Monto Pagado</th>
                <td>$<?php echo number_format($payment['amount'], 2, ',', '.'); ?></td>
            </tr>
            <tr>
                <th>Saldo Restante</th>
                <td>$<?php echo number_format($payment['balance'], 2, ',', '.'); ?></td>
            </tr>
            <tr>
                <th>Método de Pago</th>
                <td><?php echo htmlspecialchars(formatPaymentMethod($payment['payment_method'])); ?></td>
            </tr>
            <?php if (!empty($payment['notes'])): ?>
            <tr>
                <th>Notas</th>
                <td><?php echo htmlspecialchars($payment['notes']); ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>

    <div class="footer">
        <p><?php echo htmlspecialchars($companyInfo['receipt_footer'] ?? ''); ?></p>
        <p><?php echo htmlspecialchars($companyInfo['legal_info'] ?? ''); ?></p>
    </div>

    <div class="no-print">
        <button onclick="window.print()">Imprimir Recibo</button>
        <button onclick="window.close()">Cerrar</button>
    </div>

    <script>
        window.onload = function() {
            // Imprimir automáticamente al cargar la página
            window.print();
        }
    </script>
</body>
</html>