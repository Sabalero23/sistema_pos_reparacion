<?php
class SaleFunctions {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllSales() {
        $stmt = $this->pdo->query("SELECT s.*, c.name as customer_name, u.name as user_name 
                             FROM sales s 
                             LEFT JOIN customers c ON s.customer_id = c.id 
                             LEFT JOIN users u ON s.user_id = u.id 
                             ORDER BY s.sale_date DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    public function createSale($data) {
        $transactionStarted = false;
        try {
            $this->pdo->beginTransaction();
            $transactionStarted = true;

            $items = $this->getSaleItems($id);

            $stmt = $this->pdo->prepare("UPDATE sales SET status = 'cancelado' WHERE id = ?");
            $stmt->execute([$id]);

            foreach ($items as $item) {
                $this->updateStock($item['product_id'], $item['quantity'], 'cancelacion_venta', $id, 'Cancelación de venta');
            }

            $this->pdo->commit();
            return ['success' => true, 'message' => 'Venta cancelada exitosamente.'];
        } catch (PDOException $e) {
            $this->pdo->rollBack();
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

    public function getCurrentCashRegisterSession() {
        $stmt = $this->pdo->prepare("SELECT * FROM cash_register_sessions WHERE status = 'open' ORDER BY opening_date DESC LIMIT 1");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function updateStock($productId, $quantity, $movementType, $referenceId, $details) {
        $stmt = $this->pdo->prepare("UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?");
        $stmt->execute([$quantity, $productId]);

        $stmt = $this->pdo->prepare("INSERT INTO stock_movements (product_id, quantity, movement_type, reference_id, details) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$productId, $quantity, $movementType, $referenceId, $details]);
    }

    private function updateCustomerAccountBalance($customerId, $amount) {
        $stmt = $this->pdo->prepare("UPDATE customers SET account_balance = account_balance + ? WHERE id = ?");
        $stmt->execute([$amount, $customerId]);
    }

    private function registerCashTransaction($cashRegisterSessionId, $amount, $type, $description) {
        $stmt = $this->pdo->prepare("INSERT INTO cash_transactions (cash_register_session_id, amount, transaction_type, description) 
                               VALUES (?, ?, ?, ?)");
        $stmt->execute([$cashRegisterSessionId, $amount, $type, $description]);
    }
}
?>