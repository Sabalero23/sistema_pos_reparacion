<div class="container mt-4">
    <h1 class="mb-4">Detalles de la Reserva</h1>

    <div class="card mb-4">
        <div class="card-header">
            Información General
        </div>
        <div class="card-body">
            <p><strong>ID de Reserva:</strong> <?php echo $reservation['id']; ?></p>
            <p><strong>Fecha:</strong> <?php echo $reservation['reservation_date']; ?></p>
            <p><strong>Cliente:</strong> <?php echo htmlspecialchars($reservation['customer_name'] ?? 'N/A'); ?></p>
            <p><strong>Creado por:</strong> <?php echo htmlspecialchars($reservation['user_name']); ?></p>
            <p><strong>Estado:</strong> <?php echo ucfirst($reservation['status']); ?></p>
            <p><strong>Notas:</strong> <?php echo nl2br(htmlspecialchars($reservation['notes'] ?? '')); ?></p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            Productos Reservados
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservationItems as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td><?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo number_format($item['quantity'] * $item['price'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Total:</th>
                        <th><?php echo number_format($reservation['total_amount'], 2); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <?php if ($reservation['status'] === 'pendiente' && hasPermission('reservations_confirm')): ?>
        <button id="confirmReservation" class="btn btn-success" data-id="<?php echo $reservation['id']; ?>">Confirmar Reserva</button>
    <?php endif; ?>

    <?php if ($reservation['status'] === 'confirmado' && hasPermission('reservations_convert')): ?>
        <a href="<?php echo url('reservations.php?action=convert&id=' . $reservation['id']); ?>" class="btn btn-warning">Convertir a Venta</a>
    <?php endif; ?>

    <a href="<?php echo url('reservations.php'); ?>" class="btn btn-secondary">Volver a la Lista</a>
</div>

<script src="<?php echo url('js/reservations.js'); ?>"></script>