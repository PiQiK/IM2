<?php
session_start();

// Redirect to login page if the user is not logged in or session is invalid
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include("connect.php");

// Enable MySQLi error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Fetch user ID from session
$user_id = $_SESSION['user_id'];


// Handle form submissions for job actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $job_id = isset($_POST['job_id']) ? intval($_POST['job_id']) : 0;

    if ($action === 'complete') {
        // Mark the job and its applications as 'completed'
        $completeJobQuery = "
            UPDATE applications 
            SET status = 'completed' 
            WHERE job_id = ? AND status = 'accepted'";
        $stmt = $conn->prepare($completeJobQuery);
        $stmt->bind_param("i", $job_id);
        if ($stmt->execute()) {
            $success_message = "Job marked as completed successfully!";
        } else {
            $error_message = "Failed to complete the job.";
        }
        $stmt->close();
        // Redirect to avoid form resubmission
        header("Location: loggedin.php");
        exit();
    } elseif ($action === 'delete') {
        // First delete related applications
        $deleteApplicationsQuery = "DELETE FROM applications WHERE job_id = ?";
        $stmt = $conn->prepare($deleteApplicationsQuery);
        $stmt->bind_param("i", $job_id);
        $stmt->execute();
        $stmt->close();

        // Then delete the job itself
        $deleteJobQuery = "DELETE FROM jobs WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($deleteJobQuery);
        $stmt->bind_param("ii", $job_id, $user_id);
        if ($stmt->execute()) {
            $success_message = "Job deleted successfully!";
        } else {
            $error_message = "Failed to delete the job.";
        }
        $stmt->close();

        // Redirect to avoid form resubmission
        header("Location: loggedin.php");
        exit();
    }
}

// Fetch user details from the database using session data
$query = "SELECT firstName, lastName, profile_picture, bio FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$user = $result->fetch_assoc();
$firstName = htmlspecialchars($user['firstName']);
$lastName = htmlspecialchars($user['lastName']);
$profilePicture = htmlspecialchars($user['profile_picture']) ?: 'imgs/default-profile.png';
$bio = htmlspecialchars($user['bio']);

// Fetch jobs added by the logged-in user excluding completed jobs
$jobsAddedQuery = "
    SELECT 
        j.id AS job_id, 
        j.title, 
        j.description, 
        j.category,  -- Add this line to fetch the category
        u.id AS employee_id, 
        u.firstName, 
        u.lastName, 
        a.status 
    FROM jobs j
    LEFT JOIN applications a 
        ON j.id = a.job_id 
        AND a.status = 'accepted' 
    LEFT JOIN users u 
        ON a.user_id = u.id
    WHERE j.user_id = ? AND j.id NOT IN (
        SELECT job_id 
        FROM applications 
        WHERE status = 'completed'
    )";
