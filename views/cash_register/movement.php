<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container mt-4">
    <h1><?php echo $pageTitle; ?></h1>
    
    <form action="<?php echo url('cash_register.php?action=movement'); ?>" method="post">
        <div class="mb-3">
            <label for="movement_type" class="form-label">Tipo de Movimiento</label>
            <select class="form-select" id="movement_type" name="movement_type" required>
                <option value="cash_in">Ingreso de Efectivo</option>
                <option value="cash_out">Retiro de Efectivo</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="amount" class="form-label">Monto</label>
            <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
        </div>
        <div class="mb-3">
            <label for="notes" class="form-label">Notas</label>
            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Registrar Movimiento</button>
        <a href="<?php echo url('cash_register.php'); ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>