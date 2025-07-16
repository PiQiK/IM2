<?php
session_start();
include 'connect.php';
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


if (empty($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header("Location: index.php");
    exit();
}

$result = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="CSS/admin.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
<nav class="admin-nav">
  <a href="admin.php" class="<?= basename($_SERVER['PHP_SELF']) === 'admin.php' ? 'active' : '' ?>">Orders</a>
  <a href="admin_menu.php" class="<?= basename($_SERVER['PHP_SELF']) === 'admin_menu.php' ? 'active' : '' ?>">Menu</a>
</nav>

  <header class="admin-header">
    <h1>🛠 Hidden Eats – Admin Panel</h1>
    <div class="filters">
      <label>Order Status:
        <select id="statusFilter">
          <option value="all">All</option>
          <option value="Pending">Pending</option>
          <option value="Completed">Completed</option>
        </select>
      </label>
      <label>Payment Status:
        <select id="paymentFilter">
          <option value="all">All</option>
          <option value="Unpaid">Unpaid</option>
          <option value="Paid">Paid</option>
        </select>
      </label>
      <label>Date:
    <input type="date" name="date" value="<?= htmlspecialchars($_GET['date'] ?? '') ?>" onchange="this.form.submit()" />
  </label>
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
            </td>
            <td>
  <span class="payment <?= strtolower($row['payment_status']) ?>">
    <?= $row['payment_method'] ?> – <?= $row['payment_status'] ?>
  </span>
  <?php if ($row['payment_status'] !== 'Paid'): ?>
    <form method="POST" action="status_update.php" style="margin-top: 6px;">
      <input type="hidden" name="payment_id" value="<?= $row['order_id'] ?>">
      <button class="mark-paid-btn">Mark as Paid</button>
    </form>
  <?php endif; ?>
</td>

            <td>
              <?php if ($row['order_status'] !== 'Completed'): ?>
                <form method="POST" action="status_update.php">
                  <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                  <button class="complete-btn">Mark Completed</button>
                </form>
              <?php else: ?>
                —
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </section>

  <script>
    const statusFilter = document.getElementById('statusFilter');
    const paymentFilter = document.getElementById('paymentFilter');
    const rows = document.querySelectorAll('#orderTable tbody tr');

    function filterOrders() {
      const status = statusFilter.value.toLowerCase();
      const payment = paymentFilter.value.toLowerCase();

      rows.forEach(row => {
        const rowStatus = row.dataset.status.toLowerCase();
        const rowPayment = row.dataset.payment.toLowerCase();
        const statusMatch = (status === 'all' || rowStatus === status);
        const paymentMatch = (payment === 'all' || rowPayment === payment);
        row.style.display = statusMatch && paymentMatch ? '' : 'none';
      });
    }

    statusFilter.addEventListener('change', filterOrders);
    paymentFilter.addEventListener('change', filterOrders);
    
const orderTableBody = document.querySelector('#orderTable tbody');

function fetchOrders() {
  const status = document.getElementById('statusFilter').value;
  const payment = document.getElementById('paymentFilter').value;
  const date = document.querySelector('input[name="date"]').value;

  const params = new URLSearchParams({ status, payment, date });

  fetch('fetch_orders.php?' + params)
    .then(res => res.text())
    .then(html => {
      orderTableBody.innerHTML = html;
    });
}

// Initial fetch
fetchOrders();

// Poll every 15 seconds
setInterval(fetchOrders, 15000);
</script>

</body>
</html>
