<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/remote_service_functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

switch ($action) {
    case 'create':
        if (!hasPermission('remote_services_create')) {
            $_SESSION['flash_message'] = "No tienes permiso para crear servicios remotos.";
            $_SESSION['flash_type'] = 'warning';
            header('Location: remote_services.php');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = createRemoteService($_POST);
            if ($result['success']) {
                $_SESSION['flash_message'] = "Servicio remoto creado con éxito.";
                $_SESSION['flash_type'] = 'success';
                header('Location: remote_services.php');
            } else {
                $_SESSION['flash_message'] = "Error al crear el servicio remoto: " . $result['message'];
                $_SESSION['flash_type'] = 'danger';
            }
            exit();
        }
        include __DIR__ . '/../views/remote_services/create.php';
        break;

    case 'edit':
        if (!hasPermission('remote_services_edit')) {
            $_SESSION['flash_message'] = "No tienes permiso para editar servicios remotos.";
            $_SESSION['flash_type'] = 'warning';
            header('Location: remote_services.php');
            exit();
        }
        $service = getRemoteService($id);
        if (!$service) {
            $_SESSION['flash_message'] = "Servicio remoto no encontrado.";
            $_SESSION['flash_type'] = 'danger';
            header('Location: remote_services.php');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = updateRemoteService($id, $_POST);
            if ($result['success']) {
                $_SESSION['flash_message'] = "Servicio remoto actualizado con éxito.";
                $_SESSION['flash_type'] = 'success';
                header('Location: remote_services.php');
            } else {
                $_SESSION['flash_message'] = "Error al actualizar el servicio remoto: " . $result['message'];
                $_SESSION['flash_type'] = 'danger';
            }
            exit();
        }
        include __DIR__ . '/../views/remote_services/edit.php';
        break;

    case 'delete':
        if (!hasPermission('remote_services_delete')) {
            $_SESSION['flash_message'] = "No tienes permiso para eliminar servicios remotos.";
            $_SESSION['flash_type'] = 'warning';
            header('Location: remote_services.php');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = deleteRemoteService($id);
            if ($result['success']) {
                $_SESSION['flash_message'] = "Servicio remoto eliminado con éxito.";
                $_SESSION['flash_type'] = 'success';
            } else {
                $_SESSION['flash_message'] = "Error al eliminar el servicio remoto: " . $result['message'];
                $_SESSION['flash_type'] = 'danger';
            }
            header('Location: remote_services.php');
            exit();
        } else {
            // Si no es una solicitud POST, mostramos la página de confirmación
            $service = getRemoteService($id);
            if (!$service) {
                $_SESSION['flash_message'] = "Servicio remoto no encontrado.";
                $_SESSION['flash_type'] = 'danger';
                header('Location: remote_services.php');
                exit();
            }
            include __DIR__ . '/../views/remote_services/delete.php';
        }
        break;

    case 'view':
        if (!hasPermission('remote_services_view')) {
            $_SESSION['flash_message'] = "No tienes permiso para ver detalles de servicios remotos.";
            $_SESSION['flash_type'] = 'warning';
            header('Location: remote_services.php');
            exit();
        }
        $service = getRemoteService($id);
        if (!$service) {
            $_SESSION['flash_message'] = "Servicio remoto no encontrado.";
            $_SESSION['flash_type'] = 'danger';
            header('Location: remote_services.php');
            exit();
        }
        include __DIR__ . '/../views/remote_services/view.php';
        break;

    case 'list':
    default:
        if (!hasPermission('remote_services_view')) {
            $_SESSION['flash_message'] = "No tienes permiso para ver la lista de servicios remotos.";
            $_SESSION['flash_type'] = 'warning';
            header('Location: index.php');
            exit();
        }
        $services = getAllRemoteServices();
        include __DIR__ . '/../views/remote_services/list.php';
        break;
}