<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
include 'connect.php';

// Fetch menu
$menuData = $conn->query("SELECT * FROM menu ORDER BY food_category, foodName");
$menu = [];
$categories = [];
while ($item = $menuData->fetch_assoc()) {
    $cat = $item['food_category'];
    $menu[$cat][] = $item;
    if (!in_array($cat, $categories)) $categories[] = $cat;
}

// Fetch username
$stmt = $conn->prepare("SELECT username FROM users WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$username = htmlspecialchars($user['username']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hidden Eats Kiosk</title>
  <link rel="stylesheet" href="CSS/LoggedIn.css">
</head>
<body>
  <?php if (!empty($_SESSION['is_admin'])): ?>
    <a href="admin.php" class="admin-link">Go to Admin Dashboard</a>
  <?php endif; ?>

  <h1>Welcome, <?= $username ?>! 🍽️</h1>

  <!-- Category Buttons -->
  <div class="filter-buttons">
    <button class="filter-btn active" data-cat="All">All</button>
    <?php foreach ($categories as $cat): ?>
      <button class="filter-btn" data-cat="<?= htmlspecialchars($cat) ?>">
        <?= htmlspecialchars($cat) ?>
      </button>
    <?php endforeach; ?>
  </div>

  <!-- Menu Display -->
  <div class="menu-section">
    <?php foreach ($menu as $category => $items): ?>
      <?php foreach ($items as $item): ?>
        <div class="menu-card" data-cat="<?= htmlspecialchars($category) ?>">
          <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['foodName']) ?>">
          <h3><?= htmlspecialchars($item['foodName']) ?></h3>
          <span class="price">₱<?= number_format($item['price'], 2) ?></span>
          <button class="order-btn"
                  data-id="<?= $item['food_id'] ?>"
                  data-name="<?= htmlspecialchars($item['foodName']) ?>"
                  data-price="<?= $item['price'] ?>">
            Add to Cart
          </button>
        </div>
      <?php endforeach; ?>
    <?php endforeach; ?>
  </div>

  <!-- Cart Sidebar -->
  <div class="cart-sidebar">
    <h2>Your Cart</h2>
    <ul id="cart-items"></ul>
    <p>Total: ₱<span id="cart-total">0.00</span></p>
    <label for="order-type">Order Type:
      <select id="order-type">
        <option value="Dine In">Dine In</option>
        <option value="Take Out">Take Out</option>
      </select>
    </label>
    <button class="checkout-btn">Checkout</button>
  </div>

  <script src="loggedIn.js"></script>
</body>
</html>
