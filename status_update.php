<?php
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);
    $query = "UPDATE orders SET order_status = 'Completed' WHERE order_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $order_id);
    
    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }
}
