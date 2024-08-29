<div class="container mt-4">
    <h1 class="mb-4">Detalles de la Visita a Domicilio</h1>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Información del Cliente</h5>
            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($visit['customer_name']); ?></p>
            <p><strong>Dirección:</strong> <?php echo htmlspecialchars($visit['address']); ?></p>
            <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($visit['phone']); ?></p>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <h5 class="card-title">Detalles de la Visita</h5>
            <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($visit['visit_date'])); ?></p>
            <p><strong>Hora:</strong> <?php echo date('H:i', strtotime($visit['visit_time'])); ?></p>
            <p><strong>Notas:</strong> <?php echo nl2br(htmlspecialchars($visit['notes'])); ?></p>
            <p><strong>Estado:</strong> 
                <span class="badge bg-<?php echo getStatusBadgeClass($visit['status']); ?>">
                    <?php echo ucfirst(htmlspecialchars($visit['status'])); ?>
                </span>
            </p>
        </div>
    </div>

    <div class="card mt-3">
    <div class="card-body">
        <h5 class="card-title">Cambiar Estado</h5>
        <form action="<?php echo url('home_visits.php?action=updateStatus&id=' . $visit['id']); ?>" method="post">
            <div class="mb-3">
                <select class="form-select" name="status" required>
                    <option value="">Seleccionar nuevo estado</option>
                    <option value="programada" <?php echo $visit['status'] == 'programada' ? 'selected' : ''; ?>>Programada</option>
                    <option value="completada" <?php echo $visit['status'] == 'completada' ? 'selected' : ''; ?>>Completada</option>
                    <option value="cancelada" <?php echo $visit['status'] == 'cancelada' ? 'selected' : ''; ?>>Cancelada</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar Estado</button>
        </form>
    </div>
</div>

    <div class="mt-3">
        <a href="<?php echo url('home_visits.php?action=edit&id=' . $visit['id']); ?>" class="btn btn-warning">Editar</a>
        <a href="<?php echo url('home_visits.php?action=delete&id=' . $visit['id']); ?>" class="btn btn-danger">Eliminar</a>
        <a href="<?php echo url('home_visits.php'); ?>" class="btn btn-secondary">Volver a la lista</a>
    </div>
</div>

<?php
function getStatusBadgeClass($status) {
    switch($status) {
        case 'programada':
            return 'warning';
        case 'completada':
            return 'success';
        case 'cancelada':
            return 'danger';
        default:
            return 'secondary';
    }
}
?>
?>