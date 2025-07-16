document.addEventListener('DOMContentLoaded', () => {
  const filterBtns = document.querySelectorAll('.filter-btn');
  const cards = document.querySelectorAll('.menu-card');
  const cart = [];
  const itemsEl = document.getElementById('cart-items');
  const totalEl = document.getElementById('cart-total');
  const checkoutBtn = document.querySelector('.checkout-btn');

  // Filter logic
  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      filterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const cat = btn.dataset.cat;
      cards.forEach(card => {
        card.style.display = (cat === 'All' || card.dataset.cat === cat) ? 'block' : 'none';
      });
    });
  });

  // Add to cart logic
  document.querySelectorAll('.order-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = +btn.dataset.id;
      const name = btn.dataset.name;
      const price = +btn.dataset.price;
      const existing = cart.find(item => item.id === id);
      if (existing) existing.qty++;
      else cart.push({ id, name, price, qty: 1 });
      updateCart();
    });
  });

  function updateCart() {
    itemsEl.innerHTML = '';
    let total = 0;

    cart.forEach((item, idx) => {
      total += item.price * item.qty;
      itemsEl.innerHTML += `
        <li>${item.name}
          <button class="qty" data-idx="${idx}" data-action="dec">-</button>
          ${item.qty}
          <button class="qty" data-idx="${idx}" data-action="inc">+</button>
          ₱${(item.price * item.qty).toFixed(2)}
          <button class="remove" data-idx="${idx}">x</button>
        </li>`;
    });

    totalEl.textContent = total.toFixed(2);

    // Quantity controls
    itemsEl.querySelectorAll('.qty').forEach(btn => {
      btn.onclick = () => {
        const item = cart[btn.dataset.idx];
        item.qty += btn.dataset.action === 'inc' ? 1 : (item.qty > 1 ? -1 : 0);
        updateCart();
      };
    });

    // Remove buttons
    itemsEl.querySelectorAll('.remove').forEach(btn => {
      btn.onclick = () => {
        cart.splice(btn.dataset.idx, 1);
        updateCart();
      };
    });
  }

  // Checkout logic
  checkoutBtn.onclick = () => {
    if (cart.length === 0) {
      alert('Cart is empty!');
      return;
    }

    const orderType = document.getElementById('order-type').value;

    fetch('checkout.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    cart,
    order_type: document.getElementById('order-type').value,
    payment_method: document.getElementById('payment-method').value
  })
})
    .then(res => res.json())
    .then(data => {
      if (data.success && data.order_id) {
        window.location.href = `confirm.php?order_id=${data.order_id}`;
      } else {
        alert('Checkout failed: ' + (data.error || 'Unknown error'));
      }
    })
    .catch(() => {
      alert('Network error during checkout');
    });
  };
});
