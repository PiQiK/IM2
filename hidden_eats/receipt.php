<?php
include 'db.php';

if (!isset($_GET['id'])) {
  echo "Order ID missing!";
  exit();
}

$order_id = $_GET['id'];

$sql = "SELECT * FROM orders WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  echo "Order not found.";
  exit();
}

$order = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Receipt | HIDDEN EATS</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Raleway', sans-serif; }
  </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <div class="bg-white p-8 rounded-2xl shadow-xl max-w-md w-full text-center">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">🧾 Order Receipt</h2>

    <div class="text-left text-gray-700 space-y-3 mb-6">
      <p><strong>Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
      <p><strong>Items:</strong><br><?= nl2br(htmlspecialchars($order['order_items'])) ?></p>
      <p><strong>Total:</strong> ₱<?= number_format($order['total_price'], 2) ?></p>
      <p><strong>Order Type:</strong> <?= htmlspecialchars($order['order_type']) ?></p>
      <p><strong>Date:</strong> <?= $order['created_at'] ?></p>
      <p><strong>Payment Status:</strong>
        <span class="font-semibold <?= $order['payment_status'] === 'Paid' ? 'text-green-600' : 'text-red-600' ?>">
          <?= $order['payment_status'] ?>
        </span>
      </p>
    </div>

    <?php if ($order['payment_status'] === 'Unpaid'): ?>
      <div class="mb-4">
        <p class="mb-2 font-semibold">Scan to pay via GCash:</p>
        <img src="assets/qr.png" alt="GCash QR Code" class="w-48 mx-auto border rounded-lg">
      </div>

      <form action="confirm_payment.php" method="POST">
        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-xl transition">
          ✅ I have paid
        </button>
      </form>
    <?php else: ?>
      <div class="mt-4">
        <p class="text-green-600 font-semibold text-lg">✅ Payment Confirmed</p>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>