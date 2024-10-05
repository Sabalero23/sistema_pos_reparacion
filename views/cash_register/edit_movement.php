<?php
require_once __DIR__ . '/../../includes/header.php';
?>

<style>
    .container {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h1 {
        color: #333;
        text-align: center;
    }
    form {
        display: flex;
        flex-direction: column;
    }
    .form-group {
        margin-bottom: 15px;
    }
    label {
        font-weight: bold;
        margin-bottom: 5px;
        display: block;
    }
    select, input[type="number"], textarea {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
    }
    button {
        background-color: #4CAF50;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
    }
    button:hover {
        background-color: #45a049;
    }
    .back-link {
        display: inline-block;
        margin-top: 20px;
        color: #666;
        text-decoration: none;
    }
    .back-link:hover {
        text-decoration: underline;
    }
</style>

<div class="container mt-4">
    <h1><?php echo $pageTitle; ?></h1>

    <form action="" method="POST">
        <div class="form-group">
            <label for="movement_type">Tipo de Movimiento:</label>
            <select id="movement_type" name="movement_type" class="form-control" required>
                <option value="sale" <?php echo $movement['movement_type'] == 'sale' ? 'selected' : ''; ?>>Venta</option>
                <option value="purchase" <?php echo $movement['movement_type'] == 'purchase' ? 'selected' : ''; ?>>Compra</option>
                <option value="cash_in" <?php echo $movement['movement_type'] == 'cash_in' ? 'selected' : ''; ?>>Ingreso de Efectivo</option>
                <option value="cash_out" <?php echo $movement['movement_type'] == 'cash_out' ? 'selected' : ''; ?>>Salida de Efectivo</option>
            </select>
        </div>
        <div class="form-group">
            <label for="amount">Monto:</label>
            <input type="number" id="amount" name="amount" class="form-control" step="0.01" value="<?php echo htmlspecialchars($movement['amount']); ?>" required>
        </div>
        <div class="form-group">
            <label for="notes">Notas:</label>
            <textarea id="notes" name="notes" class="form-control" rows="3"><?php echo htmlspecialchars($movement['notes']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Movimiento</button>
    </form>

    <a href="<?php echo url('cash_register.php'); ?>" class="btn btn-secondary mt-3">Volver</a>
</div>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>