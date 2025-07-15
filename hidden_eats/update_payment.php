<?php
include 'db.php';

$orderId = $_POST['order_id'];
$newPaymentStatus = $_POST['payment_status'];

// Update payment status
$stmt = $conn->prepare("UPDATE orders SET payment_status = ? WHERE id = ?");
$stmt->bind_param("si", $newPaymentStatus, $orderId);
$stmt->execute();

// Update order status depending on payment
if ($newPaymentStatus === 'Pending') {
    // Force order status to Pending if payment is Pending
    $updateStatus = $conn->prepare("UPDATE orders SET order_status = 'Pending' WHERE id = ?");
    $updateStatus->bind_param("i", $orderId);
    $updateStatus->execute();
} else {
    // If newly marked as Paid, optionally auto-progress to "Preparing" only if still in "Pending"
    $check = $conn->prepare("SELECT order_status FROM orders WHERE id = ?");
    $check->bind_param("i", $orderId);
    $check->execute();
    $result = $check->get_result();
    $order = $result->fetch_assoc();

    if ($order['order_status'] === 'Pending') {
        $autoProgress = $conn->prepare("UPDATE orders SET order_status = 'Preparing' WHERE id = ?");
        $autoProgress->bind_param("i", $orderId);
        $autoProgress->execute();
    }
}

// Redirect back
header("Location: view_orders.php");
exit;
?>