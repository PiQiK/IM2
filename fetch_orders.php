<?php
include 'connect.php';

// Error reporting (for debugging, you can remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Apply filters
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

ob_start();
while ($row = $result->fetch_assoc()):
?>
<tr data-status="<?= htmlspecialchars($row['order_status']) ?>" data-payment="<?= htmlspecialchars($row['payment_status']) ?>">
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
  <td><?= htmlspecialchars($row['order_type']) ?></td>
  <td>
    <span class="status <?= strtolower($row['order_status']) ?>"><?= $row['order_status'] ?></span>
    <?php if ($row['order_status'] !== 'Completed'): ?>
      <form method="POST" action="status_update.php" style="margin-top: 6px;">
        <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
        <button class="complete-btn">Mark Completed</button>
      </form>
    <?php endif; ?>
  </td>
  <td>
    <span class="payment <?= strtolower($row['payment_status']) ?>">
      <?= htmlspecialchars($row['payment_method']) ?> – <?= $row['payment_status'] ?>
    </span>
    <?php if ($row['payment_status'] !== 'Paid'): ?>
      <form method="POST" action="status_update.php" style="margin-top: 6px;">
        <input type="hidden" name="payment_id" value="<?= $row['order_id'] ?>">
        <button class="mark-paid-btn">Mark as Paid</button>
      </form>
    <?php endif; ?>
  </td>
  <td>—</td>
</tr>
<?php endwhile;

echo ob_get_clean();
?>
