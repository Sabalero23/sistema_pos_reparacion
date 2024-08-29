<?php
class POSFunctions {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getCurrentCashRegisterSession() {
        $stmt = $this->pdo->prepare("SELECT * FROM cash_register_sessions WHERE status = 'open' ORDER BY opening_date DESC LIMIT 1");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createSale($data) {
        $this->pdo->beginTransaction();
        
        try {
            if (empty($data['customer_id']) || empty($data['payment_method']) || empty($data['items']) || !isset($data['total_amount'])) {
                throw new Exception("Datos de venta incompletos");
            }

            $validPaymentMethods = ['efectivo', 'tarjeta', 'transferencia', 'credito', 'otros'];
            if (!in_array($data['payment_method'], $validPaymentMethods)) {
                throw new Exception("Método de pago no válido");
            }

            $currentCashRegisterSession = $this->getCurrentCashRegisterSession();
            if (!$currentCashRegisterSession) {
                throw new Exception("No hay una sesión de caja abierta. Por favor, abra una caja antes de realizar una venta.");
            }

            $currentDateTime = date('Y-m-d H:i:s');

            $stmt = $this->pdo->prepare("INSERT INTO sales (customer_id, user_id, sale_date, total_amount, payment_method, cash_register_session_id, status) 
                                   VALUES (:customer_id, :user_id, :sale_date, :total_amount, :payment_method, :cash_register_session_id, 'completado')");
            $stmt->execute([
                ':customer_id' => $data['customer_id'],
                ':user_id' => $_SESSION['user_id'],
                ':sale_date' => $currentDateTime,
                ':total_amount' => $data['total_amount'],
                ':payment_method' => $data['payment_method'],
                ':cash_register_session_id' => $currentCashRegisterSession['id']
            ]);
            $saleId = $this->pdo->lastInsertId();

            $stmt = $this->pdo->prepare("INSERT INTO sale_items (sale_id, product_id, quantity, price) 
                                   VALUES (:sale_id, :product_id, :quantity, :price)");
            foreach ($data['items'] as $item) {
                if (empty($item['product_id']) || empty($item['quantity']) || !isset($item['price'])) {
                    throw new Exception("Datos de producto incompletos");
                }
                $stmt->execute([
                    ':sale_id' => $saleId,
                    ':product_id' => $item['product_id'],
                    ':quantity' => $item['quantity'],
                    ':price' => $item['price']
                ]);

                $this->updateStock($item['product_id'], -$item['quantity'], 'venta', $saleId, 'Venta de producto');
            }

            $this->pdo->commit();
            error_log("Venta creada exitosamente. ID: " . $saleId);
            return ['success' => true, 'message' => 'Venta creada exitosamente.', 'sale_id' => $saleId];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error en createSale: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al crear la venta: ' . $e->getMessage()];
        }
    }

    public function searchCustomers($term) {
        $stmt = $this->pdo->prepare("SELECT id, name FROM customers WHERE name LIKE :term ORDER BY name LIMIT 10");
        $stmt->execute([':term' => "%$term%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchProducts($term) {
        $stmt = $this->pdo->prepare("SELECT id, name, price FROM products WHERE name LIKE :term AND stock_quantity > 0 ORDER BY name LIMIT 10");
        $stmt->execute([':term' => "%$term%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function updateStock($productId, $quantity, $movementType, $referenceId, $notes) {
        try {
            $stmt = $this->pdo->prepare("UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?");
            $stmt->execute([$quantity, $productId]);

            $stmt = $this->pdo->prepare("INSERT INTO stock_movements (product_id, quantity, movement_type, reference_id, notes, user_id) 
                                   VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$productId, $quantity, $movementType, $referenceId, $notes, $_SESSION['user_id'] ?? null]);
        } catch (Exception $e) {
            error_log("Error en updateStock: " . $e->getMessage());
            throw $e;
        }
    }

    public function getSaleById($id) {
        $stmt = $this->pdo->prepare("SELECT s.*, c.name as customer_name, u.name as user_name 
                               FROM sales s 
                               LEFT JOIN customers c ON s.customer_id = c.id 
                               LEFT JOIN users u ON s.user_id = u.id 
                               WHERE s.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getSaleItems($saleId) {
        $stmt = $this->pdo->prepare("SELECT si.*, p.name as product_name 
                               FROM sale_items si 
                               JOIN products p ON si.product_id = p.id 
                               WHERE si.sale_id = ?");
        $stmt->execute([$saleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cancelSale($id) {
        try {
            $this->pdo->beginTransaction();

            $sale = $this->getSaleById($id);
            if (!$sale) {
                throw new Exception("Venta no encontrada");
            }

            if ($sale['status'] === 'cancelado') {
                throw new Exception("La venta ya ha sido cancelada");
            }

            $items = $this->getSaleItems($id);

            $stmt = $this->pdo->prepare("UPDATE sales SET status = 'cancelado' WHERE id = ?");
            $stmt->execute([$id]);

            foreach ($items as $item) {
                $this->updateStock($item['product_id'], $item['quantity'], 'cancelacion_venta', $id, 'Cancelación de venta');
            }

            $this->pdo->commit();
            return ['success' => true, 'message' => 'Venta cancelada exitosamente.'];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error en cancelSale: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al cancelar la venta: ' . $e->getMessage()];
        }
    }

    public function getAllProducts() {
        $stmt = $this->pdo->query("SELECT * FROM products WHERE stock_quantity > 0 ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllCustomers() {
        $stmt = $this->pdo->query("SELECT * FROM customers ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>