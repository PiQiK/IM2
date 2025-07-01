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
    </div>
    <button class="button" onclick="checkout()">Proceed to Checkout</button>
  </div>

  <script>
    let cartCount = 0;

    function addToCart(item) {
      cartCount++;
      document.getElementById("cartCount").innerText = cartCount;
      alert(item + " added to cart!");
    }

    function checkout() {
      if (cartCount > 0) {
        alert("Thank you for your order! Please proceed to payment.");
        cartCount = 0;
        document.getElementById("cartCount").innerText = cartCount;
      } else {
        alert("Your cart is empty.");
      }
    }
  </script>
</body>
</html>
