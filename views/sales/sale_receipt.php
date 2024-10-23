<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/roles.php';
require_once __DIR__ . '/../../includes/utils.php';

if (!isLoggedIn() || !hasPermission('sales_view')) {
    $_SESSION['flash_message'] = "No tienes permiso para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('index.php'));
    exit;
}

$saleId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if (!$saleId) {
    die("ID de venta no proporcionado");
}

$sale = getSaleById($saleId);
$saleItems = getSaleItems($saleId);

if (!$sale) {
    die("Venta no encontrada");
}

$companyInfo = getCompanyInfo();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Venta - <?php echo $sale['id']; ?></title>
    <style>
        @page {
            size: 148mm 210mm; /* Tamaño A5 exacto */
            margin: 0;
        }
        body {
            font-family: Arial, sans-serif;
            line-height: 1.3;
            color: #333;
            margin: 0;
            padding: 8mm;
            font-size: 10pt;
            background: white;
            box-sizing: border-box;
            width: 148mm; /* Ancho A5 */
            height: 210mm; /* Alto A5 */
        }
        .header {
            text-align: center;
            margin-bottom: 5mm;
        }
        .header img {
            max-width: 40mm;
            max-height: 15mm;
            margin-bottom: 2mm;
        }
        .header h1 {
            color: #2c3e50;
            margin: 2mm 0;
            font-size: 14pt;
            font-weight: bold;
        }
        .header p {
            margin: 1mm 0;
            font-size: 9pt;
        }
        .info-section {
            margin-bottom: 5mm;
        }
        .info-section h2 {
            color: #2c3e50;
            border-bottom: 1px solid #2c3e50;
            padding-bottom: 1mm;
            font-size: 11pt;
            margin: 2mm 0;
            font-weight: bold;
        }
        .info-section p {
            margin: 1mm 0;
            font-size: 9pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 3mm 0;
        }
        th, td {
            padding: 2mm;
            border: 0.5pt solid #2c3e50;
            text-align: left;
            font-size: 9pt;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #2c3e50;
        }
        .total {
            font-weight: bold;
            text-align: right;
            font-size: 10pt;
        }
        .footer {
            text-align: center;
            margin-top: 5mm;
            padding-top: 3mm;
            border-top: 1px solid #eee;
            font-size: 8pt;
            color: #555;
        }
        .sale-details {
            display: flex;
            justify-content: space-between;
            margin: 2mm 0;
            font-size: 9pt;
        }
        .sale-details span {
            margin-right: 3mm;
        }
        .payment-info {
            background-color: #f8f9fa;
            padding: 2mm;
            border-radius: 1mm;
            margin: 2mm 0;
            border: 0.5pt solid #2c3e50;
            font-size: 9pt;
        }
        .payment-info p {
            margin: 1mm 0;
            font-size: 9pt;
        }
        .total-row {
            background-color: #f8f9fa;
            font-size: 10pt;
        }
        @media print {
            html, body {
                width: 148mm;
                height: 210mm;
                margin: 0;
                padding: 8mm;
            }
            /* Evitar saltos de página */
            table { page-break-inside: avoid; }
            .info-section { page-break-inside: avoid; }
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
        <p>Tel: <?php echo htmlspecialchars($companyInfo['phone']); ?></p>
        <p>Email: <?php echo htmlspecialchars($companyInfo['email']); ?></p>
        <?php if ($companyInfo['website']): ?>
            <p>Web: <?php echo htmlspecialchars($companyInfo['website']); ?></p>
        <?php endif; ?>
    </div>

    <div class="info-section">
        <h2>Comprobante de Venta</h2>
        <div class="sale-details">
            <span><b>Nº: <?php echo $sale['id']; ?></b></span>
            <span><b>Fecha: <?php echo date('d/m/Y', strtotime($sale['sale_date'])); ?></b></span>
        </div>
        <div class="sale-details">
            <span><b>Cliente:</b> <?php echo htmlspecialchars($sale['customer_name'] ?? 'Consumidor Final'); ?></span>
            <span><b>Vendedor:</b> <?php echo htmlspecialchars($sale['user_name']); ?></span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cant.</th>
                <th>Precio</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($saleItems as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>$<?php echo number_format($item['price'], 2, ',', '.'); ?></td>
                    <td>$<?php echo number_format($item['quantity'] * $item['price'], 2, ',', '.'); ?></td>
                </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td colspan="3" class="total">Total:</td>
                <td class="total">$<?php echo number_format($sale['total_amount'], 2, ',', '.'); ?></td>
            </tr>
        </tbody>
    </table>

    <div class="payment-info">
        <p><b>Método de Pago:</b> <?php echo ucfirst($sale['payment_method']); ?></p>
        <p><b>Estado:</b> <?php echo ucfirst($sale['status']); ?></p>
    </div>

    <div class="footer">
        <p><?php echo htmlspecialchars($companyInfo['receipt_footer']); ?></p>
        <p><?php echo htmlspecialchars($companyInfo['legal_info']); ?></p>
        <p>¡Gracias por su compra!</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
<?php

function getSaleById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT s.*, c.name as customer_name, u.name as user_name 
                           FROM sales s 
                           LEFT JOIN customers c ON s.customer_id = c.id 
                           LEFT JOIN users u ON s.user_id = u.id 
                           WHERE s.id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getSaleItems($saleId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT si.*, p.name as product_name 
                           FROM sale_items si 
                           JOIN products p ON si.product_id = p.id 
                           WHERE si.sale_id = :sale_id");
    $stmt->execute([':sale_id' => $saleId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>