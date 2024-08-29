<?php
require_once __DIR__ . '/../config/config.php';

function getAllServiceOrders() {
    global $pdo;
    try {
        $sql = "SELECT so.*, c.name as customer_name, c.id as customer_id 
                FROM service_orders so 
                JOIN customers c ON so.customer_id = c.id 
                ORDER BY so.created_at DESC";
        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting all service orders: " . $e->getMessage());
        throw new Exception("Error al obtener las órdenes de servicio");
    }
}

function getServiceOrder($id) {
    global $pdo;
    try {
        $sql = "SELECT so.*, c.name as customer_name, c.email, c.phone, c.address, 
                       sd.brand, sd.model, sd.serial_number
                FROM service_orders so 
                JOIN customers c ON so.customer_id = c.id 
                JOIN service_devices sd ON so.id = sd.service_order_id 
                WHERE so.id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($order) {
            $order['items'] = getServiceItems($id);
        }

        return $order;
    } catch (PDOException $e) {
        error_log("Error getting service order: " . $e->getMessage());
        throw new Exception("Error al obtener la orden de servicio");
    }
}

function getServiceItems($orderId) {
    global $pdo;
    try {
        $sql = "SELECT * FROM service_items WHERE service_order_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting service items: " . $e->getMessage());
        throw new Exception("Error al obtener los items de la orden de servicio");
    }
}

function createServiceOrder($data) {
    global $pdo;
    try {
        $pdo->beginTransaction();

        // Obtener el nombre del cliente
        $stmt = $pdo->prepare("SELECT name FROM customers WHERE id = ?");
        $stmt->execute([$data['customer_id']]);
        $customerName = $stmt->fetchColumn();

        // Asegurarse de que total_amount y prepaid_amount sean números
        $totalAmount = floatval($data['total_amount']);
        $prepaidAmount = floatval($data['prepaid_amount']);
        
        // Calcular el balance
        $balance = $totalAmount - $prepaidAmount;

        // Insertar en service_orders
        $sql = "INSERT INTO service_orders (customer_id, warranty, total_amount, prepaid_amount, balance, order_number) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $orderNumber = generateOrderNumber();
        $stmt->execute([
            $data['customer_id'], 
            $data['warranty'], 
            $totalAmount, 
            $prepaidAmount, 
            $balance, 
            $orderNumber
        ]);
        $orderId = $pdo->lastInsertId();

        // Insertar en service_devices
        $sql = "INSERT INTO service_devices (service_order_id, brand, model, serial_number) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$orderId, $data['brand'], $data['model'], $data['serial_number']]);

        // Insertar en service_items
        $sql = "INSERT INTO service_items (service_order_id, description, cost) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        foreach ($data['services'] as $service) {
            $stmt->execute([$orderId, $service['description'], floatval($service['cost'])]);
        }

        $pdo->commit();
        return [
            'success' => true, 
            'message' => 'Orden de servicio creada con éxito.', 
            'order_id' => $orderId,
            'order_number' => $orderNumber,
            'customer_name' => $customerName,
            'prepaid_amount' => $prepaidAmount
        ];
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error creating service order: " . $e->getMessage());
        return [
            'success' => false, 
            'message' => 'Error al crear la orden de servicio: ' . $e->getMessage()
        ];
    }
}

function updateServiceOrderStatus($orderId, $newStatus, $notes, $userId) {
    global $pdo;
    try {
        $pdo->beginTransaction();

        $sql = "UPDATE service_orders SET status = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$newStatus, $orderId]);

        $sql = "INSERT INTO service_order_status_history (service_order_id, status, changed_by, notes) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$orderId, $newStatus, $userId, $notes]);

        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error updating service order status: " . $e->getMessage());
        return false;
    }
}

function getOrderStatusHistory($orderId) {
    global $pdo;
    try {
        $sql = "SELECT sosh.*, u.name as changed_by_name 
                FROM service_order_status_history sosh 
                JOIN users u ON sosh.changed_by = u.id 
                WHERE sosh.service_order_id = ? 
                ORDER BY sosh.changed_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting order status history: " . $e->getMessage());
        throw new Exception("Error al obtener el historial de estados de la orden");
    }
}

