<?php
// Asegúrate de que esto esté al principio del archivo
ob_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/utils.php';
mb_internal_encoding('UTF-8');

if (!isLoggedIn() || !hasPermission('budget_view')) {
    setFlashMessage("No tienes permiso para acceder a esta página.", 'warning');
    redirect('index.php');
}

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'list':
        $budgets = getAllBudgets();
        require_once __DIR__ . '/../views/budgets/list.php';
        break;
    case 'create':
        if (!hasPermission('budget_create')) {
            setFlashMessage("No tienes permiso para crear presupuestos.", 'warning');
            redirect('budget.php');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $budgetData = [
                'customer_id' => $_POST['customer_id'],
                'user_id' => $_SESSION['user_id'],
                'total_amount' => $_POST['total_amount'],
                'validity_period' => $_POST['validity_period'],
                'notes' => $_POST['notes'],
                'items' => $_POST['items']
            ];
            $result = createBudget($budgetData);
            if ($result) {
                setFlashMessage("Presupuesto creado con éxito.", 'success');
                redirect('budget.php');
            } else {
                setFlashMessage("Error al crear el presupuesto.", 'danger');
            }
        }
        $customers = getAllCustomers();
        $products = getAllProducts();
        require_once __DIR__ . '/../views/budgets/create.php';
        break;
    case 'edit':
        if (!hasPermission('budget_edit')) {
            setFlashMessage("No tienes permiso para editar presupuestos.", 'warning');
            redirect('budget.php');
        }
        $budgetId = $_GET['id'] ?? null;
        if (!$budgetId) {
            setFlashMessage("ID de presupuesto no proporcionado.", 'danger');
            redirect('budget.php');
        }
        $budget = getBudgetById($budgetId);
        if (!$budget) {
            setFlashMessage("Presupuesto no encontrado.", 'danger');
            redirect('budget.php');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $budgetData = [
                'id' => $budgetId,
                'customer_id' => $_POST['customer_id'],
                'total_amount' => $_POST['total_amount'],
                'validity_period' => $_POST['validity_period'],
                'notes' => $_POST['notes'],
                'items' => $_POST['items']
            ];
            $result = updateBudget($budgetData);
            if ($result) {
                setFlashMessage("Presupuesto actualizado con éxito.", 'success');
                redirect('budget.php');
            } else {
                setFlashMessage("Error al actualizar el presupuesto.", 'danger');
            }
        }
        $customers = getAllCustomers();
        $products = getAllProducts();
        require_once __DIR__ . '/../views/budgets/edit.php';
        break;
case 'view':
    $budgetId = $_GET['id'] ?? null;
    if (!$budgetId) {
        setFlashMessage("ID de presupuesto no proporcionado.", 'danger');
        redirect('budget.php');
    }
    $budget = getBudgetById($budgetId);
    if (!$budget) {
        setFlashMessage("Presupuesto no encontrado.", 'danger');
        redirect('budget.php');
    }
    require_once __DIR__ . '/../views/budgets/view.php';
    break;
    
   case 'download_pdf':
    if (!isset($_GET['id'])) {
        die("ID de presupuesto no proporcionado");
    }
    $budgetId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    $budget = getBudgetById($budgetId);
    if (!$budget) {
        die("Presupuesto no encontrado");
    }
    $companyInfo = getCompanyInfo();
    
    require_once __DIR__ . '/../includes/tfpdf/tfpdf.php';  // Asegúrate de que la ruta sea correcta

    class PDF extends tFPDF
    {
        function Header()
        {
            global $companyInfo;
            $this->SetFont('Arial','B',15);
            $this->Cell(0,10,$this->convToIso($companyInfo['name']),0,1,'C');
            $this->SetFont('Arial','',10);
            $this->Cell(0,5,$this->convToIso($companyInfo['address']),0,1,'C');
            $this->Cell(0,5,'Tel: '.$companyInfo['phone'].' | Email: '.$companyInfo['email'],0,1,'C');
            $this->Ln(10);
        }

        function Footer()
        {
            global $companyInfo;
            $this->SetY(-15);
            $this->SetFont('Arial','I',8);
            $this->Cell(0,10,$this->convToIso($companyInfo['legal_info']),0,0,'C');
        }

        function convToIso($text) {
            return mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
        }
    }

    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(0,10,$pdf->convToIso('Presupuesto #'.$budget['id']),0,1,'C');
    $pdf->SetFont('Arial','',12);
    $pdf->Cell(0,10,$pdf->convToIso('Fecha: '.$budget['budget_date'].' | Validez: '.$budget['validity_period'].' días'),0,1);
    $pdf->Cell(0,10,$pdf->convToIso('Cliente: '.($budget['customer_name'] ?? 'No especificado').' | Elaborado por: '.$budget['user_name']),0,1);
    
    $pdf->Ln(10);
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(80,10,$pdf->convToIso('Producto'),1);
    $pdf->Cell(30,10,$pdf->convToIso('Cantidad'),1);
    $pdf->Cell(40,10,$pdf->convToIso('Precio Unitario'),1);
    $pdf->Cell(40,10,$pdf->convToIso('Subtotal'),1);
    $pdf->Ln();
    
    $pdf->SetFont('Arial','',12);
    foreach ($budget['items'] as $item) {
        $pdf->Cell(80,10,$pdf->convToIso($item['product_name']),1);
        $pdf->Cell(30,10,$item['quantity'],1);
        $pdf->Cell(40,10,'$'.number_format($item['price'], 2),1);
        $pdf->Cell(40,10,'$'.number_format($item['quantity'] * $item['price'], 2),1);
        $pdf->Ln();
    }
    
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(150,10,$pdf->convToIso('Total:'),1);
    $pdf->Cell(40,10,'$'.number_format($budget['total_amount'], 2),1);
    
    $pdf->Ln(20);
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(0,10,$pdf->convToIso('Estado: '.ucfirst($budget['status'])),0,1);
    if (!empty($budget['notes'])) {
        $pdf->MultiCell(0,10,$pdf->convToIso('Notas: '.$budget['notes']),0,'L');
    }

    // Limpia cualquier salida anterior
    ob_end_clean();

    $pdf->Output('Presupuesto_'.$budget['id'].'.pdf', 'D');
    exit;
    
    case 'change_status':
    if (!hasPermission('budget_change_status')) {
        setFlashMessage("No tienes permiso para cambiar el estado de los presupuestos.", 'warning');
        redirect('budget.php');
    }
    $budgetId = $_POST['id'] ?? null;
    $newStatus = $_POST['status'] ?? null;
    if (!$budgetId || !$newStatus) {
        setFlashMessage("ID de presupuesto o estado no proporcionado.", 'danger');
        redirect('budget.php');
    }
    $result = changeBudgetStatus($budgetId, $newStatus);
    if ($result) {
        setFlashMessage("Estado del presupuesto actualizado con éxito.", 'success');
    } else {
        setFlashMessage("Error al actualizar el estado del presupuesto.", 'danger');
    }
    redirect('budget.php?action=view&id=' . $budgetId);
    break;
    
    case 'delete':
        if (!hasPermission('budget_delete')) {
            setFlashMessage("No tienes permiso para eliminar presupuestos.", 'warning');
            redirect('budget.php');
        }
        $budgetId = $_GET['id'] ?? null;
        if (!$budgetId) {
            setFlashMessage("ID de presupuesto no proporcionado.", 'danger');
            redirect('budget.php');
        }
        $result = deleteBudget($budgetId);
        if ($result) {
            setFlashMessage("Presupuesto eliminado con éxito.", 'success');
        } else {
            setFlashMessage("Error al eliminar el presupuesto.", 'danger');
        }
        redirect('budget.php');
        break;
    default:
        setFlashMessage("Acción no válida.", 'danger');
        redirect('budget.php');
}