$stmt = $conn->prepare($jobsAddedQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$jobsAdded = $stmt->get_result();

// Fetch ongoing jobs where the user is the employee
$ongoingJobsQuery = "
    SELECT j.id AS job_id, j.title, j.description, u.id AS employer_id, u.firstName, u.lastName 
    FROM applications a
    INNER JOIN jobs j ON a.job_id = j.id
    INNER JOIN users u ON j.user_id = u.id
    WHERE a.user_id = ? AND a.status = 'accepted'";
$stmt = $conn->prepare($ongoingJobsQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$ongoingJobs = $stmt->get_result();

// Fetch pending applications for notifications
$applicationsQuery = "
    SELECT a.id AS application_id, a.status, u.firstName, u.lastName, u.bio, j.title 
    FROM applications a
    INNER JOIN jobs j ON a.job_id = j.id
    INNER JOIN users u ON a.user_id = u.id
    WHERE j.user_id = ? AND a.status = 'pending'
";
$stmt = $conn->prepare($applicationsQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$applications = $stmt->get_result();
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>HIDDEN EATS</title>
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
    .custom-scroll::-webkit-scrollbar {
        width: 8px;
      }
      .custom-scroll::-webkit-scrollbar-track {
        background: transparent;
      }
      .custom-scroll::-webkit-scrollbar-thumb {
        background-color: #ccc;
        border-radius: 8px;
      }
      .custom-scroll:hover::-webkit-scrollbar-thumb {
        background-color: #999;
      }
  </style>
</head>
<body class="bg-orange-50 min-h-screen" x-data="app" x-init="loadCart()">
  <div class="flex">
    <!-- Sidebar -->
    <aside class="w-48 bg-white shadow-xl p-4 space-y-6 fixed top-0 bottom-0 left-0 dark:bg-gray-800">
      <div class="text-center font-bold text-2xl text-orange-500 tracking-wide">HIDDEN EATS</div>
      <ul class="space-y-2 text-gray-700 dark:text-gray-200">
        <li class="cursor-pointer font-bold text-orange-500" @click="category = '🍽️ All-Day Breakfast'">🍽️ All-Day Breakfast</li>
        <li class="cursor-pointer font-bold text-red-600" @click="category = '🍛 Rice Meals'">🍛 Rice Meals</li>
        <li class="cursor-pointer font-bold text-yellow-600" @click="category = '🥖 Homemade Pandesal'">🥖 Pandesal</li>
        <li class="cursor-pointer font-bold text-green-600" @click="category = '⭐️ Specials'">⭐️ Specials</li>
        <li class="cursor-pointer font-bold text-violet-600" @click="category = '➕ Add-Ons'">➕ Add-Ons</li>
        <li class="cursor-pointer font-bold text-amber-700" @click="category = '☕ Beverages'">☕ Beverages</li>
      </ul>
    </aside>

    <!-- Main Content -->
    <main class="ml-48 flex-1 p-8 pb-32">
      <h1 class="text-4xl font-bold text-gray-800 mb-6 dark:text-white" x-text="category"></h1>

      <!-- Item Section -->
      <section>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <template x-for="(item, i) in filteredItems" :key="i">
            <div class='bg-white dark:bg-gray-800 shadow-lg rounded-lg p-4 text-center hover:shadow-xl transition-fade flex flex-col justify-between h-full'>
              <img :src="item.img" :alt="item.name" class='rounded-md mb-3 w-full h-40 object-cover'>
              <div>
                <h3 class='text-lg font-semibold text-gray-800 dark:text-white' x-text="item.name"></h3>
                <p class='text-gray-600 dark:text-gray-300'>₱<span x-text="item.price.toFixed(2)"></span></p>
                <button @click="addToCart(item.name, item.price)" class='mt-3 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700'>Add to Cart</button>
              </div>
            </div>
          </template>
        </div>
      </section>
    </main>

    <!-- Footer Bar -->
    <footer class="fixed bottom-0 left-48 right-0 bg-white dark:bg-gray-800 shadow-inner p-4 border-t flex justify-end space-x-4 z-50">
      <button
        @click="confirmReset = true"
        class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600"
      >
        Start over
      </button>
      <button @click="viewOrder()" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-900">View order - ₱<span x-text="total.toFixed(2)">0.00</span></button>
    </footer>
  </div>

  <!-- Modal -->
  <div x-show="showModal" @click.away="showModal = false" class="fixed inset-0 bg-black bg-opacity-40 z-40 flex items-center justify-center">
    <div class="w-full max-w-md bg-white dark:bg-gray-800 p-6 rounded-xl shadow-2xl">
      <template x-if="checkout">
        <div>
          <!-- Step 2: Payment -->
          <template x-if="step === 2">
            <div>
              <h2 class="text-xl font-bold mb-4 text-center">Finalize Your Order</h2>
                  <!-- Order Type: Dine In or Take Out -->
                  <div class="mb-6 text-center">
                    <span class="font-semibold text-gray-700 dark:text-gray-200">Order Type:</span>
                    <div class="flex justify-center mt-2 space-x-4">
                      <label class="flex items-center space-x-2">
                        <input type="radio" x-model="orderType" value="Dine In" class="accent-yellow-500">
                        <span class="text-gray-700 dark:text-gray-200">Dine In</span>
                      </label>
                      <label class="flex items-center space-x-2">
                        <input type="radio" x-model="orderType" value="Take Out" class="accent-yellow-500">
                        <span class="text-gray-700 dark:text-gray-200">Take Out</span>
                      </label>
                    </div>
                  </div>
                  <div class="mb-6 text-left relative">
  <h3 class="text-md font-bold mb-2 text-gray-800 dark:text-white">Order Summary:</h3>
  
  <!-- Scrollable Order Summary -->
  <div id="summary-scroll" class="max-h-[200px] overflow-y-auto pr-2 custom-scroll space-y-1 border rounded p-2">
    <ul class="text-sm text-gray-700 dark:text-gray-300 space-y-1">
      <template x-for="item in cart" :key="item.name">
        <li>
          <span x-text="item.name"></span> × <span x-text="item.qty"></span> — ₱<span x-text="(item.price * item.qty).toFixed(2)"></span>
        </li>
      </template>
    </ul>
  </div>

  <!-- Scroll down arrow -->
  <button
    x-show="cart.length > 3"
    @click="$nextTick(() => document.getElementById('summary-scroll').scrollTo({ top: 9999, behavior: 'smooth' }))"
    class="absolute bottom-0 right-2 bg-white bg-opacity-80 hover:bg-opacity-100 text-gray-500 hover:text-gray-800 rounded-full p-1 shadow-md transition"
    title="Scroll to bottom"
  >
    ↓
  </button>

  <div class="mt-3 font-semibold">
    Total: ₱<span x-text="total.toFixed(2)"></span>
  </div>
</div>

                  <!-- Payment Method -->
                    <div class="mb-6 text-center">
                      <span class="font-semibold text-gray-700 dark:text-gray-200">How would you like to pay?</span>
                      <div class="flex flex-col space-y-4 mt-4">
                        <button @click="selectPayment('GCash')" class="w-full bg-purple-600 text-white py-2 rounded hover:bg-purple-700">GCash</button>
                        <button @click="selectPayment('Cash')" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">Cash</button>
                      </div>
                    </div>

                    <div class="mt-6 text-center">
                      <button @click="showModal = false" class="text-sm text-gray-500 hover:underline">← Back to Order</button>
                    </div>
                  </div>
                </template>
        </div>
      </template>

      <template x-if="!checkout">
  <div>
    <div class="max-h-[80vh] overflow-y-auto pr-1 custom-scroll">
      <h2 class="text-2xl font-bold text-red-600 mb-4 text-center">Your Order</h2>

      <template x-if="cart.length === 0">
        <p class="text-center text-gray-500 dark:text-gray-300">Your cart is empty.</p>
      </template>

      <div class='space-y-2' x-show="cart.length > 0">
        <div class="relative">
          <div id="cart-scroll" class="space-y-2 max-h-[50vh] overflow-y-auto pr-2 custom-scroll">
            <template x-for="(item, index) in cart" :key="index">
              <div class="flex justify-between items-center py-2 border-b">
                <div>
                  <p class="font-medium text-gray-800 dark:text-white" x-text="item.name"></p>
                  <div class="text-sm text-gray-500 dark:text-gray-300">
                    ₱<span x-text="item.price.toFixed(2)"></span> × <span x-text="item.qty"></span>
                  </div>
                </div>
                <div class="flex items-center space-x-2">
                  <button @click="changeQty(index, -1)" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-2 rounded">−</button>
                  <span x-text="item.qty"></span>
                  <button @click="changeQty(index, 1)" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-2 rounded">+</button>
                  <button @click="removeItem(index)" class="text-red-500 hover:text-red-700 ml-2">✕</button>
                </div>
              </div>
            </template>
          </div>

          <!-- Scroll-down arrow -->
          <button
            x-show="cart.length > 3"
            @click="$nextTick(() => document.getElementById('cart-scroll').scrollTo({ top: 9999, behavior: 'smooth' }))"
            class="absolute bottom-0 right-2 bg-white bg-opacity-80 hover:bg-opacity-100 text-gray-500 hover:text-gray-800 rounded-full p-1 shadow-md transition"
            title="Scroll to bottom"
          >
            ↓
          </button>
        </div>
      </div>

      <div class='flex justify-between mt-6 text-lg font-semibold text-gray-800 border-t pt-4'>
        <span>Total</span>
        <span>₱<span x-text="total.toFixed(2)"></span></span>
      </div>
    </div>

    <!-- ✅ FIXED button row -->
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
        confirmReset: false,
        orderType: '',
        showReceipt: false,
        receiptData: null,
        search: '',
        category: '🍛 Rice Meals',
        showModal: false,
        checkout: false,
        step: 1,
        cart: [],
        items: [
          { name: 'Cheeseburger Deluxe', price: 150, img: 'cheese_burger.jpg', category: '⭐️ Specials' },
          { name: 'Grilled Pork Teriyaki', price: 160, img: 'grilled_teriyaki.jpg', category: '⭐️ Specials' },
          { name: 'Spam Rice', price: 150, img: 'Spam Rice.jpg', category: '🍽️ All-Day Breakfast' },
          { name: 'New York Hotdog', price: 140, img: 'newyork_hotdog.jpg', category: '🍽️ All-Day Breakfast' },
          { name: 'Chorizo Rice', price: 150, img: 'Chorizo Rice.jpg', category: '🍽️ All-Day Breakfast' },
          { name: 'Burger Steak Rice', price: 120, img: 'burger_steak.jpg', category: '🍛 Rice Meals' },
          { name: 'Beef Pares Rice', price: 120, img: 'pares.jpg', category: '🍛 Rice Meals' },
          { name: 'Chicken Katsu Rice', price: 110, img: 'chicken_katsu.jpg', category: '🍛 Rice Meals' },
          { name: 'Hungarian Rice', price: 130, img: 'Hungarian Rice.jpg', category: '🍛 Rice Meals' },
          { name: 'Beef Shawarma Rice', price: 110, img: 'Beef Shawarma Rice.jpg', category: '🍛 Rice Meals' },
          { name: 'Braised Pork Rice', price: 120, img: 'Braised Pork Rice.jpg', category: '🍛 Rice Meals' },
          { name: 'Sisig Rice', price: 120, img: 'sisig.jpg', category: '🍛 Rice Meals' },
          { name: 'Buttered Pandesal', price: 8, img: 'Buttered Pandesal.jpg', category: '🥖 Homemade Pandesal' },
          { name: 'Regular Pandesal', price: 7, img: 'Regular Pandesal.jpg', category: '🥖 Homemade Pandesal' },
          { name: 'Extra Rice', price: 25, img: 'Extra Rice.jpg', category: '➕ Add-Ons' },
          { name: 'Extra Egg', price: 20, img: 'Extra Egg.jpg', category: '➕ Add-Ons' },
          { name: 'Siomai (per piece)', price: 10, img: 'Siomai.jpg', category: '➕ Add-Ons' },
          { name: 'Pork Lumpia', price: 10, img: 'Pork Lumpia.jpg', category: '➕ Add-Ons' },
          { name: 'Brewed Coffee', price: 50, img: 'coffee.jpg', category: '☕ Beverages' }
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
          this.step = 2;
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
        selectPayment(method) {
  // ✅ Safeguard the check
        if (!this.orderType || this.orderType === '') {
          alert("Please select Dine In or Take Out.");
          return;
        }

        const orderNumber = 'QB' + Math.floor(100000 + Math.random() * 900000);

        this.receiptData = {
          cart: [...this.cart],
          orderType: this.orderType,
          paymentMethod: method,
          total: this.total.toFixed(2),
          timestamp: new Date().toLocaleString(),
          orderNumber: orderNumber,
        };

        this.showModal = false;
        this.showReceipt = true;

        // Reset after storing orderType
        this.resetCart();
        this.orderType = '';
        this.checkout = false;
        this.step = 1;
      }
      }));
    });
  </script>
  <!-- Receipt Modal -->
