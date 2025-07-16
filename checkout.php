<?php
session_start();
include 'connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit();
}

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit();
}

// Fetch username from DB
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'User not found']);
    exit();
}

$username = $res->fetch_assoc()['username'];

// Decode JSON body from fetch()
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$cart = $data['cart'] ?? [];
$orderType = $data['order_type'] ?? '';

if (!is_array($cart) || empty($cart)) {
    echo json_encode(['success' => false, 'error' => 'Cart is empty or invalid']);
    exit();
}

if (!in_array($orderType, ['Dine In', 'Take Out'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid order type']);
    exit();
}

$total = 0;
foreach ($cart as $item) {
    if (!isset($item['id'], $item['qty'], $item['price']) || $item['qty'] < 1) {
        echo json_encode(['success' => false, 'error' => 'Invalid item structure']);
        exit();
    }
    $total += $item['price'] * $item['qty'];
}

$orderJSON = json_encode($cart);
$stmt = $conn->prepare("INSERT INTO orders (customer_name, order_items, total_price, order_type) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssds", $username, $orderJSON, $total, $orderType);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'order_id' => $stmt->insert_id]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}
