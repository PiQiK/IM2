<?php
include 'db.php';
$result = $conn->query("
  SELECT * FROM orders 
  ORDER BY 
    FIELD(payment_status, 'Pending', 'Paid'), 
    FIELD(order_status, 'Pending', 'Preparing', 'Ready', 'Completed'),
    created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Orders | HIDDEN EATS</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Raleway', sans-serif; }
  </style>
</head>
<body class="bg-gray-50 min-h-screen p-8">
  <div class="max-w-6xl mx-auto bg-white p-6 rounded-2xl shadow-xl">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">📋 All Orders</h2>
    <div class="overflow-auto rounded-xl">
      <table class="min-w-full table-auto text-sm text-left border border-gray-200">
        <thead class="bg-orange-500 text-white">
          <tr>
            <th class="px-4 py-2">ID</th>
            <th class="px-4 py-2">Name</th>
            <th class="px-4 py-2">Items</th>
            <th class="px-4 py-2">Total</th>
            <th class="px-4 py-2">Type</th>
            <th class="px-4 py-2">Payment</th>
            <th class="px-4 py-2">Date</th>
            <th class="px-4 py-2">Status</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
          <tr class="border-b border-gray-200 hover:bg-gray-50">
            <td class="px-4 py-2"><?= $row['id'] ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($row['customer_name']) ?></td>
            <td class="px-4 py-2 whitespace-pre-line"><?= htmlspecialchars($row['order_items']) ?></td>
            <td class="px-4 py-2">₱<?= number_format($row['total_price'], 2) ?></td>
            <td class="px-4 py-2"><?= $row['order_type'] ?></td>
            <td class="px-4 py-2">
            <form action="update_payment.php" method="POST">
                <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                <select name="payment_status" onchange="this.form.submit()" class="rounded-md border px-2 py-1 text-sm font-semibold <?= $row['payment_status'] === 'Paid' ? 'text-green-600' : 'text-red-500' ?>">
                <?php
                    $paymentOptions = ['Pending', 'Paid'];
                    foreach ($paymentOptions as $option) {
                    $selected = ($row['payment_status'] === $option) ? 'selected' : '';
                    echo "<option value='$option' $selected>$option</option>";
                    }
                ?>
                </select>
            </form>
            </td>
            <td class="px-4 py-2"><?= $row['created_at'] ?></td>
            <td class="px-4 py-2">
              <form action="update_status.php" method="POST">
                <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                <?php
                  // Status to color classes
                  $statusColors = [
                    'Pending' => 'bg-red-100 text-red-700',  
                    'Preparing' => 'bg-yellow-100 text-yellow-800',
                    'Ready' => 'bg-green-100 text-green-800',
                    'Completed' => 'bg-blue-100 text-blue-800',
                  ];
                  $currentStatus = $row['order_status'];
                  $colorClass = $statusColors[$currentStatus] ?? 'bg-gray-100 text-gray-800';
                ?>
                <select name="order_status"
                        onchange="this.form.submit()"
                        class="rounded-md border border-gray-300 px-2 py-1 text-sm font-medium <?= $colorClass ?>">
                  <?php
                    $statuses = ['Pending', 'Preparing', 'Ready', 'Completed'];
                    foreach ($statuses as $status) {
                      $selected = ($currentStatus === $status) ? 'selected' : '';
                      echo "<option value='$status' $selected>$status</option>";
                    }
                  ?>
                </select>
              </form>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>