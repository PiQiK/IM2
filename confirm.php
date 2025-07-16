<?php
include 'connect.php';

$order_id = $_GET['order_id'] ?? 0;
if (!$order_id || !is_numeric($order_id)) {
    echo "Invalid Order ID.";
    exit();
}

$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Order not found.";
    exit();
}

$order = $result->fetch_assoc();
$items = json_decode($order['order_items'], true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Confirmation</title>
  <link rel="stylesheet" href="CSS/confirm.css">
</head>
<body>
  <h1>✅ Order Placed!</h1>
  <p>Thank you, <strong><?= htmlspecialchars($order['customer_name']) ?></strong>!</p>
  <p><strong>Order ID:</strong> #<?= $order['order_id'] ?></p>
  <p><strong>Order Type:</strong> <?= htmlspecialchars($order['order_type']) ?></p>
  <p><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>


  <ul>
    <?php foreach ($items as $item): ?>
      <li>
        <?= htmlspecialchars($item['name']) ?> x<?= $item['qty'] ?> — ₱<?= number_format($item['price'] * $item['qty'], 2) ?>
      </li>
    <?php endforeach; ?>
  </ul>

  <p><strong>Total:</strong> ₱<?= number_format($order['total_price'], 2) ?></p>
  <p><strong>Status:</strong> <?= htmlspecialchars($order['order_status']) ?></p>
</body>
</html>