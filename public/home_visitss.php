<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/home_visit_functions.php';
require_once __DIR__ . '/../includes/customer_account_functions.php';

// Verificar autenticación
if (!isLoggedIn()) {
    $_SESSION['flash_message'] = "Por favor, inicia sesión para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('login.php'));
    exit;
}

$action = $_GET['action'] ?? 'list';
$visitId = $_GET['id'] ?? null;

$pageTitle = "Gestión de Visitas a Domicilio";

try {
    switch ($action) {
        case 'list':
            if (!hasPermission('home_visits_view')) {
                throw new Exception("No tienes permiso para ver las visitas a domicilio.");
            }
            $visits = getAllHomeVisits();
            require_once __DIR__ . '/../includes/header.php';
            include __DIR__ . '/../views/home_visits/list.php';
            break;

        case 'create':
            if (!hasPermission('home_visits_create')) {
                throw new Exception("No tienes permiso para crear visitas a domicilio.");
            }
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $visitData = [
                    'customer_id' => $_POST['customer_id'] ?? '',
                    'visit_date' => $_POST['visit_date'] ?? '',
                    'visit_time' => $_POST['visit_time'] ?? '',
                    'notes' => $_POST['notes'] ?? ''
                ];
                $visitId = createHomeVisit($visitData);
                $_SESSION['flash_message'] = "Visita a domicilio creada con éxito.";
                $_SESSION['flash_type'] = 'success';
                header('Location: ' . url('home_visits.php?action=view&id=' . $visitId));
                exit;
            }
            require_once __DIR__ . '/../includes/header.php';
            include __DIR__ . '/../views/home_visits/create.php';
            break;

        case 'view':
            if (!hasPermission('home_visits_view')) {
                throw new Exception("No tienes permiso para ver los detalles de las visitas a domicilio.");
            }
            if (!$visitId) {
                throw new Exception("ID de visita no proporcionado.");
            }
            $visit = getHomeVisit($visitId);
            if (!$visit) {
                throw new Exception("Visita a domicilio no encontrada.");
            }
            require_once __DIR__ . '/../includes/header.php';
            include __DIR__ . '/../views/home_visits/view.php';
            break;

        case 'edit':
            if (!hasPermission('home_visits_edit')) {
                throw new Exception("No tienes permiso para editar visitas a domicilio.");
            }
            if (!$visitId) {
                throw new Exception("ID de visita no proporcionado.");
            }
            $visit = getHomeVisit($visitId);
            if (!$visit) {
                throw new Exception("Visita a domicilio no encontrada.");
            }
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $visitData = [
                    'customer_id' => $_POST['customer_id'] ?? '',
                    'visit_date' => $_POST['visit_date'] ?? '',
                    'visit_time' => $_POST['visit_time'] ?? '',
                    'notes' => $_POST['notes'] ?? ''
                ];
                updateHomeVisit($visitId, $visitData);
                $_SESSION['flash_message'] = "Visita a domicilio actualizada con éxito.";
                $_SESSION['flash_type'] = 'success';
                header('Location: ' . url('home_visits.php?action=view&id=' . $visitId));
                exit;
            }
            require_once __DIR__ . '/../includes/header.php';
            include __DIR__ . '/../views/home_visits/edit.php';
            break;

        case 'delete':
            if (!hasPermission('home_visits_delete')) {
                throw new Exception("No tienes permiso para eliminar visitas a domicilio.");
            }
            if (!$visitId) {
                throw new Exception("ID de visita no proporcionado.");
            }
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                deleteHomeVisit($visitId);
                $_SESSION['flash_message'] = "Visita a domicilio eliminada con éxito.";
                $_SESSION['flash_type'] = 'success';
                header('Location: ' . url('home_visits.php'));
                exit;
            }
            $visit = getHomeVisit($visitId);
            if (!$visit) {
                throw new Exception("Visita a domicilio no encontrada.");
            }
            require_once __DIR__ . '/../includes/header.php';
            include __DIR__ . '/../views/home_visits/delete.php';
            break;

        case 'updateStatus':
            if (!hasPermission('home_visits_edit')) {
                throw new Exception("No tienes permiso para actualizar el estado de las visitas a domicilio.");
            }
            if (!$visitId) {
                throw new Exception("ID de visita no proporcionado.");
            }
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $newStatus = $_POST['status'] ?? '';
                $allowedStatuses = ['programada', 'completada', 'cancelada'];
                if (!in_array($newStatus, $allowedStatuses)) {
                    throw new Exception("Estado no válido.");
                }
                updateHomeVisitStatus($visitId, $newStatus);
                $_SESSION['flash_message'] = "Estado de la visita actualizado con éxito.";
                $_SESSION['flash_type'] = 'success';
                header('Location: ' . url('home_visits.php?action=view&id=' . $visitId));
                exit;
            }
            throw new Exception("Método no permitido para actualizar el estado.");

        default:
            throw new Exception("Acción no válida.");
    }
} catch (Exception $e) {
    $_SESSION['flash_message'] = $e->getMessage();
    $_SESSION['flash_type'] = 'error';
    error_log("Error en home_visits.php: " . $e->getMessage());
    header('Location: ' . url('home_visits.php'));
    exit;
}

// Si llegamos aquí, incluimos el pie de página
require_once __DIR__ . '/../includes/footer.php';