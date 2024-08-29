<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/reservation_functions.php';


if (!isLoggedIn() || !hasPermission('reservations_view')) {
    $_SESSION['flash_message'] = "No tienes permiso para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('index.php'));
    exit;
}

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'list';
$reservationId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

$pageTitle = "Gestión de Reservas";
require_once __DIR__ . '/../includes/header.php';

switch ($action) {
    case 'list':
        $reservations = getAllReservations();
        include __DIR__ . '/../views/reservations/list.php';
        break;

    case 'create':
        if (hasPermission('reservations_create')) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $postData = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $result = createReservation($postData);
                if ($result['success']) {
                    $_SESSION['flash_message'] = $result['message'];
                    $_SESSION['flash_type'] = 'success';
                    header('Location: ' . url('reservations.php'));
                    exit;
                } else {
                    $error = $result['message'];
                }
            }
            $products = getAllProducts();
            $customers = getAllCustomers();
            include __DIR__ . '/../views/reservations/create.php';
        } else {
            $_SESSION['flash_message'] = "No tienes permiso para crear reservas.";
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . url('reservations.php'));
            exit;
        }
        break;

    case 'view':
        if (!$reservationId) {
            $_SESSION['flash_message'] = "ID de reserva no proporcionado.";
            $_SESSION['flash_type'] = 'error';
            header('Location: ' . url('reservations.php'));
            exit;
        }
        $reservation = getReservationById($reservationId);
        if (!$reservation) {
            $_SESSION['flash_message'] = "Reserva no encontrada.";
            $_SESSION['flash_type'] = 'error';
            header('Location: ' . url('reservations.php'));
            exit;
        }
        $reservationItems = getReservationItems($reservationId);
        include __DIR__ . '/../views/reservations/view.php';
        break;

case 'confirm':
    header('Content-Type: application/json');
    try {
        if (!hasPermission('reservations_confirm')) {
            throw new Exception('No tienes permiso para confirmar reservas.');
        }
        if (!$reservationId) {
            throw new Exception('ID de reserva no proporcionado.');
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception('Método de solicitud no válido.');
        }
        
        $result = confirmReservation($reservationId);
        echo json_encode($result);
    } catch (Exception $e) {
        error_log('Error en la confirmación de reserva: ' . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error al confirmar la reserva: ' . $e->getMessage()
        ]);
    }
    exit;

    case 'convert':
        if (!hasPermission('reservations_convert')) {
            $_SESSION['flash_message'] = "No tienes permiso para convertir reservas en ventas.";
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . url('reservations.php'));
            exit;
        }
        if (!$reservationId) {
            $_SESSION['flash_message'] = "ID de reserva no proporcionado.";
            $_SESSION['flash_type'] = 'error';
            header('Location: ' . url('reservations.php'));
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postData = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $result = convertReservationToSale($reservationId, $postData);
            if ($result['success']) {
                $_SESSION['flash_message'] = $result['message'];
                $_SESSION['flash_type'] = 'success';
                header('Location: ' . url('sales.php?action=view&id=' . $result['sale_id']));
                exit;
            } else {
                $error = $result['message'];
            }
        }
        $reservation = getReservationById($reservationId);
        if (!$reservation) {
            $_SESSION['flash_message'] = "Reserva no encontrada.";
            $_SESSION['flash_type'] = 'error';
            header('Location: ' . url('reservations.php'));
            exit;
        }
        $reservationItems = getReservationItems($reservationId);
        include __DIR__ . '/../views/reservations/convert.php';
        break;

    default:
        $_SESSION['flash_message'] = "Acción no válida.";
        $_SESSION['flash_type'] = 'error';
        header('Location: ' . url('reservations.php'));
        exit;
}

require_once __DIR__ . '/../includes/footer.php';
?>