<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_role(['Manager','Admin']);
$page_title='Dashboard';

$today = date('Y-m-d');
$revenue = $pdo->prepare("SELECT COALESCE(SUM(total_amount),0) FROM transactions WHERE status='completed' AND DATE(created_at)=?");
$revenue->execute([$today]);
$revenue = (float)$revenue->fetchColumn();
$count = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE status='completed' AND DATE(created_at)=?");
$count->execute([$today]);
$count = (int)$count->fetchColumn();
$low = $pdo->query("SELECT COUNT(*) FROM products WHERE stock_qty <= low_stock_threshold")->fetchColumn();

require __DIR__ . '/../includes/header.php';
?>
<h1>Dashboard</h1>
<p class="lead">Operational overview for <?= htmlspecialchars($today) ?>.</p>
<div class="stat-grid">
  <div class="stat-card"><span>Today's revenue</span><strong>₱<?= number_format($revenue,2) ?></strong></div>
  <div class="stat-card"><span>Transactions</span><strong><?= $count ?></strong></div>
  <div class="stat-card"><span>Low-stock items</span><strong><?= $low ?></strong></div>
</div>
<div class="split-grid">
  <section class="panel dark-panel">
    <div class="mono-label">QUICK ACTION</div>
    <h2>Start a sale</h2>
    <p>Open the POS terminal and process a cash transaction.</p>
    <a class="btn primary" href="/carolinianpos/cashier/pos.php">Open POS</a>
  </section>
  <section class="panel">
    <div class="mono-label">INVENTORY</div>
    <h2>Stock status</h2>
    <p><?= $low ?> product(s) are at or below their configured low-stock threshold.</p>
    <a class="text-link" href="/carolinianpos/manager/inventory.php">Review inventory →</a>
  </section>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
