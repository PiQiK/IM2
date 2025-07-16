<?php
session_start();
include 'connect.php';

header('Content-Type: application/json');

// Read JSON payload
$data = json_decode(file_get_contents('php://input'), true);

// Validate basic structure
if (!isset($data['cart'], $data['order_type'], $data['payment_method']) || !is_array($data['cart']) || count($data['cart']) === 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid or empty cart']);
    exit();
}

$cart = $data['cart'];
$orderType = $data['order_type'];
$paymentMethod = $data['payment_method'];
$customerName = isset($_SESSION['user_id']) ? 'User #' . $_SESSION['user_id'] : 'Guest';

$allowedTypes = ['Dine In', 'Take Out'];
$allowedPayments = ['Cash', 'GCash'];

if (!in_array($orderType, $allowedTypes) || !in_array($paymentMethod, $allowedPayments)) {
    echo json_encode(['success' => false, 'error' => 'Invalid order type or payment method']);
    exit();
}

// Calculate total
$total = 0;
foreach ($cart as $item) {
    if (!isset($item['id'], $item['qty'], $item['price']) || $item['qty'] < 1) {
        echo json_encode(['success' => false, 'error' => 'Invalid cart item']);
        exit();
    }
    $total += $item['qty'] * $item['price'];
}

$orderItemsJson = json_encode($cart);

// Save to database
$stmt = $conn->prepare("INSERT INTO orders (customer_name, order_items, total_price, order_type, payment_method, payment_status) VALUES (?, ?, ?, ?, ?, 'Unpaid')");
$stmt->bind_param("ssdss", $customerName, $orderItemsJson, $total, $orderType, $paymentMethod);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'order_id' => $stmt->insert_id]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}
?>
