<?php

require_once '../app/init.php';

$app = new App;

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="../CSS/index.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../CSS/hidden.js" defer></script> 
    <script src="../View/categories.js"></script>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap');
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #fefefe;
    }
    header {
      background-color: #d32f2f;
      color: white;
      padding: 20px;
      text-align: center;
      font-size: 2em;
    }
    .menu {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      padding: 20px;
    }
    .item {
      background: #fff3e0;
      border: 2px solid #ffcc80;
      border-radius: 15px;
      padding: 15px;
      text-align: center;
      transition: 0.3s;
    }
    .item:hover {
      transform: scale(1.05);
      cursor: pointer;
    }
    .item img {
      max-width: 100%;
      border-radius: 10px;
    }
    .cart {
      position: fixed;
      bottom: 0;
      width: 100%;
      background: #eeeeee;
      padding: 15px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 1.2em;
    }
    .button {
      background: #388e3c;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 10px;
      font-size: 1em;
    }
    .button:hover {
      background: #2e7d32;
    }
  </style>
  <style>
    @media print {
      body * {
        visibility: hidden;
      }
      #receiptModal, #receiptModal * {
        visibility: visible;
      }
      #receiptModal {
        position: absolute;
        top: 0;
        left: 0;
        transform: none;
        width: 100%;
        height: auto;
        box-shadow: none;
      }
      .button {
        display: none;
      }
    }
  </style>
</head>
<body>
  <header>
    🍔 Welcome to QuickBite Kiosk
  </header>

  <div class="menu">
    <div class="item" onclick="addToCart('Cheeseburger')">
      <img src="https://via.placeholder.com/200x150.png?text=Cheeseburger" alt="Cheeseburger">
      <h3>Cheeseburger</h3>
      <p>₱120.00</p>
    </div>
    <div class="item" onclick="addToCart('Fried Chicken')">
      <img src="https://via.placeholder.com/200x150.png?text=Fried+Chicken" alt="Fried Chicken">
      <h3>Fried Chicken</h3>
      <p>₱150.00</p>
    </div>
    <div class="item" onclick="addToCart('Iced Tea')">
      <img src="https://via.placeholder.com/200x150.png?text=Iced+Tea" alt="Iced Tea">
      <h3>Iced Tea</h3>
      <p>₱50.00</p>
    </div>
    <div class="item" onclick="addToCart('Fries')">
      <img src="https://via.placeholder.com/200x150.png?text=Fries" alt="Fries">
      <h3>Fries</h3>
      <p>₱70.00</p>
    </div>
  </div>

  <div class="cart">
    <div>
      🛒 Items in Cart: <span id="cartCount">0</span>
      <button class="button" onclick="toggleCartItems()">View Cart</button>
    </div>
    <button class="button" onclick="checkout()">Proceed to Checkout</button>
  </div>
  
  <!-- Cart Items Modal -->
  <div id="cartItemsModal" style="display:none; position:fixed; bottom:60px; left:50%; transform:translateX(-50%);
  background:#ffffff; border:1px solid #ccc; padding:20px; border-radius:10px; max-height:300px; overflow-y:auto; width:360px; box-shadow: 0 0 10px rgba(0,0,0,0.3); font-family: Arial, sans-serif;">
  <h4 style="margin: 0 0 10px;">🛍️ You Ordered:</h4>
  <div style="display: flex; font-weight: bold; border-bottom: 1px solid #ccc; padding-bottom: 5px; margin-bottom: 5px;">
    <div style="flex: 2;">Item</div>
    <div style="flex: 1; text-align: center;">Qty</div>
    <div style="flex: 0.5;"></div>
  </div>
  <div id="cartItemsList"></div>
  <div id="cartTotal" style="text-align: right; font-weight: bold; margin-top: 10px;">Total: ₱0.00</div>
</div>

