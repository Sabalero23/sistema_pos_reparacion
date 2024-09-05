<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/utils.php';

// Verificar si se proporcionó un ID de presupuesto
$budgetId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if (!$budgetId) {
    die("ID de presupuesto no proporcionado");
}

$budget = getBudgetById($budgetId);

if (!$budget) {
    die("Presupuesto no encontrado");
}

$companyInfo = getCompanyInfo();

require_once __DIR__ . '/../includes/tfpdf/tfpdf.php';

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