<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/stock_functions.php';
require_once __DIR__ . '/../includes/cash_register_functions.php';
require_once __DIR__ . '/../includes/sale_functions.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isLoggedIn()) {
    $_SESSION['flash_message'] = "Debes iniciar sesión para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('login.php'));
    exit;
}

if (!hasPermission('sales_view')) {
    $_SESSION['flash_message'] = "No tienes permiso para acceder a esta página.";
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . url('index.php'));
    exit;
}

$saleFunctions = new SaleFunctions($pdo);

$action = $_GET['action'] ?? 'list';
$saleId = $_GET['id'] ?? null;

if ($action === 'get_sales_data') {
    header('Content-Type: application/json');
    echo json_encode(get_sales_data());
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($action) {
        case 'create':
            if (hasPermission('sales_create')) {
                $currentSession = getCurrentCashRegisterSession();
                if (!$currentSession) {
                    $_SESSION['flash_message'] = "No hay una sesión de caja abierta. Por favor, abra la caja antes de realizar una venta.";
                    $_SESSION['flash_type'] = 'warning';
                    header('Location: ' . url('cash_register.php?action=open'));
                    exit;
                }
                $result = createSale($_POST);
                if ($result['success']) {
                    $_SESSION['flash_message'] = $result['message'];
                    $_SESSION['flash_type'] = 'success';
                    header('Location: ' . url('sales.php'));
                    exit;
                } else {
                    $error = $result['message'];
                }
            } else {
                $_SESSION['flash_message'] = "No tienes permiso para crear ventas.";
                $_SESSION['flash_type'] = 'warning';
                header('Location: ' . url('sales.php'));
                exit;
            }
            break;
        case 'cancel':
            if (hasPermission('sales_cancel')) {
                $result = cancelSale($saleId);
                echo json_encode($result);
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => 'No tienes permiso para cancelar ventas.']);
                exit;
            }
            break;
    }
}

$pageTitle = "Gestión de Ventas";
require_once __DIR__ . '/../includes/header.php';

switch ($action) {
    case 'list':
        include __DIR__ . '/../views/sales/list.php';
        break;
    case 'create':
        if (hasPermission('sales_create')) {
            $products = getAllProducts();
            $customers = getAllCustomers();
            include __DIR__ . '/../views/sales/create.php';
        } else {
            $_SESSION['flash_message'] = "No tienes permiso para crear ventas.";
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . url('sales.php'));
            exit;
        }
        break;
    case 'view':
        if (!$saleId) {
            header('Location: ' . url('sales.php'));
            exit;
        }
        $sale = getSaleById($saleId);
        if (!$sale) {
            $_SESSION['flash_message'] = "Venta no encontrada.";
            $_SESSION['flash_type'] = 'error';
            header('Location: ' . url('sales.php'));
            exit;
        }
        $saleItems = getSaleItems($saleId);
        include __DIR__ . '/../views/sales/view.php';
        break;
    default:
        header('Location: ' . url('sales.php'));
        exit;
}

require_once __DIR__ . '/../includes/footer.php';

function get_sales_data() {
    global $pdo;
    $stmt = $pdo->query("SELECT s.id, c.name as customer_name, s.sale_date, s.total_amount, s.payment_method, s.status 
                         FROM sales s 
                         LEFT JOIN customers c ON s.customer_id = c.id 
                         ORDER BY s.sale_date DESC");
    $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $data = [];
    foreach ($sales as $sale) {
        $data[] = [
            'id' => $sale['id'],
            'customer_name' => $sale['customer_name'] ?? 'N/A',
            'sale_date' => $sale['sale_date'],
            'total_amount' => number_format($sale['total_amount'], 2),
            'payment_method' => ucfirst($sale['payment_method']),
            'status' => ucfirst($sale['status']),
            'actions' => '<a href="' . url('sales.php?action=view&id=' . $sale['id']) . '" class="btn btn-sm btn-info">Ver</a>'
        ];
    }
    
    return ['data' => $data];
}

function getAllSales() {
    global $pdo;
    $stmt = $pdo->query("SELECT s.*, c.name as customer_name 
                         FROM sales s 
                         LEFT JOIN customers c ON s.customer_id = c.id 
                         ORDER BY s.sale_date DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getSaleById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT s.*, c.name as customer_name, u.name as user_name 
                           FROM sales s 
                           LEFT JOIN customers c ON s.customer_id = c.id 
                           LEFT JOIN users u ON s.user_id = u.id 
                           WHERE s.id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getSaleItems($saleId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT si.*, p.name as product_name 
                           FROM sale_items si 
                           JOIN products p ON si.product_id = p.id 
                           WHERE si.sale_id = :sale_id");
    $stmt->execute([':sale_id' => $saleId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function createSale($data) {
    global $pdo;
    try {
        $pdo->beginTransaction();

        // Validar datos de entrada
        if (empty($data['customer_id']) || empty($data['payment_method']) || empty($data['items']) || empty($data['total_amount'])) {
            throw new Exception("Datos de venta incompletos");
        }

        $validPaymentMethods = ['efectivo', 'tarjeta', 'transferencia', 'otros'];
        if (!in_array($data['payment_method'], $validPaymentMethods)) {
            throw new Exception("Método de pago no válido");
        }

        $currentDateTime = date('Y-m-d H:i:s');
        $currentCashRegisterSession = getCurrentCashRegisterSession();
        if (!$currentCashRegisterSession) {
            throw new Exception("No hay una sesión de caja abierta. Por favor, abra una caja antes de realizar una venta.");
        }

        $stmt = $pdo->prepare("INSERT INTO sales (customer_id, user_id, sale_date, total_amount, payment_method, cash_register_session_id) 
                               VALUES (:customer_id, :user_id, :sale_date, :total_amount, :payment_method, :cash_register_session_id)");
        $stmt->execute([
            ':customer_id' => $data['customer_id'],
            ':user_id' => $_SESSION['user_id'],
            ':sale_date' => $currentDateTime,
            ':total_amount' => $data['total_amount'],
            ':payment_method' => $data['payment_method'],
            ':cash_register_session_id' => $currentCashRegisterSession['id']
        ]);
        $saleId = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO sale_items (sale_id, product_id, quantity, price) 
                               VALUES (:sale_id, :product_id, :quantity, :price)");
        foreach ($data['items'] as $item) {
            if (empty($item['product_id']) || empty($item['quantity']) || empty($item['price'])) {
                throw new Exception("Datos de producto incompletos");
            }
            $stmt->execute([
                ':sale_id' => $saleId,
                ':product_id' => $item['product_id'],
                ':quantity' => $item['quantity'],
                ':price' => $item['price']
            ]);

            updateStock($item['product_id'], -$item['quantity'], 'venta', $saleId, 'Venta de producto');
        }

        $pdo->commit();
        return ['success' => true, 'message' => 'Venta creada exitosamente.', 'sale_id' => $saleId];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Error al crear la venta: ' . $e->getMessage()];
    }
}

function cancelSale($id) {
    global $pdo;
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("UPDATE sales SET status = 'cancelled' WHERE id = :id");
        $stmt->execute([':id' => $id]);

        $saleItems = getSaleItems($id);
        foreach ($saleItems as $item) {
            updateStock($item['product_id'], $item['quantity'], 'sale_cancel', $id, 'Cancelación de venta');
        }

        $pdo->commit();
        return ['success' => true, 'message' => 'Venta cancelada exitosamente.'];
    } catch (PDOException $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Error al cancelar la venta: ' . $e->getMessage()];
    }
}

function getAllProducts() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM products WHERE stock_quantity > 0 ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllCustomers() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM customers ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>