<?php
include 'connect.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Handle order status update
    if (isset($_POST['order_id'])) {
        $order_id = intval($_POST['order_id']);
        $stmt = $conn->prepare("UPDATE orders SET order_status = 'Completed' WHERE order_id = ?");
        $stmt->bind_param("i", $order_id);

        if ($stmt->execute()) {
            header("Location: admin.php");
            exit();
        } else {
            echo "Error updating order status.";
            exit();
        }
    }

    // Handle payment status update
    if (isset($_POST['payment_id'])) {
        $order_id = intval($_POST['payment_id']);
        $stmt = $conn->prepare("UPDATE orders SET payment_status = 'Paid' WHERE order_id = ?");
        $stmt->bind_param("i", $order_id);

        if ($stmt->execute()) {
            header("Location: admin.php");
            exit();
        } else {
            echo "Error updating payment status.";
            exit();
        }
    }
}
?>
