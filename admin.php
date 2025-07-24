<?php
session_start();
include 'connect.php';

if (empty($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header("Location: index.php");
    exit();
}

// Prepare filters
$where = [];
$params = [];
$types = '';

if (!empty($_GET['status']) && $_GET['status'] !== 'all') {
    $where[] = 'order_status = ?';
    $params[] = $_GET['status'];
    $types .= 's';
}
if (!empty($_GET['payment']) && $_GET['payment'] !== 'all') {
    $where[] = 'payment_status = ?';
    $params[] = $_GET['payment'];
    $types .= 's';
}
if (!empty($_GET['date'])) {
    $where[] = 'DATE(created_at) = ?';
    $params[] = $_GET['date'];
    $types .= 's';
}

$sql = "SELECT * FROM orders";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="CSS/admin.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
</head>
<body>
<nav class="admin-nav">
  <a href="admin.php" class="<?= basename($_SERVER['PHP_SELF']) === 'admin.php' ? 'active' : '' ?>">Orders</a>
  <a href="admin_menu.php" class="<?= basename($_SERVER['PHP_SELF']) === 'admin_menu.php' ? 'active' : '' ?>">Menu</a>
</nav>

<header class="admin-header">
  <h1>🛠 Hidden Eats – Admin Panel</h1>
  <div class="filters">
    <div class="refresh-status">
  <img src="IMGS/refresh_spinner.png" alt="Loading..." id="refreshSpinner" style="display: none; width: 24px; vertical-align: middle;" />
</div>

    <form method="GET" style="display: flex; gap: 1rem; align-items: center;">
      <label>Order Status:
        <select name="status" onchange="this.form.submit()">
          <option value="all">All</option>
          <option value="Pending" <?= ($_GET['status'] ?? '') === 'Pending' ? 'selected' : '' ?>>Pending</option>
          <option value="Completed" <?= ($_GET['status'] ?? '') === 'Completed' ? 'selected' : '' ?>>Completed</option>
        </select>
      </label>
      <label>Payment Status:
        <select name="payment" onchange="this.form.submit()">
          <option value="all">All</option>
          <option value="Unpaid" <?= ($_GET['payment'] ?? '') === 'Unpaid' ? 'selected' : '' ?>>Unpaid</option>
          <option value="Paid" <?= ($_GET['payment'] ?? '') === 'Paid' ? 'selected' : '' ?>>Paid</option>
        </select>
      </label>
      <label>Date:
        <input type="date" name="date" value="<?= htmlspecialchars($_GET['date'] ?? '') ?>" onchange="this.form.submit()" />
      </label>
    </form>
  </div>
  <div class="admin-controls">
    <a href="LoggedIn.php" class="kiosk-btn" title="Go to Kiosk View">🍽️ View Kiosk</a>
    <a href="logout.php" class="logout-btn" title="Logout">🚪 Logout</a>
  </div>
</header>

<section class="admin-table-container">
  <table class="admin-table" id="orderTable">
    <thead>
      <tr>
        <th>ID</th>
        <th>Customer</th>
        <th>Items</th>
        <th>Total</th>
        <th>Type</th>
        <th>Status</th>
        <th>Payment</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr data-status="<?= $row['order_status'] ?>" data-payment="<?= $row['payment_status'] ?>">
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
          <td>
            <span class="status <?= strtolower($row['order_status']) ?>"><?= $row['order_status'] ?></span>
            <?php if ($row['order_status'] !== 'Completed'): ?>
              <form method="POST" action="status_update.php">
                <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>" />
                <button class="complete-btn" type="submit">Mark Completed</button>
              </form>
            <?php endif; ?>
          </td>
          <td>
            <span class="payment <?= strtolower($row['payment_status']) ?>">
              <?= $row['payment_method'] ?> – <?= $row['payment_status'] ?>
            </span>
            <?php if ($row['payment_status'] !== 'Paid'): ?>
              <form method="POST" action="status_update.php">
                <input type="hidden" name="payment_id" value="<?= $row['order_id'] ?>" />
                <button class="mark-paid-btn" type="submit">Mark as Paid</button>
              </form>
            <?php endif; ?>
          </td>
          <td>—</td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</section>

</body>
</html>
