<?php
$pageTitle = "Eliminar Servicio Remoto";
include __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4">
    <h1>Eliminar Servicio Remoto</h1>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Detalles del Servicio Remoto</h5>
            <p><strong>Cliente:</strong> <?php echo htmlspecialchars($service['customer_name']); ?></p>
            <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($service['service_date'])); ?></p>
            <p><strong>Hora:</strong> <?php echo date('H:i', strtotime($service['service_time'])); ?></p>
            <p><strong>Estado:</strong> <?php echo ucfirst($service['status']); ?></p>
            <p><strong>Notas:</strong> <?php echo nl2br(htmlspecialchars($service['notes'])); ?></p>
        </div>
    </div>

    <div class="alert alert-danger" role="alert">
        ¿Estás seguro de que deseas eliminar este servicio remoto? Esta acción no se puede deshacer.
    </div>

    <form action="<?php echo url('remote_services.php?action=delete&id=' . $service['id']); ?>" method="post">
        <button type="submit" class="btn btn-danger">Eliminar Servicio Remoto</button>
        <a href="<?php echo url('remote_services.php'); ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>