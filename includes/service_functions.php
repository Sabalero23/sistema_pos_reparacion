<?php
require_once __DIR__ . '/../config/config.php';

function getAllServiceOrders() {
    global $pdo;
    try {
        $sql = "SELECT so.*, c.name as customer_name, c.id as customer_id,
                       sd.brand, sd.model
                FROM service_orders so 
                JOIN customers c ON so.customer_id = c.id 
                LEFT JOIN service_devices sd ON so.id = sd.service_order_id
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

function getOrderNotes($orderId) {
    global $pdo;
    try {
        $sql = "SELECT son.*, u.name as user_name 
                FROM service_order_notes son 
                LEFT JOIN users u ON son.user_id = u.id 
                WHERE son.service_order_id = ? 
                ORDER BY son.created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$orderId]);
        $allNotes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $textNotes = [];
        $images = [];

        foreach ($allNotes as $note) {
            if (!empty($note['image_path'])) {
                $images[] = [
                    'path' => $note['image_path'],
                    'created_at' => $note['created_at'],
                    'user_name' => $note['user_name'] ?? 'Usuario desconocido'
                ];
            } elseif (!empty($note['note'])) {
                $textNotes[] = [
                    'id' => $note['id'],
                    'note' => $note['note'],
                    'user_name' => $note['user_name'] ?? 'Usuario desconocido',
                    'created_at' => $note['created_at']
                ];
            }
        }

        // Ordenar las imágenes por fecha de creación (la más reciente primero)
        usort($images, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return [
            'textNotes' => $textNotes,
            'images' => $images
        ];
    } catch (PDOException $e) {
        error_log("Error getting order notes: " . $e->getMessage());
        throw new Exception("Error al obtener las notas de la orden");
    }
}

function addOrderNoteWithImages($orderId, $userId, $note, $images) {
    global $pdo;
    try {
        $pdo->beginTransaction();

        // Insertar la nota principal
        $sql = "INSERT INTO service_order_notes (service_order_id, user_id, note) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$orderId, $userId, $note]);
        $noteId = $pdo->lastInsertId();

        // Procesar las imágenes
        if (!empty($images['name'][0])) {
            $uploadDir = __DIR__ . '/../public/uploads/service_notes/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $sql = "INSERT INTO service_order_notes (service_order_id, user_id, note, image_path) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);

            foreach ($images['tmp_name'] as $key => $tmp_name) {
                if ($images['error'][$key] == 0) {
                    $imageName = uniqid() . '_' . basename($images['name'][$key]);
                    $imagePath = '/uploads/service_notes/' . $imageName;
                    if (move_uploaded_file($tmp_name, $uploadDir . $imageName)) {
                        $stmt->execute([$orderId, $userId, "Imagen adjunta a la nota #$noteId", $imagePath]);
                    }
                }
            }
        }

        $pdo->commit();
        return true;
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Error adding order note with images: " . $e->getMessage());
        throw new Exception("Error al agregar la nota a la orden");
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

function getDeviceInfo($orderId) {
    global $pdo;
    try {
        $sql = "SELECT brand, model FROM service_devices WHERE service_order_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting device info: " . $e->getMessage());
        return ['brand' => '', 'model' => ''];
    }
}

function updateServiceOrder($data) {
    global $pdo;
    try {
        $pdo->beginTransaction();

        // Obtener el monto prepagado anterior
        $stmt = $pdo->prepare("SELECT prepaid_amount FROM service_orders WHERE id = ?");
        $stmt->execute([$data['id']]);
        $oldPrepaidAmount = $stmt->fetchColumn();

        // Actualizar la tabla service_orders
        $sql = "UPDATE service_orders SET 
                customer_id = ?, 
                warranty = ?, 
                total_amount = ?, 
                prepaid_amount = ?, 
                balance = ?
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $balance = $data['total_amount'] - $data['prepaid_amount'];
        $stmt->execute([
            $data['customer_id'],
            $data['warranty'],
            $data['total_amount'],
            $data['prepaid_amount'],
            $balance,
            $data['id']
        ]);

        // Actualizar la tabla service_devices
        $sql = "UPDATE service_devices SET 
                brand = ?, 
                model = ?, 
                serial_number = ?
                WHERE service_order_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['brand'],
            $data['model'],
            $data['serial_number'],
            $data['id']
        ]);

        // Eliminar los servicios existentes
        $sql = "DELETE FROM service_items WHERE service_order_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$data['id']]);

        // Insertar los nuevos servicios
        $sql = "INSERT INTO service_items (service_order_id, description, cost) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        foreach ($data['services'] as $service) {
            $stmt->execute([$data['id'], $service['description'], floatval($service['cost'])]);
        }

        $pdo->commit();
        return [
            'success' => true, 
            'message' => 'Orden de servicio actualizada con éxito.',
            'old_prepaid_amount' => $oldPrepaidAmount
        ];
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error updating service order: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error al actualizar la orden de servicio: ' . $e->getMessage()];
    }
}

function getServiceOrderForEdit($id) {
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
        error_log("Error getting service order for edit: " . $e->getMessage());
        throw new Exception("Error al obtener la orden de servicio para editar");
    }
}