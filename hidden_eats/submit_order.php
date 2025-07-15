<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['orderNumber'])) {
  http_response_code(400);
  echo json_encode(["status" => "error", "message" => "Invalid input or missing fields"]);
  exit;
}

$name = $data['orderNumber']; // used as a customer name
$items = json_encode($data['cart']);
$total = $data['total'];
$type = $data['orderType'];
$payment = $data['paymentMethod'];

// Force order_status to "Pending" if payment is still pending
$status = ($payment === 'Pending') ? 'Pending' : 'Preparing';
$created = date('Y-m-d H:i:s');

$stmt = $conn->prepare("INSERT INTO orders (customer_name, order_items, total_price, order_type, payment_status, order_status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
if (!$stmt) {
  echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
  exit;
}

$stmt->bind_param("ssdssss", $name, $items, $total, $type, $payment, $status, $created);

if ($stmt->execute()) {
  echo json_encode(["status" => "success"]);
} else {
  echo json_encode(["status" => "error", "message" => $stmt->error]);
}
