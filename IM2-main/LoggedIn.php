<?php
session_start();
include 'connect.php';

if (empty($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch menu data
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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Hidden Eats Kiosk</title>
  <link rel="stylesheet" href="CSS/LoggedIn.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
</head>
<body>
<header class="user-header">
  <h1>🍽️ Hidden Eats</h1>
</header>


  <section class="menu-filters">
    <div class="filter-buttons">
      <button class="filter-btn active" data-cat="All">All</button>
      <?php foreach ($cats as $c): ?>
        <button class="filter-btn" data-cat="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></button>
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

    <button class="clear-cart-btn" id="clear-cart-btn">Clear Cart</button>
    <button class="checkout-btn">Checkout</button>
    <?php if (!empty($_SESSION['is_admin'])): ?>
  <a href="admin.php" class="admin-dashboard-btn">Go to Admin Dashboard</a>
<?php endif; ?>

  </aside>

  <!-- GCash QR Modal -->
<div id="gcashModal" class="modal" style="display:none;">
  <div class="modal-content">
    <h2>Scan to Pay via GCash</h2>
    <img src="IMGS/gcash_qr.png" alt="GCash QR Code" class="qr-image" />
    <p>Once paid, click below to confirm your order.</p>
    <button id="confirmGcashPaymentBtn" class="checkout-btn">I've Paid - Confirm Order</button>
    <button onclick="document.getElementById('gcashModal').style.display='none'" class="clear-cart-btn">Cancel</button>
  </div>
</div>


  <script src="loggedIn.js"></script>
</body>
</html>
