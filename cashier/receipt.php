<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_login();
$id=(int)($_GET['id']??0);
$stmt=$pdo->prepare("SELECT t.*,u.full_name FROM transactions t JOIN users u ON u.id=t.cashier_id WHERE t.id=?");
$stmt->execute([$id]); $t=$stmt->fetch();
if(!$t) exit('Receipt not found.');
$items=$pdo->prepare("SELECT ti.*,p.name,p.sku FROM transaction_items ti JOIN products p ON p.id=ti.product_id WHERE ti.transaction_id=?");
$items->execute([$id]); $items=$items->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Receipt #<?= $id ?></title>
<link rel="stylesheet" href="/carolinianpos/assets/css/app.css"></head>
<body class="receipt-page"><section class="receipt">
<div class="mono-label">UNIVERSITY OF SAN CARLOS</div><h1>CarolinianPOS</h1>
<p>Transaction #<?= $id ?><br><?= htmlspecialchars($t['created_at']) ?><br>Cashier: <?= htmlspecialchars($t['full_name']) ?></p>
<hr>
<?php foreach($items as $i): ?><div class="receipt-line"><span><?= htmlspecialchars($i['name']) ?> × <?= $i['quantity'] ?></span><strong>₱<?= number_format((float)$i['subtotal'],2) ?></strong></div><?php endforeach; ?>
<hr><div class="receipt-line"><span>Total</span><strong>₱<?= number_format((float)$t['total_amount'],2) ?></strong></div>
<div class="receipt-line"><span>Cash</span><strong>₱<?= number_format((float)$t['amount_paid'],2) ?></strong></div>
<div class="receipt-line"><span>Change</span><strong>₱<?= number_format((float)$t['change_amount'],2) ?></strong></div>
<button class="btn primary print-hide" onclick="window.print()">Print receipt</button>
<a class="text-link print-hide" href="/carolinianpos/cashier/pos.php">New sale →</a>
</section></body></html>
