<?php
session_start();
include 'connect.php';

if (empty($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$catQ = $conn->query("SELECT * FROM menu ORDER BY food_category, foodName");
$menu = [];
$cats = [];
while ($r = $catQ->fetch_assoc()) {
    $menu[$r['food_category']][] = $r;
    if (!in_array($r['food_category'], $cats)) $cats[] = $r['food_category'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hidden Eats Kiosk</title>
  <link rel="stylesheet" href="CSS/LoggedIn.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
  <header class="user-header">
    <h1>🍽️ Hidden Eats</h1>
    <?php if (!empty($_SESSION['is_admin'])): ?>
      <a href="admin.php" class="admin-link">Go to Admin Dashboard</a>
    <?php endif; ?>
  </header>

  <section class="menu-filters">
    <div class="filter-buttons">
      <button class="filter-btn active" data-cat="All">All</button>
      <?php foreach ($cats as $c): ?>
        <button class="filter-btn" data-cat="<?= htmlspecialchars($c) ?>"> <?= htmlspecialchars($c) ?> </button>
      <?php endforeach; ?>
    </div>
  </section>

  <main class="menu-section">
    <?php foreach ($menu as $cat => $items): ?>
      <?php foreach ($items as $item): ?>
        <div class="menu-card" data-cat="<?= htmlspecialchars($cat) ?>">
          <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['foodName']) ?>">
          <div class="menu-details">
            <h3><?= htmlspecialchars($item['foodName']) ?></h3>
            <p class="price">₱<?= number_format($item['price'], 2) ?></p>
            <button class="order-btn"
              data-id="<?= $item['food_id'] ?>"
              data-name="<?= htmlspecialchars($item['foodName']) ?>"
              data-price="<?= $item['price'] ?>">Add to Cart</button>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endforeach; ?>
  </main>

  <aside class="cart-sidebar">
    <h2>🛒 Your Cart</h2>
    <ul id="cart-items"></ul>
    <p>Total: ₱<span id="cart-total">0.00</span></p>

    <label>Order Type:
      <select id="order-type">
        <option value="Dine In">Dine In</option>
        <option value="Take Out">Take Out</option>
      </select>
    </label>

    <label>Payment Method:
      <select id="payment-method">
        <option value="Cash">Cash</option>
        <option value="GCash">GCash</option>
      </select>
    </label>

    <button class="checkout-btn">Checkout</button>
  </aside>

  <script src="loggedIn.js"></script>
</body>
</html>
