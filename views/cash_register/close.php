<?php 
include __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/cash_register_functions.php';

$currentSession = getCurrentCashRegisterSession();
if (!$currentSession) {
    echo '<div class="container mt-4">';
    echo '<div class="alert alert-warning">No hay una sesión de caja abierta para cerrar.</div>';
    echo '<a href="' . url('cash_register.php') . '" class="btn btn-primary">Volver al Estado de Caja</a>';
    echo '</div>';
    include __DIR__ . '/../../includes/footer.php';
    exit;
}

$movements = getCashRegisterMovements($currentSession['id']);
$purchases = getPurchasesForSession($currentSession['id']);

$totals = [
    'sale' => 0,
    'purchase' => 0,
    'cash_in' => 0,
    'cash_out' => 0
];

foreach ($movements as $movement) {
    if ($movement['movement_type'] == 'sale') {
        $totals['sale'] += $movement['amount'];
    } elseif ($movement['movement_type'] == 'cash_in') {
        $totals['cash_in'] += $movement['amount'];
    } elseif ($movement['movement_type'] == 'cash_out') {
        $totals['cash_out'] += $movement['amount'];
    }
}

foreach ($purchases as $purchase) {
    $totals['purchase'] += $purchase['total_amount'];
}

$expectedBalance = $currentSession['opening_balance'] + $totals['sale'] + $totals['cash_in'] - $totals['purchase'] - $totals['cash_out'];
?>

<div class="container mt-4">
    <h1><?php echo $pageTitle; ?></h1>
    
    <div class="alert alert-info">
        Caja abierta desde: <?php echo $currentSession['opening_date']; ?><br>
        Saldo inicial: <?php echo number_format($currentSession['opening_balance'], 2); ?> <?php echo CURRENCY; ?>
    </div>
    
    <h2>Resumen de Movimientos</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Tipo de Movimiento</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Ventas</td>
                <td><?php echo number_format($totals['sale'], 2); ?> <?php echo CURRENCY; ?></td>
            </tr>
            <tr>
                <td>Compras</td>
                <td><?php echo number_format($totals['purchase'], 2); ?> <?php echo CURRENCY; ?></td>
            </tr>
            <tr>
                <td>Ingresos de Efectivo</td>
                <td><?php echo number_format($totals['cash_in'], 2); ?> <?php echo CURRENCY; ?></td>
            </tr>
            <tr>
                <td>Retiros de Efectivo</td>
                <td><?php echo number_format($totals['cash_out'], 2); ?> <?php echo CURRENCY; ?></td>
            </tr>
        </tbody>
    </table>
    
    <div class="alert alert-info">
        Saldo Esperado: <?php echo number_format($expectedBalance, 2); ?> <?php echo CURRENCY; ?>
    </div>
    
    <form action="<?php echo url('cash_register.php?action=close'); ?>" method="post">
        <div class="mb-3">
            <label for="closing_balance" class="form-label">Saldo Final</label>
            <input type="number" step="0.01" class="form-control" id="closing_balance" name="closing_balance" required value="<?php echo $expectedBalance; ?>">
        </div>
        <div class="mb-3">
            <label for="notes" class="form-label">Notas</label>
            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Cerrar Caja</button>
        <a href="<?php echo url('cash_register.php'); ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>