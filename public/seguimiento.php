<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/service_functions.php';
require_once __DIR__ . '/../includes/phpqrcode/qrlib.php';

// Inicializar variables
$error = '';
$order = null;
$status_history = [];
$order_notes_data = [];
$qr_code = '';

$company_info = getCompanyInfo();
$logo_path = $company_info['logo_path'];

// Función para generar el código QR
function generateQRCode($order_number) {
    if (empty($order_number)) return '';
    
    $url = url("seguimiento.php?order_number=" . urlencode($order_number));
    $tempDir = __DIR__ . '/../includes/qr/';
    $fileName = 'qr_' . $order_number . '.png';
    $filePath = $tempDir . $fileName;
    
    if (!file_exists($filePath)) {
        QRcode::png($url, $filePath, QR_ECLEVEL_L, 3, 2);
    }
    
    $imageData = base64_encode(file_get_contents($filePath));
    return 'data:image/png;base64,' . $imageData;
}

// Estado Financiero
function getOrderPartsTotalCost($order_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT SUM(cost * quantity) as total_cost FROM order_parts WHERE order_id = ?");
    $stmt->execute([$order_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total_cost'] ?? 0;
}

// Manejar la búsqueda de orden
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['order_number'])) {
    $order_number = $_POST['order_number'] ?? $_GET['order_number'] ?? '';

    if (empty($order_number)) {
        $error = "Por favor, ingrese el número de orden.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM service_orders WHERE order_number = ?");
            $stmt->execute([$order_number]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $order = getServiceOrder($result['id']);
                $status_history = getOrderStatusHistory($result['id']);
                $order_notes_data = getOrderNotes($result['id']);
                $qr_code = generateQRCode($order['order_number']);
            } else {
                $error = "No se encontró ninguna orden con ese número.";
            }
        } catch (PDOException $e) {
            $error = "Error al buscar la orden: " . $e->getMessage();
        }
    }
}

// Manejar la impresión de la orden
if (isset($_GET['action']) && $_GET['action'] === 'print' && isset($_GET['id'])) {
    $order_id = $_GET['id'];
    try {
        $order = getServiceOrder($order_id);
        $order_parts = getOrderParts($order_id);
        
        if ($order) {
            include __DIR__ . '/../views/services/print_cliente.php';
            exit;
        } else {
            $error = "No se encontró la orden para imprimir.";
        }
    } catch (Exception $e) {
        $error = "Error al obtener la orden para imprimir: " . $e->getMessage();
    }
}

