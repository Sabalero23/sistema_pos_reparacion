<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';


if (!isLoggedIn() || !hasPermission('settings_edit')) {
    $_SESSION['flash_message'] = "No tienes permiso para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('index.php'));
    exit;
}

$action = $_GET['action'] ?? 'list';

function getAllTerms() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM service_terms ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTermById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM service_terms WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function createTerm($content) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO service_terms (content) VALUES (?)");
    return $stmt->execute([$content]);
}

function updateTerm($id, $content) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE service_terms SET content = ? WHERE id = ?");
    return $stmt->execute([$content, $id]);
}

function activateTerm($id) {
    global $pdo;
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("UPDATE service_terms SET active = 0");
        $stmt->execute();
        $stmt = $pdo->prepare("UPDATE service_terms SET active = 1 WHERE id = ?");
        $stmt->execute([$id]);
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

$pageTitle = "Gestión de Términos y Condiciones";
require_once __DIR__ . '/../includes/header.php';

switch ($action) {
    case 'list':
        $terms = getAllTerms();
        include __DIR__ . '/../views/terms/list.php';
        break;

    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $content = $_POST['content'] ?? '';
            if (createTerm($content)) {
                $_SESSION['flash_message'] = "Términos creados con éxito.";
                $_SESSION['flash_type'] = 'success';
                header('Location: ' . url('manage_terms.php'));
                exit;
            } else {
                $error = "Error al crear los términos.";
            }
        }
        include __DIR__ . '/../views/terms/create.php';
        break;

    case 'edit':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['flash_message'] = "ID de términos no proporcionado.";
            $_SESSION['flash_type'] = 'error';
            header('Location: ' . url('manage_terms.php'));
            exit;
        }
        $term = getTermById($id);
        if (!$term) {
            $_SESSION['flash_message'] = "Términos no encontrados.";
            $_SESSION['flash_type'] = 'error';
            header('Location: ' . url('manage_terms.php'));
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $content = $_POST['content'] ?? '';
            if (updateTerm($id, $content)) {
                $_SESSION['flash_message'] = "Términos actualizados con éxito.";
                $_SESSION['flash_type'] = 'success';
                header('Location: ' . url('manage_terms.php'));
                exit;
            } else {
                $error = "Error al actualizar los términos.";
            }
        }
        include __DIR__ . '/../views/terms/edit.php';
        break;

    case 'activate':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['flash_message'] = "ID de términos no proporcionado.";
            $_SESSION['flash_type'] = 'error';
        } elseif (activateTerm($id)) {
            $_SESSION['flash_message'] = "Términos activados con éxito.";
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = "Error al activar los términos.";
            $_SESSION['flash_type'] = 'error';
        }
        header('Location: ' . url('manage_terms.php'));
        exit;
        break;

    default:
        $_SESSION['flash_message'] = "Acción no válida.";
        $_SESSION['flash_type'] = 'error';
        header('Location: ' . url('manage_terms.php'));
        exit;
}
require_once __DIR__ . '/../includes/footer.php';