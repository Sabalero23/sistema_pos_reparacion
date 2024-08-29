<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="mb-2">Reporte de Movimientos de Caja</h1>
            <p class="mb-0">Periodo: <?php echo htmlspecialchars($startDate); ?> - <?php echo htmlspecialchars($endDate); ?></p>
        </div>
        <a href="<?php echo url('reports.php'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver a Reportes
        </a>
    </div>

    <?php foreach ($reportData as $index => $sessionReport): ?>
        <div class="card mb-4 session-report" id="session-<?php echo $index; ?>">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Sesión de Caja #<?php echo $sessionReport['session']['id']; ?></h5>
                <button class="btn btn-primary btn-sm print-session" data-session="<?php echo $index; ?>">
                    <i class="fas fa-print"></i> Imprimir esta sesión
                </button>
            </div>
            <div class="card-body">
                <p>Apertura: <?php echo $sessionReport['session']['opening_date']; ?> - Cierre: <?php echo $sessionReport['session']['closing_date'] ?? 'En curso'; ?></p>
                <p>Saldo inicial: $<?php echo number_format($sessionReport['session']['opening_balance'], 2); ?> ARS</p>
                
                <h6>Movimientos:</h6>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Monto</th>
                            <th>Notas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sessionReport['movements'] as $movement): ?>
                            <tr>
                                <td><?php echo $movement['created_at']; ?></td>
                                <td><?php echo $movement['movement_type']; ?></td>
                                <td>$<?php echo number_format($movement['amount'], 2); ?> ARS</td>
                                <td><?php echo $movement['notes']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <h6>Resumen de Movimientos:</h6>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tipo de Movimiento</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Ventas</td>
                            <td>$<?php echo number_format($sessionReport['totals']['sales'], 2); ?> ARS</td>
                        </tr>
                        <tr>
                            <td>Compras</td>
                            <td>$<?php echo number_format($sessionReport['totals']['purchases'], 2); ?> ARS</td>
                        </tr>
                        <tr>
                            <td>Ingresos de Efectivo</td>
                            <td>$<?php echo number_format($sessionReport['totals']['cash_in'], 2); ?> ARS</td>
                        </tr>
                        <tr>
                            <td>Retiros de Efectivo</td>
                            <td>$<?php echo number_format($sessionReport['totals']['cash_out'], 2); ?> ARS</td>
                        </tr>
                    </tbody>
                </table>

                <?php if ($sessionReport['session']['closing_date']): ?>
                    <p>Saldo final: $<?php echo number_format($sessionReport['session']['closing_balance'], 2); ?> ARS</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const printButtons = document.querySelectorAll('.print-session');
    printButtons.forEach(button => {
        button.addEventListener('click', function() {
            const sessionId = this.getAttribute('data-session');
            const sessionElement = document.getElementById('session-' + sessionId);
            const printContent = sessionElement.innerHTML;
            const originalContent = document.body.innerHTML;

            document.body.innerHTML = `
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            ${printContent}
                        </div>
                    </div>
                </div>
            `;

            window.print();
            document.body.innerHTML = originalContent;
            location.reload();
        });
    });
});
</script>