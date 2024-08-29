<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/home_visit_functions.php';
require_once __DIR__ . '/../includes/customer_account_functions.php';

if (!isLoggedIn() || !hasPermission('home_visits_view')) {
    $_SESSION['flash_message'] = "No tienes permiso para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('index.php'));
    exit;
}

$action = $_GET['action'] ?? 'list';
$visitId = $_GET['id'] ?? null;

$pageTitle = "Gestión de Visitas a Domicilio";

try {
    switch ($action) {
        case 'list':
            $visits = getAllHomeVisits();
            require_once __DIR__ . '/../includes/header.php';
            include __DIR__ . '/../views/home_visits/list.php';
            break;

        case 'create':
            if (!hasPermission('home_visits_create')) {
                $_SESSION['flash_message'] = "No tienes permiso para crear visitas a domicilio.";
                $_SESSION['flash_type'] = 'warning';
                header('Location: ' . url('home_visits.php'));
                exit;
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
                $_SESSION['flash_message'] = "No tienes permiso para editar visitas a domicilio.";
                $_SESSION['flash_type'] = 'warning';
                header('Location: ' . url('home_visits.php'));
                exit;
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
        
        case 'updateStatus':
    if (!hasPermission('home_visits_edit')) {
        $_SESSION['flash_message'] = "No tienes permiso para actualizar el estado de las visitas a domicilio.";
        $_SESSION['flash_type'] = 'warning';
        header('Location: ' . url('home_visits.php'));
        exit;
    }
    if (!$visitId) {
        error_log("Intento de actualizar estado sin proporcionar ID de visita");
        throw new Exception("ID de visita no proporcionado.");
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $newStatus = $_POST['status'] ?? '';
        $allowedStatuses = ['programada', 'completada', 'cancelada'];
        if (!in_array($newStatus, $allowedStatuses)) {
            error_log("Intento de actualizar a un estado no válido: $newStatus");
            throw new Exception("Estado no válido.");
        }
        try {
            error_log("Intentando actualizar visita ID: $visitId a estado: $newStatus");
            updateHomeVisitStatus($visitId, $newStatus);
            $_SESSION['flash_message'] = "Estado de la visita actualizado con éxito.";
            $_SESSION['flash_type'] = 'success';
        } catch (Exception $e) {
            error_log("Error al actualizar el estado de la visita ID $visitId: " . $e->getMessage());
            $_SESSION['flash_message'] = "Error al actualizar el estado: " . $e->getMessage();
            $_SESSION['flash_type'] = 'error';
        }
        header('Location: ' . url('home_visits.php?action=view&id=' . $visitId));
        exit;
    }
    break;

        case 'delete':
    if (!hasPermission('home_visits_delete')) {
        $_SESSION['flash_message'] = "No tienes permiso para eliminar visitas a domicilio.";
        $_SESSION['flash_type'] = 'warning';
        header('Location: ' . url('home_visits.php'));
        exit;
    }
    if (!$visitId) {
        throw new Exception("ID de visita no proporcionado.");
    }
    $visit = getHomeVisit($visitId); // Obtener los detalles de la visita
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        deleteHomeVisit($visitId);
        $_SESSION['flash_message'] = "Visita a domicilio eliminada con éxito.";
        $_SESSION['flash_type'] = 'success';
        header('Location: ' . url('home_visits.php'));
        exit;
    }
    require_once __DIR__ . '/../includes/header.php';
    include __DIR__ . '/../views/home_visits/delete.php';
    break;

        case 'searchCustomers':
            if (!hasPermission('home_visits_create') && !hasPermission('home_visits_edit')) {
                echo json_encode(['error' => 'No tienes permiso para realizar esta acción.']);
                exit;
            }
            header('Content-Type: application/json');
            try {
                $term = $_GET['term'] ?? '';
                $customers = searchCustomers($term);
                echo json_encode($customers);
            } catch (Exception $e) {
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;

        case 'addCustomer':
            if (!hasPermission('home_visits_create') && !hasPermission('home_visits_edit')) {
                echo json_encode(['success' => false, 'message' => 'No tienes permiso para realizar esta acción.']);
                exit;
            }
            header('Content-Type: application/json');
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                try {
                    $result = addCustomer($_POST);
                    echo json_encode($result);
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            }
            exit;

        default:
            throw new Exception("Acción no válida.");
    }
} catch (Exception $e) {
    $_SESSION['flash_message'] = $e->getMessage();
    $_SESSION['flash_type'] = 'error';
    header('Location: ' . url('home_visits.php'));
    exit;
}

if (!in_array($action, ['searchCustomers', 'addCustomer'])) {
    require_once __DIR__ . '/../includes/footer.php';
}