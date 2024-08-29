<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/service_functions.php';
require_once __DIR__ . '/../../includes/phpqrcode/qrlib.php';

$order_id = $_GET['id'] ?? null;

if (!$order_id) {
    die("ID de orden no proporcionado.");
}

$order = getServiceOrder($order_id);
$order_parts = getOrderParts($order_id);

if (!$order) {
    die("Orden no encontrada.");
}

$company_info = getCompanyInfo();
$logo_path = $company_info['logo_path'];

// Verificar si la función generateQRCode ya existe
if (!function_exists('generateQRCode')) {
    function generateQRCode($order_number) {
        $url = url("seguimiento.php?order_number=" . urlencode($order_number));
        $tempDir = __DIR__ . '/../../includes/qr/';
        $fileName = 'qr_' . $order_number . '.png';
        $filePath = $tempDir . $fileName;
        
        if (!file_exists($filePath)) {
            QRcode::png($url, $filePath, QR_ECLEVEL_L, 3, 2);
        }
        
        $imageData = base64_encode(file_get_contents($filePath));
        return 'data:image/png;base64,' . $imageData;
    }
}

$qr_code = generateQRCode($order['order_number']);

// Calcular el costo total de las piezas
$total_parts_cost = array_sum(array_map(function($part) {
    return $part['cost'] * $part['quantity'];
}, $order_parts));

// Recalcular el saldo
$new_balance = $order['total_amount'] + $total_parts_cost - $order['prepaid_amount'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden de Servicio #<?php echo htmlspecialchars($order['order_number'] ?? ''); ?></title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            margin: 0;
            padding: 10mm;
            box-sizing: border-box;
        }
        .container {
            width: 100%;
            max-width: 190mm;
            margin: 0 auto;
        }
        h1 {
            font-size: 16pt;
            margin-bottom: 5mm;
        }
        h2 {
            font-size: 12pt;
            margin-top: 5mm;
            margin-bottom: 3mm;
        }
        p {
            margin: 1mm 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5mm;
        }
        th, td {
            border: 0.5pt solid #ddd;
            padding: 2mm;
            text-align: left;
            font-size: 9pt;
        }
        th {
            background-color: #f2f2f2;
        }
        .columns-container {
            display: flex;
            justify-content: space-between;
            flex-wrap: nowrap;
        }
        .column {
            width: 31%;
            padding-right: 1%;
        }
        .text-center {
            text-align: center;
        }
        .qr-code {
            max-width: 100%;
            height: auto;
        }
        .qr-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        .signature-line {
            border-top: 1pt solid #000;
            width: 70mm;
            margin-top: 10mm;
            margin-bottom: 2mm;
        }
        .signature-label {
            font-size: 8pt;
        }
        @media print {
            body {
                width: 210mm;
                height: 297mm;
            }
            .no-print {
                display: none;
            }
        }
        @media screen and (max-width: 210mm) {
            .columns-container {
                flex-wrap: wrap;
            }
            .column {
                width: 100%;
                margin-bottom: 5mm;
            }
        }
        .company-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }
        .company-logo {
            flex: 1;
            padding-right: 20px;
        }
        .company-logo img {
            max-width: 100%;
            height: auto;
        }
        .company-info {
            flex: 2;
            text-align: left;
        }
        .company-info h2 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 1.5em;
        }
        .company-info p {
            margin: 5px 0;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="company-header">
            <div class="company-logo">
                <img src="<?php echo htmlspecialchars($logo_path); ?>" alt="Logo de <?php echo htmlspecialchars($company_info['name']); ?>">
            </div>
            <div class="company-info">
                <h2><?php echo htmlspecialchars($company_info['name']); ?></h2>
                <p><?php echo htmlspecialchars($company_info['address']); ?></p>
                <p>Tel: <?php echo htmlspecialchars($company_info['phone']); ?></p>
                <p>Email: <?php echo htmlspecialchars($company_info['email']); ?></p>
                <p>Web: <?php echo htmlspecialchars($company_info['website']); ?></p>
                <p><?php echo htmlspecialchars($company_info['legal_info']); ?></p>
            </div>
        </div>

        <h1>Orden de Servicio #<?php echo htmlspecialchars($order['order_number']); ?></h1>
        
        <div class="columns-container">
            <div class="column">
                <h2>Detalles del Cliente</h2>
                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email'] ?? 'No especificado'); ?></p>
                <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($order['phone'] ?? 'No especificado'); ?></p>
                <p><strong>Dirección:</strong> <?php echo htmlspecialchars($order['address'] ?? 'No especificada'); ?></p>
            </div>

            <div class="column">
                <h2>Detalles del Dispositivo</h2>
                <p><strong>Marca:</strong> <?php echo htmlspecialchars($order['brand']); ?></p>
                <p><strong>Modelo:</strong> <?php echo htmlspecialchars($order['model']); ?></p>
                <p><strong>Número de Serie:</strong> <?php echo htmlspecialchars($order['serial_number']); ?></p>
                <p><strong>Estado Actual:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
            </div>

            <div class="column text-center">
                <div class="qr-container">
                    <h2>Seguimiento de Orden</h2>
                    <img src="<?php echo $qr_code; ?>" alt="Código QR de seguimiento" class="qr-code">
                    <p>Escanea para seguimiento</p>
                </div>
            </div>
        </div>

        <h2>Servicios</h2>
        <table>
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Costo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order['items'] as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['description']); ?></td>
                    <td><?php echo number_format($item['cost'], 2); ?> ARS</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Piezas Utilizadas</h2>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Número de Parte</th>
                    <th>Cantidad</th>
                    <th>Costo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_parts as $part): ?>
                <tr>
                    <td><?php echo htmlspecialchars($part['part_name']); ?></td>
                    <td><?php echo htmlspecialchars($part['part_number']); ?></td>
                    <td><?php echo htmlspecialchars($part['quantity']); ?></td>
                    <td><?php echo number_format($part['cost'], 2); ?> ARS</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Resumen Financiero</h2>
        <table>
            <tr>
                <td><strong>Total:</strong></td>
                <td><?php echo number_format($order['total_amount'], 2); ?> ARS</td>
            </tr>
            <tr>
                <td><strong>Monto Prepago:</strong></td>
                <td><?php echo number_format($order['prepaid_amount'], 2); ?> ARS</td>
            </tr>
            <tr>
                <td><strong>Piezas Utilizadas:</strong></td>
                <td><?php echo number_format($total_parts_cost, 2); ?> ARS</td>
            </tr>
            <tr>
                <td><strong>Saldo:</strong></td>
                <td><?php echo number_format($new_balance, 2); ?> ARS</td>
            </tr>
        </table>

        <h2>Términos y Condiciones</h2>
        <p><?php echo nl2br(htmlspecialchars(getActiveTermsAndConditions())); ?></p>

        <!-- Sección de firma del cliente -->
        <div class="signature-section">
            <p><strong>Al firmar, acepto los términos y condiciones mencionados anteriormente.</strong></p>
            <div class="signature-line"></div>
            
            <p class="signature-label">Firma del Cliente: <?php echo htmlspecialchars($order['customer_name'] ?? ''); ?></p>
            <br>
           
        </div>
        
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }

        window.onafterprint = function() {
            window.history.back();
        }
    </script>
</body>
</html>