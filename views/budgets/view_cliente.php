<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/utils.php';

// Verificar si se proporcionó un ID y un token
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
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
        .content-wrapper { max-width: 800px; margin: auto; }
        .header img { max-width: 200px; max-height: 100px; }
    </style>
</head>
<body>
    <div class="content-wrapper">
        <div class="header text-center my-4">
            <?php if ($companyInfo['logo_path']): ?>
                <img src="<?php echo htmlspecialchars($companyInfo['logo_path']); ?>" alt="Logo de la empresa" class="mb-3">
            <?php endif; ?>
            <h1><?php echo htmlspecialchars($companyInfo['name']); ?></h1>
            <p><?php echo htmlspecialchars($companyInfo['address']); ?> | Tel: <?php echo htmlspecialchars($companyInfo['phone']); ?> | Email: <?php echo htmlspecialchars($companyInfo['email']); ?></p>
            <?php if ($companyInfo['website']): ?>
                <p>Web: <?php echo htmlspecialchars($companyInfo['website']); ?></p>
            <?php endif; ?>
        </div>

        <div class="info-section mb-4">
            <h2>Presupuesto #<?php echo $budget['id']; ?></h2>
            <p><strong>Fecha:</strong> <?php echo $budget['budget_date']; ?> | <strong>Validez:</strong> <?php echo $budget['validity_period']; ?> días</p>
            <p><strong>Cliente:</strong> <?php echo htmlspecialchars($budget['customer_name'] ?? 'No especificado'); ?> | <strong>Elaborado por:</strong> <?php echo htmlspecialchars($budget['user_name']); ?></p>
        </div>

        <div class="info-section mb-4">
            <table class="table table-bordered">
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
                        <td colspan="3" class="text-right"><strong>Total:</strong></td>
                        <td><strong>$<?php echo number_format($budget['total_amount'], 2); ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="info-section mb-4">
            <p><strong>Estado:</strong> <?php echo ucfirst($budget['status']); ?></p>
            <?php if (!empty($budget['notes'])): ?>
                <p><strong>Notas:</strong> <?php echo nl2br(htmlspecialchars($budget['notes'])); ?></p>
            <?php endif; ?>
        </div>

        <div class="footer text-center mt-4">
            <p><?php echo htmlspecialchars($companyInfo['receipt_footer']); ?></p>
            <p><?php echo htmlspecialchars($companyInfo['legal_info']); ?></p>
        </div>

        <div class="text-center mt-4 mb-4 no-print">
            <button class="btn btn-primary mr-2" onclick="window.print()">Imprimir Presupuesto</button>
            <a href="<?php echo url('budget.php?action=download_pdf&id=' . $budgetId); ?>" class="btn btn-secondary">Descargar PDF</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
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