// Verificar si el último estado es 'cerrado'
$is_last_status_closed = false;
if (!empty($status_history)) {
    $last_status = end($status_history);
    $is_last_status_closed = strtolower($last_status['status']) === 'cerrado';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguimiento de Orden de Servicio</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            font-size: 18px; 
            margin: 0;
            padding: 0;
        }
        @media print {
            body { 
                font-size: 16px; 
            }
            .container { 
                width: 100%; 
                max-width: none; 
                padding: 10mm; 
                margin: 0; 
            }
            .no-print { display: none !important; }
            .card { border: none; }
            .card-header { background: none; border-bottom: 1px solid #ddd; }
            .table td, .table th { padding: 0.5rem; }
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        .company-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .company-logo img {
            max-width: 180px;
            height: auto;
        }
        .company-info {
            margin-left: 20px;
        }
        .company-info h2 { font-size: 2em; margin: 0; }
        .company-info p { margin: 0; font-size: 1.2em; }
        .order-details-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .order-details, .order-qr {
            width: 48%;
        }
        .qr-code { max-width: 600px; height: auto; }
        table { font-size: 1.1em; }
        .blinking {
            animation: blink 1s linear infinite;
            font-weight: bold;
        }
        @keyframes blink {
            0% { opacity: 1; }
            50% { opacity: 0; }
            100% { opacity: 1; }
        }
        h4, h5 { 
            font-size: 1.5em;
            margin-top: 20px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="company-header">
            <div class="company-logo">
                <img src="<?php echo htmlspecialchars($logo_path); ?>" alt="Logo">
            </div>
            <div class="company-info">
                <h2><?php echo htmlspecialchars($company_info['name']); ?></h2>
                <p><?php echo htmlspecialchars($company_info['address']); ?></p>
                <p>Tel: <?php echo htmlspecialchars($company_info['phone']); ?></p>
                <p>Email: <?php echo htmlspecialchars($company_info['email']); ?></p>
                <p>Web: <?php echo htmlspecialchars($company_info['website']); ?></p>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!$order): ?>
            <form method="POST" class="mb-4 no-print">
                <div class="form-group">
                    <label for="order_number">Número de Orden</label>
                    <input type="text" class="form-control form-control-lg" id="order_number" name="order_number" required>
                </div>
                <button type="submit" class="btn btn-primary btn-lg">Buscar Orden</button>
            </form>
        <?php else: ?>
            <div class="order-details-container">
                <div class="order-details">
                    <h4>Orden #<?php echo htmlspecialchars($order['order_number']); ?></h4>
                    <p><strong>Cliente:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                    <p><strong>Dispositivo:</strong> <?php echo htmlspecialchars($order['brand'] . ' ' . $order['model']); ?></p>
                    <p><strong>Estado:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
                    <p><strong>Fecha:</strong> <?php echo htmlspecialchars($order['created_at']); ?></p>
                </div>
                <div class="order-qr">
                    <img src="<?php echo $qr_code; ?>" alt="QR" class="qr-code">
                </div>
            </div>

            <h5>Servicios</h5>
            <table class="table">
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th>Costo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order['items'] as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['description']); ?></td>
                        <td><?php echo number_format($item['cost'], 2); ?> ARS</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h5>Piezas Utilizadas</h5>
            <?php
            $order_parts = getOrderParts($order['id']);
            $total_parts_cost = 0;
            ?>
            <?php if (empty($order_parts)): ?>
                <p>No se utilizaron piezas en esta orden.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Cantidad</th>
                            <th>Costo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order_parts as $part): 
                            $total_parts_cost += $part['cost'] * $part['quantity'];
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($part['part_name']); ?></td>
                            <td><?php echo htmlspecialchars($part['quantity']); ?></td>
                            <td><?php echo number_format($part['cost'], 2); ?> ARS</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <h5>Resumen Financiero</h5>
            <?php
            $new_balance = $order['total_amount'] + $total_parts_cost - $order['prepaid_amount'];
            ?>
            <table class="table">
                <tbody>
                    <tr>
                        <th>Total:</th>
                        <td><?php echo number_format($order['total_amount'], 2); ?> ARS</td>
                    </tr>
                    <tr>
                        <th>Monto Prepago:</th>
                        <td><?php echo number_format($order['prepaid_amount'], 2); ?> ARS</td>
                    </tr>
                    <tr>
                        <th>Piezas Utilizadas:</th>
                        <td><?php echo number_format($total_parts_cost, 2); ?> ARS</td>
                    </tr>
                    <tr>
                        <th>Saldo:</th>
                        <td><?php echo number_format($new_balance, 2); ?> ARS</td>
                    </tr>
                </tbody>
            </table>

            <h5>Historial de Estados</h5>
            <table class="table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($status_history as $history): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($history['changed_at']); ?></td>
                        <td><?php echo htmlspecialchars($history['status']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h5>Notas e Imágenes de la Orden</h5>
            <?php if (empty($order_notes_data['textNotes']) && empty($order_notes_data['images'])): ?>
                <p>No hay notas ni imágenes para esta orden.</p>
            <?php else: ?>
                <?php if (!empty($order_notes_data['images'])): ?>
                    <h6>Imágenes</h6>
                    <div class="d-flex flex-wrap mb-3">
                        <?php foreach ($order_notes_data['images'] as $image): ?>
                            <div class="mr-2 mb-2">
                                <img src="<?php echo htmlspecialchars(url($image['path'])); ?>" 
                                     alt="Imagen de la orden" 
                                     class="img-thumbnail" 
                                     style="max-width: 100px; max-height: 100px; cursor: pointer;"
                                     onclick="openImageModal('<?php echo htmlspecialchars(url($image['path'])); ?>')">
                                <small class="d-block text-muted">
                                    <?php echo date('d/m/Y H:i', strtotime($image['created_at'])); ?>
                                </small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($order_notes_data['textNotes'])): ?>
                    <h6>Notas</h6>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Usuario</th>
                                <th>Nota</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order_notes_data['textNotes'] as $note): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($note['created_at']))); ?></td>
                                <td><?php echo htmlspecialchars($note['user_name']); ?></td>
                                <td><?php echo nl2br(htmlspecialchars($note['note'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            <?php endif; ?>

            <div class="mt-4 no-print">
                <button onclick="window.print()" class="btn btn-secondary btn-lg">Imprimir Seguimiento</button>
                <a href="?action=print&id=<?php echo $order['id']; ?>" class="btn btn-secondary btn-lg" target="_blank">Imprimir Orden</a>
                <a href="seguimiento.php" class="btn btn-secondary btn-lg">Buscar Otra Orden</a>
            </div>
        <?php endif; ?>
    </div>

    <div class="modal fade" id="orderClosedModal" tabindex="-1" role="dialog" aria-labelledby="orderClosedModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderClosedModalLabel">Estado de la Orden</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img src="/public/assets/img/feliz1.gif" alt="Orden completada" class="img-fluid mb-3" style="max-width: 200px;">
                    <p id="orderClosedMessage" class="text-danger blinking" style="font-size: 24px;">TRABAJO FINALIZADO, PUEDES PASAR A RETIRAR TU EQUIPO!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Imagen de la nota</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="Imagen de la nota" style="max-width: 100%; height: auto;">
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php
        if ($is_last_status_closed) {
            echo "$('#orderClosedModal').modal('show');";
        }
        ?>
    });

    function openImageModal(imageSrc) {
        document.getElementById('modalImage').src = imageSrc;
        $('#imageModal').modal('show');
    }
    </script>
</body>
</html>