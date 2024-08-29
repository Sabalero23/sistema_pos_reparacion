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
        <h2>Comprobante de Venta</h2>
        <p><strong>Número de Venta:</strong> <?php echo $sale['id']; ?> | <strong>Fecha:</strong> <?php echo $sale['sale_date']; ?></p>
        <p><strong>Cliente:</strong> <?php echo htmlspecialchars($sale['customer_name'] ?? 'Consumidor Final'); ?> | <strong>Atendido por:</strong> <?php echo htmlspecialchars($sale['user_name']); ?></p>
    </div>

    <div class="info-section">
        <h2>Detalle de Productos</h2>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($saleItems as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td>$<?php echo number_format($item['quantity'] * $item['price'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="total">Total:</td>
                    <td class="total">$<?php echo number_format($sale['total_amount'], 2); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="info-section">
        <p><strong>Método de Pago:</strong> <?php echo ucfirst($sale['payment_method']); ?> | <strong>Estado:</strong> <?php echo ucfirst($sale['status']); ?></p>
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