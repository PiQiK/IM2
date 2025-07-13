<?php

require_once '../app/init.php';

$app = new App;

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>QUICKBITE</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <style>
    body { font-family: 'Raleway', sans-serif; }
    .toast {
      position: fixed;
      bottom: 90px;
      right: 20px;
      background: #38a169;
      color: white;
      padding: 10px 20px;
      border-radius: 8px;
      z-index: 100;
      animation: fadeInOut 3s ease forwards;
    }
    @keyframes fadeInOut {
      0% { opacity: 0; transform: translateY(20px); }
      10% { opacity: 1; transform: translateY(0); }
      90% { opacity: 1; }
      100% { opacity: 0; transform: translateY(20px); }
    }
  </style>
</head>
<body class="bg-orange-50 min-h-screen" x-data="app" x-init="loadCart()" :class="darkMode ? 'dark bg-gray-900 text-white' : ''">
  <div class="flex">
    <!-- Sidebar -->
    <aside class="w-48 bg-white shadow-xl p-4 space-y-6 fixed top-0 bottom-0 left-0 dark:bg-gray-800">
      <div class="text-center font-bold text-2xl text-orange-500 tracking-wide">QUICKBITE</div>
      <ul class="space-y-2 text-gray-700 dark:text-gray-200">
        <li class="cursor-pointer" @click="category = 'Combos'">Combos</li>
        <li class="cursor-pointer" @click="category = 'Chicken'">Chicken</li>
        <li class="cursor-pointer font-bold text-red-600" @click="category = 'Burgers'">Burgers</li>
        <li class="cursor-pointer" @click="category = 'Fries'">Fries</li>
        <li class="cursor-pointer" @click="category = 'Beverages'">Beverages</li>
      </ul>
      <div class="pt-6">
        <label class="flex items-center space-x-2 cursor-pointer">
          <input type="checkbox" @change="toggleDark()" class="accent-orange-500">
          <span class="text-sm">Dark Mode</span>
        </label>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="ml-48 flex-1 p-8 pb-32">
      <h1 class="text-4xl font-bold text-gray-800 mb-6 dark:text-white" x-text="category"></h1>

      <!-- Search -->
      <input type="text" placeholder="Search..." x-model="search" class="mb-6 w-full p-3 border rounded shadow-sm dark:bg-gray-700 dark:text-white">

      <!-- Item Section -->
      <section>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <template x-for="(item, i) in filteredItems" :key="i">
            <div class='bg-white dark:bg-gray-800 shadow-lg rounded-lg p-4 text-center hover:shadow-xl transition-fade flex flex-col justify-between h-full'>
              <img :src="item.img" :alt="item.name" class='rounded-md mb-3 w-full h-40 object-cover'>
              <div>
                <h3 class='text-lg font-semibold text-gray-800 dark:text-white' x-text="item.name"></h3>
                <p class='text-gray-600 dark:text-gray-300'>$<span x-text="item.price.toFixed(2)"></span></p>
                <button @click="addToCart(item.name, item.price)" class='mt-3 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700'>Add to Cart</button>
              </div>
            </div>
          </template>
        </div>
      </section>
    </main>

    <!-- Footer Bar -->
    <footer class="fixed bottom-0 left-48 right-0 bg-white dark:bg-gray-800 shadow-inner p-4 border-t flex justify-end space-x-4 z-50">
      <button @click="resetCart()" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Start over</button>
      <button @click="viewOrder()" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-900">View order - $<span x-text="total.toFixed(2)">0.00</span></button>
    </footer>
  </div>

  <!-- Modal -->
  <div x-show="showModal" @click.away="showModal = false" class="fixed inset-0 bg-black bg-opacity-40 z-40 flex items-center justify-center">
    <div class="w-full max-w-md bg-white dark:bg-gray-800 p-6 rounded-xl shadow-2xl">
      <template x-if="checkout">
        <div>
          <h2 class="text-xl font-bold mb-4 text-center">Checkout</h2>
          <input type="text" x-model="customerName" placeholder="Your name" class="w-full p-2 mb-4 border rounded">
          <input type="email" x-model="customerEmail" placeholder="Email" class="w-full p-2 mb-4 border rounded">
          <button @click="submitOrder()" class="w-full bg-green-600 text-white p-2 rounded hover:bg-green-700">Submit Order</button>
        </div>
      </template>
      <template x-if="!checkout">
        <div>
          <h2 class="text-2xl font-bold text-red-600 mb-4 text-center">Your Order</h2>
          <template x-if="cart.length === 0">
            <p class="text-center text-gray-500 dark:text-gray-300">Your cart is empty.</p>
          </template>
          <div class='space-y-2' x-show="cart.length > 0">
            <template x-for="(item, index) in cart" :key="index">
              <div class='flex justify-between items-center py-2 border-b'>
                <div>
                  <p class='font-semibold' x-text="item.name"></p>
                  <div class='flex items-center space-x-2 mt-1'>
                    <button @click="changeQty(index, -1)" class='px-2 bg-gray-200 rounded'>−</button>
                    <span x-text="item.qty"></span>
                    <button @click="changeQty(index, 1)" class='px-2 bg-gray-200 rounded'>+</button>
                  </div>
                </div>
                <div class='text-right'>
                  <p class='text-gray-700'>$<span x-text="(item.price * item.qty).toFixed(2)"></span></p>
                  <button @click="removeItem(index)" class='text-xs text-red-500 hover:underline'>Remove</button>
                </div>
              </div>
            </template>
          </div>
          <div class='flex justify-between mt-6 text-lg font-semibold text-gray-800 border-t pt-4'>
            <span>Total</span>
            <span>$<span x-text="total.toFixed(2)"></span></span>
          </div>
          <div class='mt-6 flex justify-center space-x-4'>
            <button @click="showModal = false" class='px-5 py-2 bg-gray-300 rounded hover:bg-gray-400'>Back to Menu</button>
            <button @click="checkout = true" class='px-5 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600'>Proceed to Checkout</button>
          </div>
        </div>
      </template>
    </div>
  </div>

  <div id="toast-container"></div>

  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.data('app', () => ({
        darkMode: false,
        search: '',
        category: 'Burgers',
        showModal: false,
        checkout: false,
        customerName: '',
        customerEmail: '',
        cart: [],
        items: [
          { name: 'The Big Beef', price: 10.25, img: 'https://source.unsplash.com/300x200/?big-beef,burger', category: 'Burgers' },
          { name: 'The Classic Stack', price: 12.50, img: 'https://source.unsplash.com/300x200/?classic,burger', category: 'Burgers' },
          { name: 'Old School Burger', price: 9.50, img: 'https://source.unsplash.com/300x200/?old-school,burger', category: 'Burgers' },
          { name: 'The Juicy Original', price: 9.50, img: 'https://source.unsplash.com/300x200/?juicy,burger', category: 'Burgers' },
          { name: 'Grill Master', price: 12.50, img: 'https://source.unsplash.com/300x200/?grill,burger', category: 'Burgers' },
          { name: 'Classic Fries', price: 4.00, img: 'https://source.unsplash.com/300x200/?fries', category: 'Fries' },
          { name: 'Cheese Fries', price: 4.00, img: 'https://source.unsplash.com/300x200/?fries', category: 'Fries' },
          { name: 'Garlic Parmesan Fries', price: 4.00, img: 'https://source.unsplash.com/300x200/?fries', category: 'Fries' },
          { name: 'Barbecue Fries', price: 4.00, img: 'https://source.unsplash.com/300x200/?fries', category: 'Fries' },
          { name: 'Sour Cream & Onion Fries', price: 4.00, img: 'https://source.unsplash.com/300x200/?fries', category: 'Fries' },
          { name: 'Spicy Cajun Fries', price: 4.00, img: 'https://source.unsplash.com/300x200/?fries', category: 'Fries' },
          { name: 'Chicken Combo', price: 8.00, img: 'https://source.unsplash.com/300x200/?chicken', category: 'Combos' }
        ],
        get filteredItems() {
          return this.items.filter(i => i.category === this.category && i.name.toLowerCase().includes(this.search.toLowerCase()));
        },
        get total() {
          return this.cart.reduce((sum, i) => sum + i.price * i.qty, 0);
        },
        addToCart(name, price) {
          const index = this.cart.findIndex(i => i.name === name);
          if (index > -1) this.cart[index].qty++;
          else this.cart.push({ name, price, qty: 1 });
          this.saveCart();
          this.showToast(`${name} added to cart`);
        },
        removeItem(index) {
          this.cart.splice(index, 1);
          this.saveCart();
        },
        changeQty(index, amount) {
          this.cart[index].qty += amount;
          if (this.cart[index].qty <= 0) this.removeItem(index);
          this.saveCart();
        },
        viewOrder() {
          this.checkout = false;
          this.showModal = true;
        },
        resetCart() {
          this.cart = [];
          this.saveCart();
          this.showModal = false;
        },
        saveCart() {
          localStorage.setItem('cart', JSON.stringify(this.cart));
        },
        loadCart() {
          const stored = localStorage.getItem('cart');
          if (stored) this.cart = JSON.parse(stored);
        },
        showToast(msg) {
          const toast = document.createElement('div');
          toast.className = 'toast';
          toast.innerText = msg;
          document.getElementById('toast-container').appendChild(toast);
          setTimeout(() => toast.remove(), 3000);
        },
        toggleDark() {
          this.darkMode = !this.darkMode;
        },
        submitOrder() {
          if (!this.customerName || !this.customerEmail) {
            alert("Please complete your information.");
            return;
          }
          this.resetCart();
          this.customerName = '';
          this.customerEmail = '';
          this.showModal = true;
          this.checkout = false;
          this.showToast("Thank you for your order!");
        }
      }));
    });
  </script>
</body>
</html>

