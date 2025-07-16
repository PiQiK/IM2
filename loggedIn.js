document.addEventListener('DOMContentLoaded', () => {
  const filterBtns = document.querySelectorAll('.filter-btn');
  const cards = document.querySelectorAll('.menu-card');
  const cart = [];
  const itemsEl = document.getElementById('cart-items');
  const totalEl = document.getElementById('cart-total');
  const checkoutBtn = document.querySelector('.checkout-btn');
  const clearCartBtn = document.getElementById('clear-cart-btn');
  
  // Filter buttons
  filterBtns.forEach(b => b.addEventListener('click', () => {
    filterBtns.forEach(x => x.classList.remove('active'));
    b.classList.add('active');
    cards.forEach(c => {
      c.style.display = b.dataset.cat === 'All' || c.dataset.cat === b.dataset.cat ? 'block' : 'none';
    });
  }));

  // Add to cart
  document.querySelectorAll('.order-btn').forEach(btn => btn.addEventListener('click', () => {
    const id = +btn.dataset.id;
    const name = btn.dataset.name;
    const price = +btn.dataset.price;
    const existing = cart.find(item => item.id === id);
    if (existing) {
      existing.qty++;
    } else {
      cart.push({ id, name, price, qty: 1 });
    }
    updateCart();
  }));

  // Update cart view
  function updateCart() {
    itemsEl.innerHTML = '';
    let total = 0;
    cart.forEach((item, idx) => {
      const subtotal = item.price * item.qty;
      total += subtotal;
      itemsEl.innerHTML += `
  <li>
    <div class="item-top">
      <strong>${item.name}</strong>
      <span>₱${subtotal.toFixed(2)}</span>
    </div>
    <div class="item-controls">
      <button class="qty" data-idx="${idx}" data-action="dec">-</button>
      <span>${item.qty}</span>
      <button class="qty" data-idx="${idx}" data-action="inc">+</button>
      <button class="remove" data-idx="${idx}">x</button>
    </div>
  </li>`;

    });
    totalEl.textContent = total.toFixed(2);

    // Quantity controls
    itemsEl.querySelectorAll('.qty').forEach(btn => {
      btn.onclick = () => {
        const i = cart[btn.dataset.idx];
        i.qty += btn.dataset.action === 'inc' ? 1 : (i.qty > 1 ? -1 : 0);
        updateCart();
      };
    });

    // Remove item
    itemsEl.querySelectorAll('.remove').forEach(btn => {
      btn.onclick = () => {
        cart.splice(btn.dataset.idx, 1);
        updateCart();
      };
    });
  }

  // Checkout
  checkoutBtn.onclick = () => {
    if (!cart.length) return alert('Cart is empty!');
    const orderType = document.getElementById('order-type').value;
    const paymentMethod = document.getElementById('payment-method').value;
  
    if (paymentMethod === 'GCash') {
      // Show modal
      document.getElementById('gcashModal').style.display = 'flex';
  
      // Store for later
      window.__checkoutData = { cart, orderType, paymentMethod };
      return;
    }
  
    sendOrder(cart, orderType, paymentMethod);
  };

  function sendOrder(cart, orderType, paymentMethod) {
    fetch('checkout.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        cart,
        order_type: orderType,
        payment_method: paymentMethod
      })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        window.location.href = `confirm.php?order_id=${data.order_id}`;
      } else {
        alert('Checkout failed: ' + data.error);
      }
    })
    .catch(() => alert('Network error during checkout'));
  }
  
  document.getElementById('confirmGcashPaymentBtn').onclick = () => {
    const { cart, orderType, paymentMethod } = window.__checkoutData || {};
    if (!cart || !paymentMethod) return alert("Missing order data.");
    document.getElementById('gcashModal').style.display = 'none';
    sendOrder(cart, orderType, paymentMethod);
  };
  

  clearCartBtn.onclick = () => {
    if (cart.length === 0) {
      alert("Cart is already empty.");
      return;
    }

    if (confirm("Are you sure you want to clear your cart?")) {
      cart.length = 0;
      updateCart();
    }
  };


});
