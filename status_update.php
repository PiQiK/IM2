<?php
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);
    $query = "UPDATE orders SET order_status = 'Completed' WHERE order_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $order_id);
    
    if ($stmt->execute()) {
        header("Location: admin.php");
    } else {
        echo "Error updating status.";
    }
}

if (isset($_POST['payment_id'])) {
    $order_id = intval($_POST['payment_id']);
    $stmt = $conn->prepare("UPDATE orders SET payment_status = 'Paid' WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    if ($stmt->execute()) {
        header("Location: admin.php");
        exit();
    } else {
        echo "Failed to update payment status.";
    }
}

?>