<div x-show="showReceipt" class="fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center">
  <div class="w-full max-w-md bg-white dark:bg-gray-800 p-6 rounded-xl shadow-2xl max-h-[90vh] overflow-y-auto custom-scroll">
    <h2 class="text-xl font-bold text-center mb-4 text-green-600">Thank You for Ordering!</h2>

    <template x-if="receiptData">
      <div>
        <p class="text-center text-sm text-gray-700 dark:text-gray-300 mb-2">
          Your order number is <span class="font-bold" x-text="receiptData.orderNumber"></span>
        </p>
        <p class="text-center text-sm text-gray-700 dark:text-gray-300 mb-4">
          Please proceed to the cashier.
        </p>

        <p class="text-sm text-gray-700 dark:text-gray-300 mb-2"><strong>Date:</strong> <span x-text="receiptData.timestamp"></span></p>
        <p class="text-sm text-gray-700 dark:text-gray-300 mb-2"><strong>Order Type:</strong> <span x-text="receiptData.orderType"></span></p>
        <p class="text-sm text-gray-700 dark:text-gray-300 mb-4"><strong>Payment Method:</strong> <span x-text="receiptData.paymentMethod"></span></p>

        <div class="relative">
  <div id="receipt-scroll" class="max-h-[200px] overflow-y-auto pr-2 custom-scroll border-t pt-2 space-y-1 text-sm mb-4 text-gray-800 dark:text-gray-200">
    <ul class="space-y-1">
      <template x-for="item in receiptData.cart" :key="item.name">
        <li>
          <span x-text="item.name"></span> × <span x-text="item.qty"></span> — ₱<span x-text="(item.price * item.qty).toFixed(2)"></span>
        </li>
      </template>
    </ul>
  </div>

  <!-- Scroll-down arrow -->
  <button
    x-show="receiptData.cart.length > 3"
    @click="$nextTick(() => document.getElementById('receipt-scroll').scrollTo({ top: 9999, behavior: 'smooth' }))"
    class="absolute bottom-1 right-2 bg-white bg-opacity-80 hover:bg-opacity-100 text-gray-500 hover:text-gray-800 rounded-full p-1 shadow-md transition"
    title="Scroll to bottom"
  >
    ↓
  </button>
</div>

        <div class="text-lg font-semibold text-right">
          Total: ₱<span x-text="receiptData.total"></span>
        </div>

        <div class="mt-6 text-center flex justify-between">
          <button @click="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Print</button>
          <button @click="showReceipt = false" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Close</button>
        </div>
      </div>
    </template>
  </div>
</div>
  </div>
</div>
<div x-show="confirmReset" class="fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center">
  <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-sm text-center">
    <h2 class="text-xl font-semibold text-gray-800 mb-4">Are you sure?</h2>
    <p class="text-gray-600 mb-6">This will clear your current order and start over.</p>
    <div class="flex justify-center gap-4">
      <button @click="resetCart(); confirmReset = false" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Yes, Start Over</button>
      <button @click="confirmReset = false" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Cancel</button>
    </div>
  </div>
</div>
</body>
</html>