function addOrderNoteWithImage($orderId, $userId, $note, $image) {
    global $pdo;
    try {
        $pdo->beginTransaction();

        $imagePath = null;
        if ($image && $image['error'] == 0) {
            $uploadDir = __DIR__ . '/../public/uploads/service_notes/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $imageName = uniqid() . '_' . basename($image['name']);
            $imagePath = '/uploads/service_notes/' . $imageName;
            move_uploaded_file($image['tmp_name'], $uploadDir . $imageName);
        }

        $sql = "INSERT INTO service_order_notes (service_order_id, user_id, note, image_path) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$orderId, $userId, $note, $imagePath]);

        $pdo->commit();
        return true;
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Error adding order note with image: " . $e->getMessage());
        throw new Exception("Error al agregar la nota a la orden");
    }
}

function getOrderNotes($orderId) {
    global $pdo;
    try {
        $sql = "SELECT son.*, u.name as user_name, son.image_path 
                FROM service_order_notes son 
                JOIN users u ON son.user_id = u.id 
                WHERE son.service_order_id = ? 
                ORDER BY son.created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting order notes: " . $e->getMessage());
        throw new Exception("Error al obtener las notas de la orden");
    }
}

function addOrderPart($orderId, $partData) {
    global $pdo;
    try {
        $sql = "INSERT INTO service_parts (service_order_id, part_name, part_number, quantity, cost) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$orderId, $partData['part_name'], $partData['part_number'], $partData['quantity'], $partData['cost']]);
    } catch (PDOException $e) {
        error_log("Error adding order part: " . $e->getMessage());
        throw new Exception("Error al agregar la pieza a la orden");
    }
}

function getOrderParts($orderId) {
    global $pdo;
    try {
        $sql = "SELECT * FROM service_parts WHERE service_order_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting order parts: " . $e->getMessage());
        throw new Exception("Error al obtener las piezas de la orden");
    }
}

function getActiveTermsAndConditions() {
    global $pdo;
    try {
        $sql = "SELECT content FROM service_terms WHERE active = 1 ORDER BY created_at DESC LIMIT 1";
        $stmt = $pdo->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['content'] : '';
    } catch (PDOException $e) {
        error_log("Error getting active terms and conditions: " . $e->getMessage());
        throw new Exception("Error al obtener los términos y condiciones activos");
    }
}

function generateOrderNumber() {
    return 'ORD' . date('YmdHis') . rand(100, 999);
}

function searchCustomers($term) {
    global $pdo;
    try {
        $sql = "SELECT id, name FROM customers WHERE name LIKE :term ORDER BY name LIMIT 10";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':term' => "%$term%"]);
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function($customer) {
            return [
                'id' => $customer['id'],
                'value' => $customer['name'],
                'label' => $customer['name']
            ];
        }, $customers);
    } catch (PDOException $e) {
        error_log("Error searching customers: " . $e->getMessage());
        throw new Exception("Error al buscar clientes");
    }
}

function addCustomer($data) {
    global $pdo;
    error_log("Datos recibidos en addCustomer: " . json_encode($data));

    if (empty($data['name'])) {
        return ['success' => false, 'message' => 'El nombre del cliente es requerido.'];
    }

    try {
        // Eliminar la restricción UNIQUE de la columna email si existe
        $pdo->exec("ALTER TABLE customers DROP INDEX email");
    } catch (PDOException $e) {
        // Si no existe el índice, ignoramos el error
        error_log("Nota: El índice UNIQUE en email no existía o no se pudo eliminar. Esto es normal si ya se había eliminado antes.");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO customers (name, email, phone, address) VALUES (?, ?, ?, ?)");
        $stmt->bindParam(1, $data['name']);
        $stmt->bindParam(2, $data['email']);
        $stmt->bindParam(3, $data['phone']);
        $stmt->bindParam(4, $data['address']);

        $result = $stmt->execute();
        
        if ($result) {
            $newCustomerId = $pdo->lastInsertId();
            error_log("Cliente creado exitosamente con ID: " . $newCustomerId);
            return [
                'success' => true, 
                'message' => 'Cliente añadido exitosamente.',
                'customer_id' => $newCustomerId
            ];
        } else {
            $errorInfo = $stmt->errorInfo();
            error_log("Error al insertar: " . json_encode($errorInfo));
            return ['success' => false, 'message' => 'Error al añadir cliente: ' . $errorInfo[2]];
        }
    } catch (PDOException $e) {
        error_log("Excepción PDO en addCustomer: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error de base de datos al crear el cliente: ' . $e->getMessage()];
    }
}