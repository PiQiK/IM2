<?php
include 'db.php';

$orderId = $_POST['order_id'];
$newStatus = $_POST['order_status'];

// Get the payment status of the order
$stmt = $conn->prepare("SELECT payment_status FROM orders WHERE id = ?");
$stmt->bind_param("i", $orderId);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$paymentStatus = $order['payment_status'];

// Force order_status to 'Pending' if payment is not done
if ($paymentStatus === 'Pending') {
  $newStatus = 'Pending';
}

// Update order status
$update = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
$update->bind_param("si", $newStatus, $orderId);
$update->execute();

header("Location: view_orders.php");
exit;
?>