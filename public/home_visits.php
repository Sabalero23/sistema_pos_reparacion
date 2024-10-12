<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/home_visit_functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

switch ($action) {
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
        
        // Validar los datos (puedes agregar más validaciones si es necesario)
        if (empty($visitData['customer_id']) || empty($visitData['visit_date']) || empty($visitData['visit_time'])) {
            throw new Exception("Por favor, complete todos los campos obligatorios.");
        }

        $result = createHomeVisit($visitData);
        
        if ($result['success']) {
            $_SESSION['flash_message'] = "Visita a domicilio creada con éxito.";
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = "Error al crear la visita: " . $result['message'];
            $_SESSION['flash_type'] = 'danger';
        }
        
        header('Location: ' . url('home_visits.php'));
        exit;
    }
    
    require_once __DIR__ . '/../includes/header.php';
    include __DIR__ . '/../views/home_visits/create.php';
    break;
    case 'edit':
        if (!hasPermission('home_visits_edit')) {
            $_SESSION['error'] = "No tienes permiso para editar visitas a domicilio.";
            header('Location: home_visits.php');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $visitData = [
                'customer_id' => $_POST['customer_id'] ?? '',
                'visit_date' => $_POST['visit_date'] ?? '',
                'visit_time' => $_POST['visit_time'] ?? '',
                'notes' => $_POST['notes'] ?? '',
                'status' => $_POST['status'] ?? 'programada'
            ];
            
            // Validar los datos
            if (empty($visitData['customer_id']) || empty($visitData['visit_date']) || empty($visitData['visit_time'])) {
                throw new Exception("Por favor, complete todos los campos obligatorios.");
            }

            $result = updateHomeVisit($id, $visitData);
            
            if ($result['success']) {
                $_SESSION['flash_message'] = "Visita a domicilio actualizada con éxito.";
                $_SESSION['flash_type'] = 'success';
            } else {
                $_SESSION['flash_message'] = "Error al actualizar la visita: " . $result['message'];
                $_SESSION['flash_type'] = 'danger';
            }
            
            header('Location: ' . url('home_visits.php'));
            exit;
        }
        
        $visit = getHomeVisit($id);
        if (!$visit) {
            $_SESSION['error'] = "Visita no encontrada.";
            header('Location: home_visits.php');
            exit();
        }
        
        require_once __DIR__ . '/../includes/header.php';
        include __DIR__ . '/../views/home_visits/edit.php';
        break;
    
    case 'store':
        if (!hasPermission('home_visits_create')) {
            $_SESSION['error'] = "No tienes permiso para crear visitas a domicilio.";
            header('Location: home_visits.php');
            exit();
        }
        $result = createHomeVisit($_POST);
        if ($result['success']) {
            $_SESSION['success'] = "Visita a domicilio creada con éxito.";
        } else {
            $_SESSION['error'] = $result['message'];
        }
        header('Location: home_visits.php');
        break;
    case 'view':
        if (!hasPermission('home_visits_view')) {
            $_SESSION['error'] = "No tienes permiso para ver detalles de visitas a domicilio.";
            header('Location: home_visits.php');
            exit();
        }
        $visit = getHomeVisit($id);
        include __DIR__ . '/../views/home_visits/view.php';
        break;
    case 'list':
    default:
        if (!hasPermission('home_visits_view')) {
            $_SESSION['error'] = "No tienes permiso para ver la lista de visitas a domicilio.";
            header('Location: index.php');
            exit();
        }
        $visits = getAllHomeVisits();
        include __DIR__ . '/../views/home_visits/list.php';
        break;
}