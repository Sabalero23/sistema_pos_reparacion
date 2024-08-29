<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/stock_functions.php';
require_once __DIR__ . '/../includes/cash_register_functions.php';


if (!isLoggedIn() || !hasPermission('purchases_view')) {
    $_SESSION['flash_message'] = "No tienes permiso para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('index.php'));
    exit;
}

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'list';
$purchaseId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

$pageTitle = "Gestión de Compras";
require_once __DIR__ . '/../includes/header.php';



switch ($action) {
    case 'list':
        $purchases = getAllPurchases();
        include __DIR__ . '/../views/purchases/list.php';
        break;
    case 'create':
        if (hasPermission('purchases_create')) {
            $currentSession = getCurrentCashRegisterSession();
            if (!$currentSession) {
                $_SESSION['flash_message'] = "No hay una sesión de caja abierta. Por favor, abra la caja antes de registrar una compra.";
                $_SESSION['flash_type'] = 'warning';
                header('Location: ' . url('cash_register.php?action=open'));
                exit;
            }
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $postData = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $result = createPurchase($postData);
                if ($result['success']) {
                    $_SESSION['flash_message'] = $result['message'];
                    $_SESSION['flash_type'] = 'success';
                    header('Location: ' . url('purchases.php'));
                    exit;
                } else {
                    $error = $result['message'];
                }
            }
            $products = getAllProducts();
            $suppliers = getAllSuppliers();
            include __DIR__ . '/../views/purchases/create.php';
        } else {
            $_SESSION['flash_message'] = "No tienes permiso para crear compras.";
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . url('purchases.php'));
            exit;
        }
        break;
    case 'view':
        if (!$purchaseId) {
            header('Location: ' . url('purchases.php'));
            exit;
        }
        $purchase = getPurchaseById($purchaseId);
        if (!$purchase) {
            $_SESSION['flash_message'] = "Compra no encontrada.";
            $_SESSION['flash_type'] = 'error';
            header('Location: ' . url('purchases.php'));
            exit;
        }
        $purchaseItems = getPurchaseItems($purchaseId);
        include __DIR__ . '/../views/purchases/view.php';
        break;
    case 'receive':
        if (!$purchaseId) {
            header('Location: ' . url('purchases.php'));
            exit;
        }
        if (hasPermission('purchases_receive')) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $postData = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $result = receivePurchase($purchaseId, $postData);
                if ($result['success']) {
                    $_SESSION['flash_message'] = $result['message'];
                    $_SESSION['flash_type'] = 'success';
                    header('Location: ' . url('purchases.php?action=view&id=' . $purchaseId));
                    exit;
                } else {
                    $error = $result['message'];
                }
            }
            $purchase = getPurchaseById($purchaseId);
            $purchaseItems = getPurchaseItems($purchaseId);
            include __DIR__ . '/../views/purchases/receive.php';
        } else {
            $_SESSION['flash_message'] = "No tienes permiso para recibir compras.";
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . url('purchases.php'));
            exit;
        }
        break;
    case 'cancel':
        if (!$purchaseId) {
            header('Location: ' . url('purchases.php'));
            exit;
        }
        if (hasPermission('purchases_cancel')) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $result = cancelPurchase($purchaseId);
                echo json_encode($result);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No tienes permiso para cancelar compras.']);
            exit;
        }
        break;
    case 'movements':
        if (!$purchaseId) {
            header('Location: ' . url('purchases.php'));
            exit;
        }
        if (hasPermission('purchases_view_movements')) {
            $purchase = getPurchaseById($purchaseId);
            if (!$purchase) {
                $_SESSION['flash_message'] = "Compra no encontrada.";
                $_SESSION['flash_type'] = 'error';
                header('Location: ' . url('purchases.php'));
                exit;
            }
            $movements = getPurchaseMovements($purchaseId);
            include __DIR__ . '/../views/purchases/movements.php';
        } else {
            $_SESSION['flash_message'] = "No tienes permiso para ver los movimientos de compras.";
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . url('purchases.php'));
            exit;
        }
        break;
    default:
        header('Location: ' . url('purchases.php'));
        exit;
}

require_once __DIR__ . '/../includes/footer.php';

