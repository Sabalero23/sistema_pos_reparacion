<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/roles.php';
require_once __DIR__ . '/../../includes/remote_service_functions.php';

if (!isLoggedIn() || !hasPermission('remote_services_view')) {
    $_SESSION['flash_message'] = "No tienes permiso para ver detalles de servicios remotos.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('index.php'));
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    $_SESSION['flash_message'] = "ID de servicio remoto no válido.";
    $_SESSION['flash_type'] = 'danger';
    header('Location: ' . url('remote_services.php'));
    exit();
}

$service = getRemoteService($id);
if (!$service) {
    $_SESSION['flash_message'] = "Servicio remoto no encontrado.";
    $_SESSION['flash_type'] = 'danger';
    header('Location: ' . url('remote_services.php'));
    exit();
}

$pageTitle = "Detalles del Servicio Remoto";
include __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4">
    <h1>Detalles del Servicio Remoto</h1>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Servicio Remoto #<?php echo htmlspecialchars($service['id']); ?></h5>
            <p><strong>Cliente:</strong> <?php echo htmlspecialchars($service['customer_name']); ?></p>
            <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($service['service_date'])); ?></p>
            <p><strong>Hora:</strong> <?php echo date('H:i', strtotime($service['service_time'])); ?></p>
            <p><strong>Estado:</strong> <?php echo ucfirst($service['status']); ?></p>
            <p><strong>Notas:</strong> <?php echo nl2br(htmlspecialchars($service['notes'])); ?></p>
        </div>
    </div>

    <div class="mt-3">
        <a href="<?php echo url('remote_services.php'); ?>" class="btn btn-secondary">Volver a la lista</a>
        <?php if (hasPermission('remote_services_edit')): ?>
            <a href="<?php echo url('remote_services.php?action=edit&id=' . $service['id']); ?>" class="btn btn-warning">Editar</a>
        <?php endif; ?>
        <?php if (hasPermission('remote_services_delete')): ?>
            <a href="<?php echo url('remote_services.php?action=delete&id=' . $service['id']); ?>" class="btn btn-danger">Eliminar</a>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>