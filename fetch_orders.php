<?php
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

ob_start();
while ($row = $result->fetch_assoc()):
?>
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
  <td><?= $row['payment_method'] ?> - <?= $row['payment_status'] ?></td>
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
<?php endwhile;

echo ob_get_clean();
?>