function createPurchase($data) {
    global $pdo;
    $transactionStarted = false;
    try {
        $pdo->beginTransaction();
        $transactionStarted = true;

        $currentSession = getCurrentCashRegisterSession();
        if (!$currentSession) {
            throw new Exception("No hay una sesión de caja abierta. Por favor, abra la caja antes de registrar una compra.");
        }

        if (empty($data['supplier_id']) || empty($data['items']) || empty($data['total_amount'])) {
            throw new Exception("Datos de compra incompletos");
        }

        $currentDateTime = date('Y-m-d H:i:s');
        error_log("Fecha y hora de la compra: " . $currentDateTime);

        $stmt = $pdo->prepare("INSERT INTO purchases (supplier_id, user_id, purchase_date, total_amount, status, cash_register_session_id) 
                               VALUES (:supplier_id, :user_id, :purchase_date, :total_amount, 'pendiente', :cash_register_session_id)");
        $stmt->execute([
            ':supplier_id' => $data['supplier_id'],
            ':user_id' => $_SESSION['user_id'],
            ':purchase_date' => $currentDateTime,
            ':total_amount' => $data['total_amount'],
            ':cash_register_session_id' => $currentSession['id']
        ]);
        $purchaseId = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO purchase_items (purchase_id, product_id, quantity, price) 
                               VALUES (:purchase_id, :product_id, :quantity, :price)");
        foreach ($data['items'] as $item) {
            if (empty($item['product_id']) || empty($item['quantity']) || empty($item['price'])) {
                throw new Exception("Datos de producto incompletos");
            }
            $stmt->execute([
                ':purchase_id' => $purchaseId,
                ':product_id' => $item['product_id'],
                ':quantity' => $item['quantity'],
                ':price' => $item['price']
            ]);
        }

        // Registrar el movimiento en la caja
        addCashRegisterMovement($currentSession['id'], 'purchase', -$data['total_amount'], $purchaseId, "Compra #$purchaseId");

        // Registrar el movimiento de la compra
        addPurchaseMovement($purchaseId, $_SESSION['user_id'], 'creacion', "Compra creada");

        $pdo->commit();
        error_log("Compra creada exitosamente. ID: " . $purchaseId);
        return ['success' => true, 'message' => 'Compra creada exitosamente.', 'purchase_id' => $purchaseId];
    } catch (Exception $e) {
        if ($transactionStarted) {
            $pdo->rollBack();
        }
        error_log("Error al crear la compra: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error al crear la compra: ' . $e->getMessage()];
    }
}

function receivePurchase($purchaseId, $data) {
    global $pdo;
    try {
        $pdo->beginTransaction();

        $purchase = getPurchaseById($purchaseId);
        if (!$purchase) {
            throw new Exception("Compra no encontrada.");
        }

        if ($purchase['status'] !== 'pendiente') {
            throw new Exception("La compra ya ha sido recibida o cancelada.");
        }

        $stmt = $pdo->prepare("UPDATE purchases SET status = 'recibido', received_date = NOW(), total_amount = :total_amount WHERE id = :id");
        
        $totalAmount = 0;
        $stmt2 = $pdo->prepare("UPDATE purchase_items SET received_quantity = :received_quantity, price = :price WHERE id = :item_id");
        
        foreach ($data['items'] as $itemId => $itemData) {
            $receivedQuantity = isset($itemData['received_quantity']) ? floatval($itemData['received_quantity']) : 0;
            $price = isset($itemData['price']) ? floatval($itemData['price']) : 0;
            $subtotal = $receivedQuantity * $price;
            $totalAmount += $subtotal;

            $stmt2->execute([
                ':item_id' => $itemId,
                ':received_quantity' => $receivedQuantity,
                ':price' => $price
            ]);

            $item = getPurchaseItemById($itemId);
            updateStock($item['product_id'], $receivedQuantity, 'compra', $purchaseId, 'Recepción de compra');
        }

        $stmt->execute([
            ':id' => $purchaseId,
            ':total_amount' => $totalAmount
        ]);

        // Registrar el movimiento de la compra
        addPurchaseMovement($purchaseId, $_SESSION['user_id'], 'recepcion', "Compra recibida. Total actualizado: $" . number_format($totalAmount, 2));

        $pdo->commit();
        return ['success' => true, 'message' => 'Compra recibida exitosamente.'];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Error al recibir la compra: ' . $e->getMessage()];
    }
}

function cancelPurchase($purchaseId) {
    global $pdo;
    try {
        $pdo->beginTransaction();

        $purchase = getPurchaseById($purchaseId);
        if (!$purchase) {
            throw new Exception("Compra no encontrada.");
        }

        if ($purchase['status'] === 'cancelado') {
            throw new Exception("La compra ya ha sido cancelada.");
        }

        $stmt = $pdo->prepare("UPDATE purchases SET status = 'cancelado' WHERE id = :id");
        $stmt->execute([':id' => $purchaseId]);

        if ($purchase['status'] === 'recibido') {
            $purchaseItems = getPurchaseItems($purchaseId);
            foreach ($purchaseItems as $item) {
                updateStock($item['product_id'], -$item['received_quantity'], 'compra_cancel', $purchaseId, 'Cancelación de compra');
            }
        }

        // Registrar el movimiento en la caja si la compra estaba pagada
        $currentSession = getCurrentCashRegisterSession();
        if ($currentSession && $purchase['status'] !== 'pendiente') {
            addCashRegisterMovement($currentSession['id'], 'purchase_cancel', $purchase['total_amount'], $purchaseId, "Cancelación Compra #$purchaseId");
        }

        // Registrar el movimiento de la compra
        addPurchaseMovement($purchaseId, $_SESSION['user_id'], 'cancelacion', "Compra cancelada");

        $pdo->commit();
        return ['success' => true, 'message' => 'Compra cancelada exitosamente.'];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Error al cancelar la compra: ' . $e->getMessage()];
    }
}

function addPurchaseMovement($purchaseId, $userId, $movementType, $details) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO purchase_movements (purchase_id, user_id, movement_type, details) 
                           VALUES (:purchase_id, :user_id, :movement_type, :details)");
    $stmt->execute([
        ':purchase_id' => $purchaseId,
        ':user_id' => $userId,
        ':movement_type' => $movementType,
        ':details' => $details
    ]);
}

function getPurchaseItemById($itemId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM purchase_items WHERE id = :id");
    $stmt->execute([':id' => $itemId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getAllPurchases() {
    global $pdo;
    $stmt = $pdo->query("SELECT p.*, s.name as supplier_name 
                         FROM purchases p 
                         LEFT JOIN suppliers s ON p.supplier_id = s.id 
                         ORDER BY p.purchase_date DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPurchaseById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT p.*, s.name as supplier_name, u.name as user_name 
                           FROM purchases p 
                           LEFT JOIN suppliers s ON p.supplier_id = s.id 
                           LEFT JOIN users u ON p.user_id = u.id 
                           WHERE p.id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getPurchaseItems($purchaseId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT pi.*, p.name as product_name 
                           FROM purchase_items pi 
                           JOIN products p ON pi.product_id = p.id 
                           WHERE pi.purchase_id = :purchase_id");
    $stmt->execute([':purchase_id' => $purchaseId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPurchaseMovements($purchaseId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT pm.*, u.name as user_name 
                           FROM purchase_movements pm 
                           JOIN users u ON pm.user_id = u.id 
                           WHERE pm.purchase_id = :purchase_id 
                           ORDER BY pm.created_at DESC");
    $stmt->execute([':purchase_id' => $purchaseId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllProducts() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM products ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllSuppliers() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM suppliers ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



function getSupplierById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM suppliers WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getProductById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function formatCurrency($amount) {
    return number_format($amount, 2, ',', '.');
}

function formatDate($date) {
    return date('d/m/Y H:i', strtotime($date));
}

// Función para verificar si una compra puede ser editada
function canEditPurchase($purchase) {
    return $purchase['status'] === 'pendiente';
}

// Función para verificar si una compra puede ser cancelada
function canCancelPurchase($purchase) {
    return $purchase['status'] !== 'cancelado';
}

// Función para obtener el total de compras en un período
function getTotalPurchases($startDate, $endDate) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT SUM(total_amount) FROM purchases 
                           WHERE purchase_date BETWEEN :start_date AND :end_date 
                           AND status != 'cancelado'");
    $stmt->execute([':start_date' => $startDate, ':end_date' => $endDate]);
    return $stmt->fetchColumn();
}

// Función para obtener las compras por proveedor en un período
function getPurchasesBySupplier($startDate, $endDate) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT s.name as supplier_name, SUM(p.total_amount) as total 
                           FROM purchases p 
                           JOIN suppliers s ON p.supplier_id = s.id 
                           WHERE p.purchase_date BETWEEN :start_date AND :end_date 
                           AND p.status != 'cancelado' 
                           GROUP BY p.supplier_id 
                           ORDER BY total DESC");
    $stmt->execute([':start_date' => $startDate, ':end_date' => $endDate]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener los productos más comprados en un período
function getMostPurchasedProducts($startDate, $endDate, $limit = 10) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT p.name as product_name, SUM(pi.quantity) as total_quantity 
                           FROM purchase_items pi 
                           JOIN products p ON pi.product_id = p.id 
                           JOIN purchases pu ON pi.purchase_id = pu.id 
                           WHERE pu.purchase_date BETWEEN :start_date AND :end_date 
                           AND pu.status != 'cancelado' 
                           GROUP BY pi.product_id 
                           ORDER BY total_quantity DESC 
                           LIMIT :limit");
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date', $endDate);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener el historial de precios de un producto
function getProductPriceHistory($productId, $limit = 10) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT p.purchase_date, pi.price 
                           FROM purchase_items pi 
                           JOIN purchases p ON pi.purchase_id = p.id 
                           WHERE pi.product_id = :product_id 
                           ORDER BY p.purchase_date DESC 
                           LIMIT :limit");
    $stmt->bindParam(':product_id', $productId);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener las compras pendientes de recepción
function getPendingPurchases() {
    global $pdo;
    $stmt = $pdo->query("SELECT p.*, s.name as supplier_name 
                         FROM purchases p 
                         LEFT JOIN suppliers s ON p.supplier_id = s.id 
                         WHERE p.status = 'pendiente' 
                         ORDER BY p.purchase_date DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener el total de compras por mes en el último año
function getPurchasesByMonth() {
    global $pdo;
    $stmt = $pdo->query("SELECT DATE_FORMAT(purchase_date, '%Y-%m') as month, SUM(total_amount) as total 
                         FROM purchases 
                         WHERE purchase_date >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) 
                         AND status != 'cancelado' 
                         GROUP BY month 
                         ORDER BY month");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>