function getAllBudgets() {
    global $pdo;
    $stmt = $pdo->query("SELECT b.*, c.name as customer_name, c.phone as customer_phone 
                         FROM budgets b 
                         LEFT JOIN customers c ON b.customer_id = c.id 
                         ORDER BY b.budget_date DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getBudgetById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT b.*, c.name as customer_name, u.name as user_name 
                           FROM budgets b 
                           LEFT JOIN customers c ON b.customer_id = c.id 
                           LEFT JOIN users u ON b.user_id = u.id 
                           WHERE b.id = ?");
    $stmt->execute([$id]);
    $budget = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($budget) {
        $stmt = $pdo->prepare("SELECT bi.*, p.name as product_name 
                               FROM budget_items bi 
                               JOIN products p ON bi.product_id = p.id 
                               WHERE bi.budget_id = ?");
        $stmt->execute([$id]);
        $budget['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return $budget;
}

function createBudget($data) {
    global $pdo;
    try {
        $pdo->beginTransaction();
        
        // Generar un token aleatorio
        $token = bin2hex(random_bytes(32));

        // Calcular el total del presupuesto
        $total = 0;
        foreach ($data['items'] as $item) {
            $price = floatval($item['price']);
            $quantity = intval($item['quantity']);
            $subtotal = $price * $quantity;
            $total += $subtotal;
        }

        $stmt = $pdo->prepare("INSERT INTO budgets (customer_id, user_id, total_amount, validity_period, notes, view_token) 
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['customer_id'],
            $data['user_id'],
            $total,  // Utilizar el total calculado
            $data['validity_period'],
            $data['notes'],
            $token
        ]);
        $budgetId = $pdo->lastInsertId();
        
        $stmt = $pdo->prepare("INSERT INTO budget_items (budget_id, product_id, quantity, price) 
                               VALUES (?, ?, ?, ?)");
        foreach ($data['items'] as $item) {
            $price = floatval($item['price']);
            $quantity = intval($item['quantity']);
            
            $stmt->execute([
                $budgetId,
                $item['product_id'],
                $quantity,
                $price
            ]);
        }
        
        $pdo->commit();
        return $budgetId;
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log($e->getMessage());
        return false;
    }
}

function updateBudget($data) {
    global $pdo;
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("UPDATE budgets SET customer_id = ?, total_amount = ?, validity_period = ?, notes = ? 
                               WHERE id = ?");
        $stmt->execute([
            $data['customer_id'],
            $data['total_amount'],
            $data['validity_period'],
            $data['notes'],
            $data['id']
        ]);
        $stmt = $pdo->prepare("DELETE FROM budget_items WHERE budget_id = ?");
        $stmt->execute([$data['id']]);
        $stmt = $pdo->prepare("INSERT INTO budget_items (budget_id, product_id, quantity, price) 
                               VALUES (?, ?, ?, ?)");
        foreach ($data['items'] as $item) {
            $stmt->execute([
                $data['id'],
                $item['product_id'],
                $item['quantity'],
                $item['price']
            ]);
        }
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log($e->getMessage());
        return false;
    }
}

function deleteBudget($id) {
    global $pdo;
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("DELETE FROM budget_items WHERE budget_id = ?");
        $stmt->execute([$id]);
        $stmt = $pdo->prepare("DELETE FROM budgets WHERE id = ?");
        $stmt->execute([$id]);
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log($e->getMessage());
        return false;
    }
}

function getAllCustomers() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM customers ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllProducts() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM products ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function changeBudgetStatus($budgetId, $newStatus) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE budgets SET status = ? WHERE id = ?");
    return $stmt->execute([$newStatus, $budgetId]);
}