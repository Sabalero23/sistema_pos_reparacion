<?php include __DIR__ . '/../../includes/header.php'; ?>

<style>
    .container {
        max-width: 900px;
        margin: 0 auto;
        padding: 20px;
    }
    h1, h2 {
        color: #333;
        text-align: center;
    }
    .summary {
        background-color: #f0f0f0;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .summary p {
        margin: 5px 0;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: left;
    }
    th {
        background-color: #4CAF50;
        color: white;
    }
    tr:nth-child(even) {
        background-color: #f2f2f2;
    }
    .action-link {
        color: #1a73e8;
        text-decoration: none;
    }
    .action-link:hover {
        text-decoration: underline;
    }
    .date-form {
        margin-bottom: 20px;
        text-align: center;
    }
    .date-form input[type="date"] {
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    .date-form button {
        background-color: #4CAF50;
        color: white;
        padding: 8px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    .date-form button:hover {
        background-color: #45a049;
    }
</style>

<div class="container">
    <h1><?php echo $pageTitle; ?></h1>

    <form action="" method="GET" class="date-form">
        <label for="date">Seleccionar fecha:</label>
        <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>">
        <button type="submit">Ver movimientos</button>
    </form>

    <div class="summary">
        <h2>Resumen del día <?php echo htmlspecialchars($date); ?></h2>
        <p>Total Ingresos: $<?php echo number_format($summary['total_income'], 2); ?></p>
        <p>Total Egresos: $<?php echo number_format($summary['total_expense'], 2); ?></p>
        <p>Balance: $<?php echo number_format($summary['balance'], 2); ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Hora</th>
                <th>Tipo</th>
                <th>Monto</th>
                <th>Notas</th>
                <th>Usuario</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($movements as $movement): ?>
                <tr>
                    <td><?php echo htmlspecialchars(date('H:i', strtotime($movement['created_at']))); ?></td>
                    <td><?php echo htmlspecialchars($movement['movement_type']); ?></td>
                    <td>$<?php echo number_format($movement['amount'], 2); ?></td>
                    <td><?php echo htmlspecialchars($movement['notes']); ?></td>
                    <td><?php echo htmlspecialchars($movement['user_name']); ?></td>
                    <td>
                        <a href="<?php echo url('cash_register_daily.php?action=edit&id=' . $movement['id'] . '&date=' . $date); ?>" class="action-link">Editar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>