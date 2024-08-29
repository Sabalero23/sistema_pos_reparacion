<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container mt-4">
    <h1><?php echo $pageTitle; ?></h1>
    
    <?php if ($currentSession): ?>
        <div class="alert alert-info">
            Caja abierta desde: <?php echo $currentSession['opening_date']; ?><br>
            Saldo inicial: <?php echo number_format($currentSession['opening_balance'], 2); ?> <?php echo CURRENCY; ?>
        </div>
        
        <h2>Movimientos</h2>
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
                <?php foreach ($movements as $movement): ?>
                    <tr>
                        <td><?php echo $movement['created_at']; ?></td>
                        <td><?php echo ucfirst($movement['movement_type']); ?></td>
                        <td><?php echo number_format($movement['amount'], 2); ?> <?php echo CURRENCY; ?></td>
                        <td><?php echo $movement['notes']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <a href="<?php echo url('cash_register.php?action=close'); ?>" class="btn btn-warning">Cerrar Caja</a>
    <?php else: ?>
        <div class="alert alert-warning">No hay una sesión de caja abierta.</div>
        <a href="<?php echo url('cash_register.php?action=open'); ?>" class="btn btn-primary">Abrir Caja</a>
    <?php endif; ?>
    
    <a href="<?php echo url('cash_register.php?action=movement'); ?>" class="btn btn-secondary">Registrar Movimiento</a>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>