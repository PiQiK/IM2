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
  <meta charset="UTF-8" />
  <title>Order Confirmation</title>
  <link rel="stylesheet" href="CSS/confirm.css" />
</head>
<body>
  <div class="confirmation-wrapper">
    <div class="confirmation-box">
      <h1>✅ Order Confirmed</h1>
      <p class="order-id">Order ID: #<?= $order['order_id'] ?></p>
      <p class="customer">Customer: <?= htmlspecialchars($order['customer_name']) ?></p>
      <p class="details">Order Type: <?= $order['order_type'] ?></p>
      <p class="details">Payment: <?= $order['payment_method'] ?> (<?= $order['payment_status'] ?>)</p>

      <ul class="item-list">
        <?php foreach ($items as $item): ?>
          <li>
            <span><?= htmlspecialchars($item['name']) ?> x<?= $item['qty'] ?></span>
            <span>₱<?= number_format($item['price'] * $item['qty'], 2) ?></span>
          </li>
        <?php endforeach; ?>
      </ul>

      <p class="total">Total: ₱<?= number_format($order['total_price'], 2) ?></p>
      <p class="status">Status: <?= $order['order_status'] ?></p>

      <a href="LoggedIn.php" class="back-btn">← Back to Menu</a>
    </div>
  </div>
</body>

</html>
