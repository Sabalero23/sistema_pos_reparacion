<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/roles.php';
require_once __DIR__ . '/../../includes/utils.php';


if (!isLoggedIn() || !hasPermission('budget_view')) {
    setFlashMessage("No tienes permiso para acceder a esta página.", 'warning');
    redirect('index.php');
}

$budgetId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if (!$budgetId) {
    die("ID de presupuesto no proporcionado");
}

$budget = getBudgetById($budgetId);

if (!$budget) {
    die("Presupuesto no encontrado");
}

$companyInfo = getCompanyInfo();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presupuesto - <?php echo $budget['id']; ?></title>
    <style>
        @page { size: A4; margin: 0; }
        body { font-family: Arial, sans-serif; line-height: 1.3; color: #333; margin: 0; padding: 10mm; font-size: 9pt; }
        .header { text-align: center; margin-bottom: 5mm; }
        .header img { max-width: 40mm; max-height: 15mm; }
        .header h1 { color: #2c3e50; margin: 2mm 0; font-size: 12pt; }
        .info-section { margin-bottom: 3mm; }
        .info-section h2 { color: #2c3e50; border-bottom: 1px solid #eee; padding-bottom: 1mm; font-size: 11pt; margin: 2mm 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 3mm; }
        th, td { padding: 1.5mm; border: 0.5pt solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .total { font-weight: bold; text-align: right; }
        .footer { text-align: center; margin-top: 5mm; font-size: 7pt; color: #777; }
        @media print { body { width: 210mm; height: 297mm; } }
    </style>
</head>
<body>
    <div class="header">
        <?php if ($companyInfo['logo_path']): ?>
            <img src="<?php echo htmlspecialchars($companyInfo['logo_path']); ?>" alt="Logo de la empresa">
        <?php endif; ?>
        <h1><?php echo htmlspecialchars($companyInfo['name']); ?></h1>
        <p><?php echo htmlspecialchars($companyInfo['address']); ?> | Tel: <?php echo htmlspecialchars($companyInfo['phone']); ?> | Email: <?php echo htmlspecialchars($companyInfo['email']); ?></p>
        <?php if ($companyInfo['website']): ?>
            <p>Web: <?php echo htmlspecialchars($companyInfo['website']); ?></p>
        <?php endif; ?>
    </div>

    <div class="info-section">
        <h2>Presupuesto #<?php echo $budget['id']; ?></h2>
        <p><strong>Fecha:</strong> <?php echo $budget['budget_date']; ?> | <strong>Validez:</strong> <?php echo $budget['validity_period']; ?> días</p>
        <p><strong>Cliente:</strong> <?php echo htmlspecialchars($budget['customer_name'] ?? 'No especificado'); ?> | <strong>Elaborado por:</strong> <?php echo htmlspecialchars($budget['user_name']); ?></p>
    </div>

    <div class="info-section">
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
                <?php foreach ($budget['items'] as $item): ?>
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
                    <td class="total">$<?php echo number_format($budget['total_amount'], 2); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="info-section">
        <p><strong>Estado:</strong> <?php echo ucfirst($budget['status']); ?></p>
        <?php if (!empty($budget['notes'])): ?>
            <p><strong>Notas:</strong> <?php echo nl2br(htmlspecialchars($budget['notes'])); ?></p>
        <?php endif; ?>
    </div>

    <div class="footer">
        <p><?php echo htmlspecialchars($companyInfo['receipt_footer']); ?></p>
        <p><?php echo htmlspecialchars($companyInfo['legal_info']); ?></p>
    </div>

    <script>
        window.onload = function() { window.print(); }
    </script>
</body>
</html>
<?php
function getBudgetById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT b.*, c.name as customer_name, u.name as user_name 
                           FROM budgets b 
                           LEFT JOIN customers c ON b.customer_id = c.id 
                           LEFT JOIN users u ON b.user_id = u.id 
                           WHERE b.id = :id");
    $stmt->execute([':id' => $id]);
    $budget = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($budget) {
        $stmt = $pdo->prepare("SELECT bi.*, p.name as product_name 
                               FROM budget_items bi 
                               JOIN products p ON bi.product_id = p.id 
                               WHERE bi.budget_id = :budget_id");
        $stmt->execute([':budget_id' => $id]);
        $budget['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return $budget;
}
?>