<script>
    let cartItems = JSON.parse(localStorage.getItem("cartItems")) || {};
  
    const productPrices = {
      "Cheeseburger": 120,
      "Fried Chicken": 150,
      "Iced Tea": 50,
      "Fries": 70
    };
  
    function addToCart(itemName) {
      const price = productPrices[itemName];
      if (cartItems[itemName]) {
        cartItems[itemName].quantity += 1;
      } else {
        cartItems[itemName] = { price: price, quantity: 1 };
      }
  
      updateCartDisplay();
      alert(`${itemName} (₱${price}) added to cart!`);
    }
  
    function removeFromCart(itemName) {
      delete cartItems[itemName];
      updateCartDisplay();
    }
  
    function changeQuantity(itemName, delta) {
      if (cartItems[itemName]) {
        cartItems[itemName].quantity += delta;
        if (cartItems[itemName].quantity <= 0) {
          removeFromCart(itemName);
        }
        updateCartDisplay();
      }
    }
  
    function checkout() {
  if (Object.keys(cartItems).length > 0) {
    showReceipt();
    localStorage.removeItem("cartItems");
    cartItems = {};
    updateCartDisplay();
    hideCartItems();
  } else {
    alert("Your cart is empty.");
  }
}
  
    function toggleCartItems() {
      const modal = document.getElementById("cartItemsModal");
      modal.style.display = (modal.style.display === "none") ? "block" : "none";
    }
  
    function hideCartItems() {
      document.getElementById("cartItemsModal").style.display = "none";
    }
  
    function updateCartDisplay() {
      const listContainer = document.getElementById("cartItemsList");
      const totalEl = document.getElementById("cartTotal");
      const countEl = document.getElementById("cartCount");
  
      listContainer.innerHTML = "";
      let totalItems = 0;
      let totalCost = 0;
  
      for (const [itemName, itemData] of Object.entries(cartItems)) {
        const { price, quantity } = itemData;
        const itemTotal = price * quantity;
        totalItems += quantity;
        totalCost += itemTotal;
  
        const row = document.createElement("div");
        row.style.display = "flex";
        row.style.alignItems = "center";
        row.style.marginBottom = "8px";
  
        // Left - Item name and price
        const nameDiv = document.createElement("div");
        nameDiv.style.flex = "2";
        nameDiv.textContent = `${itemName}: ₱${itemTotal.toFixed(2)}`;
  
        // Center - Quantity controls
        const qtyDiv = document.createElement("div");
        qtyDiv.style.flex = "1";
        qtyDiv.style.textAlign = "center";
        qtyDiv.innerHTML = `
          <button onclick="changeQuantity('${itemName}', -1)" style="width:25px;">-</button>
          <span style="margin: 0 8px;">${quantity}</span>
          <button onclick="changeQuantity('${itemName}', 1)" style="width:25px;">+</button>
        `;
  
        // Right - Remove button
        const removeDiv = document.createElement("div");
        removeDiv.style.flex = "0.5";
        removeDiv.style.textAlign = "right";
        removeDiv.innerHTML = `<button onclick="removeFromCart('${itemName}')" style="color:red;">🗑️</button>`;
  
        row.appendChild(nameDiv);
        row.appendChild(qtyDiv);
        row.appendChild(removeDiv);
        listContainer.appendChild(row);

        localStorage.setItem("cartItems", JSON.stringify(cartItems));
      }
  
      countEl.innerText = totalItems;
      totalEl.textContent = `Total: ₱${totalCost.toFixed(2)}`;
    }
    function showReceipt() {
  const content = document.getElementById("receiptContent");
  const totalEl = document.getElementById("receiptTotal");
  const timeEl = document.getElementById("receiptTime");
  let total = 0;

  // Add date and time
  const now = new Date();
  const dateTimeString = now.toLocaleString("en-PH", {
    weekday: 'short', year: 'numeric', month: 'short', day: 'numeric',
    hour: '2-digit', minute: '2-digit', second: '2-digit',
    hour12: true
  });
  timeEl.textContent = dateTimeString;

  // Build receipt items
  content.innerHTML = "";
  for (const [itemName, itemData] of Object.entries(cartItems)) {
    const { price, quantity } = itemData;
    const itemTotal = price * quantity;
    total += itemTotal;

    const row = document.createElement("div");
    row.style.display = "flex";
    row.style.justifyContent = "space-between";
    row.style.marginBottom = "5px";
    row.innerHTML = `
      <span>${itemName} x${quantity}</span>
      <span>₱${itemTotal.toFixed(2)}</span>
    `;
    content.appendChild(row);
  }

  totalEl.textContent = `Total: ₱${total.toFixed(2)}`;
  document.getElementById("receiptModal").style.display = "block";
}


function closeReceipt() {
  document.getElementById("receiptModal").style.display = "none";
}
  </script>
  <div id="receiptModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%);
  background:white; border:2px solid #ccc; border-radius:10px; width:350px; max-height:90vh; overflow-y:auto;
  box-shadow:0 0 15px rgba(0,0,0,0.3); padding:20px; font-family:Arial, sans-serif; z-index:1000;">
  
  <h3 style="text-align:center;">🧾 Order Receipt</h3>
<div id="receiptNumber" style="text-align:center; font-size: 0.9em; margin-bottom: 5px; color: #444;"></div>
<div id="receiptTime" style="text-align:center; font-size: 0.9em; color: #555;"></div>

  <div id="receiptContent" style="margin-top:15px;"></div>
  <div id="receiptTotal" style="text-align:right; font-weight:bold; margin-top:10px;"></div>

  <div style="text-align:center; margin-top:15px;">
    <button onclick="window.print()" class="button" style="margin-right:10px;">🖨️ Print Receipt</button>
    <button onclick="closeReceipt()" class="button">Close</button>
  </div>
</div>
</body>
</html>
