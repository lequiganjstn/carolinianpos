<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_login();
$page_title='POS';

$products = $pdo->query("SELECT id,name,sku,category,price,stock_qty FROM products WHERE stock_qty > 0 ORDER BY name")->fetchAll();
require __DIR__ . '/../includes/header.php';
?>
<div class="page-head">
  <div><div class="mono-label">TERMINAL</div><h1>Point of Sale</h1><p class="lead">Search products, build a cart, and complete a cash transaction.</p></div>
  <a class="btn secondary" href="/carolinianpos/cashier/sales.php">My sales</a>
</div>
<div id="posApp" class="pos-layout">
  <section class="panel">
    <div class="search-row">
      <input id="search" class="search" placeholder="Search by product name or SKU">
    </div>
    <div id="productGrid" class="product-grid">
      <?php foreach ($products as $p): ?>
      <button class="product-card" data-name="<?= htmlspecialchars(strtolower($p['name'].' '.$p['sku'])) ?>"
        data-id="<?= $p['id'] ?>" data-price="<?= $p['price'] ?>" data-stock="<?= $p['stock_qty'] ?>"
        data-label="<?= htmlspecialchars($p['name']) ?>">
        <span class="mono-label"><?= htmlspecialchars($p['category']) ?></span>
        <strong><?= htmlspecialchars($p['name']) ?></strong>
        <span>₱<?= number_format((float)$p['price'],2) ?></span>
        <small><?= (int)$p['stock_qty'] ?> in stock</small>
      </button>
      <?php endforeach; ?>
    </div>
  </section>
  <section class="cart panel">
    <div class="mono-label">CURRENT SALE</div>
    <h2>Cart</h2>
    <div id="cartItems" class="cart-items"><div class="empty">No items added.</div></div>
    <div class="totals">
      <div><span>Subtotal</span><strong id="subtotal">₱0.00</strong></div>
      <div><span>Tax</span><strong id="tax">₱0.00</strong></div>
      <div class="total"><span>Total</span><strong id="total">₱0.00</strong></div>
    </div>
    <form id="checkoutForm" method="post" action="checkout.php" class="form-stack">
      <input type="hidden" name="cart" id="cartInput">
      <label>Cash received<input type="number" min="0" step="0.01" id="cash" name="amount_paid" required></label>
      <div class="change">Change <strong id="change">₱0.00</strong></div>
      <button class="btn primary wide" type="submit">Complete transaction</button>
    </form>
  </section>
</div>
<script>
const cart = new Map(), TAX_RATE = 0;
const money = n => '₱' + Number(n).toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2});
const products = [...document.querySelectorAll('.product-card')];
document.querySelector('#search').addEventListener('input', e => {
  const q=e.target.value.toLowerCase();
  products.forEach(p=>p.hidden=!p.dataset.name.includes(q));
});
products.forEach(btn=>btn.addEventListener('click',()=>{
  const id=btn.dataset.id, stock=+btn.dataset.stock;
  const current=cart.get(id);
  if (current && current.qty >= stock) return;
  cart.set(id,{id, name:btn.dataset.label, price:+btn.dataset.price, qty:(current?.qty||0)+1});
  render();
}));
function render(){
  const el=document.querySelector('#cartItems');
  el.innerHTML='';
  if(!cart.size){el.innerHTML='<div class="empty">No items added.</div>';}
  let subtotal=0;
  cart.forEach(item=>{
    subtotal += item.price*item.qty;
    const row=document.createElement('div'); row.className='cart-row';
    row.innerHTML=`<div><strong>${item.name}</strong><small>₱${item.price.toFixed(2)} each</small></div>
      <div class="qty"><button type="button" data-id="${item.id}" data-action="minus">−</button><span>${item.qty}</span><button type="button" data-id="${item.id}" data-action="plus">+</button></div>
      <strong>${money(item.price*item.qty)}</strong>`;
    el.appendChild(row);
  });
  el.querySelectorAll('button').forEach(b=>b.onclick=()=>{
    const item=cart.get(b.dataset.id);
    if(b.dataset.action==='plus') item.qty++;
    else if(item.qty>1) item.qty--; else cart.delete(b.dataset.id);
    render();
  });
  const tax=subtotal*TAX_RATE,total=subtotal+tax;
  document.querySelector('#subtotal').textContent=money(subtotal);
  document.querySelector('#tax').textContent=money(tax);
  document.querySelector('#total').textContent=money(total);
  const cash=+document.querySelector('#cash').value||0;
  document.querySelector('#change').textContent=money(Math.max(0,cash-total));
  document.querySelector('#cartInput').value=JSON.stringify([...cart.values()]);
}
document.querySelector('#cash').addEventListener('input',render);
document.querySelector('#checkoutForm').addEventListener('submit',e=>{
  if(!cart.size){e.preventDefault();alert('Add at least one product.');return;}
  const total=[...cart.values()].reduce((s,x)=>s+x.price*x.qty,0);
  if((+document.querySelector('#cash').value||0)<total){e.preventDefault();alert('Cash received is less than the total.');}
});
render();
</script>
<?php require __DIR__ . '/../includes/footer.php'; ?>
