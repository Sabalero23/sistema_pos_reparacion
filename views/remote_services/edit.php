<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/roles.php';
require_once __DIR__ . '/../../includes/remote_service_functions.php';

if (!isLoggedIn() || !hasPermission('remote_services_edit')) {
    $_SESSION['flash_message'] = "No tienes permiso para editar servicios remotos.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('remote_services.php'));
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

$pageTitle = "Editar Servicio Remoto";
include __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4">
    <h1>Editar Servicio Remoto</h1>

    <form action="<?php echo url('remote_services.php?action=edit&id=' . $service['id']); ?>" method="post" id="remoteServiceForm">
        <div class="mb-3">
            <label for="customer_search" class="form-label">Cliente</label>
            <input type="text" class="form-control" id="customer_search" placeholder="Buscar cliente" value="<?php echo htmlspecialchars($service['customer_name']); ?>" required autocomplete="off">
            <input type="hidden" id="customer_id" name="customer_id" value="<?php echo $service['customer_id']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="service_date" class="form-label">Fecha de Servicio</label>
            <input type="date" class="form-control" id="service_date" name="service_date" value="<?php echo $service['service_date']; ?>" required autocomplete="off">
        </div>

        <div class="mb-3">
            <label for="service_time" class="form-label">Hora de Servicio</label>
            <input type="time" class="form-control" id="service_time" name="service_time" value="<?php echo $service['service_time']; ?>" required autocomplete="off">
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Estado</label>
            <select class="form-control" id="status" name="status" required>
                <option value="programado" <?php echo $service['status'] == 'programado' ? 'selected' : ''; ?>>Programado</option>
                <option value="completado" <?php echo $service['status'] == 'completado' ? 'selected' : ''; ?>>Completado</option>
                <option value="cancelado" <?php echo $service['status'] == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="notes" class="form-label">Notas</label>
            <textarea class="form-control" id="notes" name="notes" rows="3" autocomplete="off"><?php echo htmlspecialchars($service['notes']); ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar Servicio Remoto</button>
    </form>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>