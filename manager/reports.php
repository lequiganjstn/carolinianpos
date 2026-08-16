<?php
require_once __DIR__ . '/../config/db.php'; require_once __DIR__ . '/../includes/auth_check.php'; require_role(['Manager','Admin']);
$page_title='Reports';$date=$_GET['date']??date('Y-m-d');
$s=$pdo->prepare("SELECT COALESCE(SUM(total_amount),0) revenue,COUNT(*) transactions FROM transactions WHERE status='completed' AND DATE(created_at)=?");
$s->execute([$date]);$summary=$s->fetch();
$best=$pdo->prepare("SELECT p.name,SUM(ti.quantity) qty,SUM(ti.subtotal) revenue FROM transaction_items ti JOIN products p ON p.id=ti.product_id JOIN transactions t ON t.id=ti.transaction_id WHERE t.status='completed' AND DATE(t.created_at)=? GROUP BY p.id ORDER BY qty DESC LIMIT 10");
$best->execute([$date]);$best=$best->fetchAll();
$cashiers=$pdo->prepare("SELECT u.full_name,COUNT(t.id) transactions,COALESCE(SUM(t.total_amount),0) revenue FROM transactions t JOIN users u ON u.id=t.cashier_id WHERE t.status='completed' AND DATE(t.created_at)=? GROUP BY u.id ORDER BY revenue DESC");
$cashiers->execute([$date]);$cashiers=$cashiers->fetchAll();
require __DIR__ . '/../includes/header.php'; ?>
<h1>Reports</h1><p class="lead">Basic daily sales reporting.</p>
<form class="filter-bar"><label>Date<input type="date" name="date" value="<?= $date ?>"></label><button class="btn primary">View</button></form>
<div class="stat-grid"><div class="stat-card"><span>Revenue</span><strong>₱<?= number_format((float)$summary['revenue'],2) ?></strong></div><div class="stat-card"><span>Transactions</span><strong><?= $summary['transactions'] ?></strong></div></div>
<div class="split-grid"><section class="panel"><div class="mono-label">BEST SELLERS</div><table><thead><tr><th>Product</th><th>Units</th><th>Revenue</th></tr></thead><tbody><?php foreach($best as $r): ?><tr><td><?= htmlspecialchars($r['name']) ?></td><td><?= $r['qty'] ?></td><td>₱<?= number_format((float)$r['revenue'],2) ?></td></tr><?php endforeach; ?></tbody></table></section>
<section class="panel"><div class="mono-label">BY CASHIER</div><table><thead><tr><th>Cashier</th><th>Transactions</th><th>Revenue</th></tr></thead><tbody><?php foreach($cashiers as $r): ?><tr><td><?= htmlspecialchars($r['full_name']) ?></td><td><?= $r['transactions'] ?></td><td>₱<?= number_format((float)$r['revenue'],2) ?></td></tr><?php endforeach; ?></tbody></table></section></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
