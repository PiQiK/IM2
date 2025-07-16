<?php
session_start();
include 'connect.php';

if (empty($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header("Location: index.php");
    exit();
}

// Update status if POSTed
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_order_id'])) {
    $id = intval($_POST['complete_order_id']);
    $stmt = $conn->prepare("UPDATE orders SET order_status = 'Completed' WHERE order_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

$stmt = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="CSS/admin.css">
</head>
<body>
  <header class="admin-header">
    <h1>🛠 HiddenEats Admin Panel</h1>
    <div class="admin-controls">
      <label for="filterType">Filter Orders:</label>
      <select id="filterType">
        <option value="all">All</option>
        <option value="Pending">Pending</option>
        <option value="Completed">Completed</option>
      </select>
    </div>
  </header>

  <section class="admin-table-container">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Username</th>
          <th>Items</th>
          <th>Total</th>
          <th>Type</th>
          <th>Payment</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="orderTable">
        <?php while ($row = $stmt->fetch_assoc()): ?>
          <tr data-status="<?= $row['order_status'] ?>">
            <td><?= $row['order_id'] ?></td>
            <td><?= htmlspecialchars($row['customer_name']) ?></td>
            <td>
              <ul>
                <?php foreach (json_decode($row['order_items'], true) as $item): ?>
                  <li><?= htmlspecialchars($item['name']) ?> x<?= $item['qty'] ?></li>
                <?php endforeach; ?>
              </ul>
            </td>
            <td>₱<?= number_format($row['total_price'], 2) ?></td>
            <td><?= $row['order_type'] ?></td>
            <td><?= htmlspecialchars($row['payment_method']) ?></td>
            <td>
              <span class="<?= $row['order_status'] === 'Completed' ? 'status-completed' : 'status-pending' ?>">
                <?= $row['order_status'] ?>
              </span>
            </td>
            <td>
              <?php if ($row['order_status'] !== 'Completed'): ?>
              <form method="POST" action="admin.php">
                <input type="hidden" name="complete_order_id" value="<?= $row['order_id'] ?>">
                <button type="submit" class="complete-btn">Mark Completed</button>
              </form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </section>

  <script>
    document.getElementById('filterType').addEventListener('change', e => {
      const type = e.target.value;
      document.querySelectorAll('#orderTable tr').forEach(row => {
        const status = row.dataset.status;
        row.style.display = (type === 'all' || status === type) ? '' : 'none';
      });
    });
  </script>
</body>
</html>
