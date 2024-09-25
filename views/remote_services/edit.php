<?php
$pageTitle = "Editar Servicio Remoto";
require_once __DIR__ . '/../../includes/header.php';
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

// Procesar el formulario si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updatedService = [
        'id' => $serviceId,
        'customer_id' => $_POST['customer_id'],
        'service_date' => $_POST['service_date'],
        'service_time' => $_POST['service_time'],
        'notes' => $_POST['notes'],
        'status' => $_POST['status']
    ];

    $result = updateRemoteService($updatedService['id'], $updatedService);

    if ($result['success']) {
        $_SESSION['flash_message'] = "Servicio actualizado con éxito.";
        $_SESSION['flash_type'] = 'success';
        header('Location: remote_services.php');
        exit();
    } else {
        $_SESSION['flash_message'] = "Error al actualizar el servicio: " . $result['message'];
        $_SESSION['flash_type'] = 'danger';
    }
}
?>

<div class="container mt-4">
    <h1 class="mb-4">Editar Servicio Remoto</h1>

    <form action="<?php echo url('remote_services.php?action=edit&id=' . $service['id']); ?>" method="post" id="remoteServiceForm">
        <div class="mb-3">
            <label for="customer_search" class="form-label">Cliente</label>
            <input type="text" class="form-control" id="customer_search" placeholder="Buscar cliente" value="<?php echo htmlspecialchars($service['customer_name'] ?? ''); ?>" required>
            <input type="hidden" id="customer_id" name="customer_id" value="<?php echo $service['customer_id'] ?? ''; ?>" required>
        </div>

        <div class="mb-3">
            <label for="service_date" class="form-label">Fecha de Servicio</label>
            <input type="date" class="form-control" id="service_date" name="service_date" value="<?php echo $service['service_date'] ?? ''; ?>" required>
        </div>

        <div class="mb-3">
            <label for="service_time" class="form-label">Hora de Servicio</label>
            <input type="time" class="form-control" id="service_time" name="service_time" value="<?php echo $service['service_time'] ?? ''; ?>" required>
        </div>

        <div class="mb-3">
            <label for="notes" class="form-label">Notas</label>
            <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo htmlspecialchars($service['notes'] ?? ''); ?></textarea>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Estado</label>
            <select class="form-control" id="status" name="status" required>
                <option value="programado" <?php echo ($service['status'] ?? '') === 'programado' ? 'selected' : ''; ?>>Programado</option>
                <option value="completado" <?php echo ($service['status'] ?? '') === 'completado' ? 'selected' : ''; ?>>Completado</option>
                <option value="cancelado" <?php echo ($service['status'] ?? '') === 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar Servicio</button>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>