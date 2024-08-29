<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container mt-4">
    <h1><?php echo $pageTitle; ?></h1>
    
    <form action="<?php echo url('cash_register.php?action=open'); ?>" method="post">
        <div class="mb-3">
            <label for="opening_balance" class="form-label">Saldo Inicial</label>
            <input type="number" step="0.01" class="form-control" id="opening_balance" name="opening_balance" required>
        </div>
        <div class="mb-3">
            <label for="notes" class="form-label">Notas</label>
            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Abrir Caja</button>
        <a href="<?php echo url('cash_register.php'); ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>