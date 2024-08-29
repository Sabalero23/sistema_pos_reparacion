<div class="container mt-4">
    <h1 class="mb-4">Gestión de Reservas</h1>
    
    <?php if (hasPermission('reservations_create')): ?>
        <a href="<?php echo url('reservations.php?action=create'); ?>" class="btn btn-primary mb-3">
            <i class="fas fa-plus"></i> Nueva Reserva
        </a>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped table-hover" id="reservationsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $reservation): ?>
                    <tr>
                        <td><?php echo $reservation['id']; ?></td>
                        <td><?php echo $reservation['reservation_date']; ?></td>
                        <td><?php echo htmlspecialchars($reservation['customer_name'] ?? 'N/A'); ?></td>
                        <td><?php echo number_format($reservation['total_amount'], 2); ?></td>
                        <td><?php echo ucfirst($reservation['status']); ?></td>
                        <td>
                            <a href="<?php echo url('reservations.php?action=view&id=' . $reservation['id']); ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                            <?php if ($reservation['status'] === 'pendiente' && hasPermission('reservations_confirm')): ?>
                                <button class="btn btn-sm btn-success confirm-reservation" data-id="<?php echo $reservation['id']; ?>">
                                    <i class="fas fa-check"></i> Confirmar
                                </button>
                            <?php endif; ?>
                            <?php if ($reservation['status'] === 'confirmado' && hasPermission('reservations_convert')): ?>
                                <a href="<?php echo url('reservations.php?action=convert&id=' . $reservation['id']); ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-exchange-alt"></i> Convertir a Venta
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="<?php echo url('js/reservations.js'); ?>"></script>