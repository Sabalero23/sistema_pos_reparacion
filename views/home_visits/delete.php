<div class="container mt-4">
    <h1 class="mb-4">Eliminar Visita a Domicilio</h1>

    <?php if (!$visit): ?>
    <div class="alert alert-danger" role="alert">
        Visita a domicilio no encontrada.
    </div>
<?php else: ?>
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Detalles de la Visita</h5>
            <p><strong>Cliente:</strong> <?php echo htmlspecialchars($visit['customer_name']); ?></p>
            <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($visit['visit_date'])); ?></p>
            <p><strong>Hora:</strong> <?php echo date('H:i', strtotime($visit['visit_time'])); ?></p>
            <p><strong>Notas:</strong> <?php echo nl2br(htmlspecialchars($visit['notes'])); ?></p>
        </div>
    </div>

        <div class="alert alert-warning" role="alert">
            ¿Estás seguro de que deseas eliminar esta visita a domicilio? Esta acción no se puede deshacer.
        </div>

        <form action="<?php echo url('home_visits.php?action=delete&id=' . $visitId); ?>" method="post">
            <button type="submit" class="btn btn-danger">Eliminar Visita</button>
            <a href="<?php echo url('home_visits.php'); ?>" class="btn btn-secondary">Cancelar</a>
        </form>
    <?php endif; ?>
</div>