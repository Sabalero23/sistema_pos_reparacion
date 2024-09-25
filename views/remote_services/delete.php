<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/remote_service_functions.php';

// Verificar si se proporcionó un ID de servicio
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['flash_message'] = "ID de servicio no válido.";
    $_SESSION['flash_type'] = 'danger';
    header('Location: remote_services.php');
    exit();
}

$serviceId = $_GET['id'];
$service = getRemoteService($serviceId);

// Verificar si el servicio existe
if (!$service) {
    $_SESSION['flash_message'] = "Servicio no encontrado.";
    $_SESSION['flash_type'] = 'danger';
    header('Location: remote_services.php');
    exit();
}

// Procesar la eliminación del servicio
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = deleteRemoteService($serviceId);

    if ($result['success']) {
        $_SESSION['flash_message'] = "Servicio eliminado con éxito.";
        $_SESSION['flash_type'] = 'success';
    } else {
        $_SESSION['flash_message'] = "Error al eliminar el servicio: " . $result['message'];
        $_SESSION['flash_type'] = 'danger';
    }

    header('Location: remote_services.php');
    exit();
}

$pageTitle = "Eliminar Servicio Remoto";
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4">
    <h1 class="mb-4">Eliminar Servicio Remoto</h1>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Detalles del Servicio</h5>
            <p><strong>Cliente:</strong> <?php echo htmlspecialchars($service['customer_name']); ?></p>
            <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($service['service_date'])); ?></p>
            <p><strong>Hora:</strong> <?php echo date('H:i', strtotime($service['service_time'])); ?></p>
            <p><strong>Notas:</strong> <?php echo nl2br(htmlspecialchars($service['notes'])); ?></p>
        </div>
    </div>

    <div class="alert alert-warning" role="alert">
        ¿Estás seguro de que deseas eliminar este servicio remoto? Esta acción no se puede deshacer.
    </div>

    <form action="<?php echo url('remote_services.php?action=delete&id=' . $serviceId); ?>" method="post">
        <button type="submit" class="btn btn-danger">Eliminar Servicio</button>
        <a href="<?php echo url('remote_services.php'); ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>