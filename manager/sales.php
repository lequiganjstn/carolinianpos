<?php
require_once __DIR__ . '/../config/db.php'; require_once __DIR__ . '/../includes/auth_check.php'; require_role(['Manager','Admin']);
$page_title='Sales'; $start=$_GET['start']??date('Y-m-d');$end=$_GET['end']??date('Y-m-d');
$stmt=$pdo->prepare("SELECT t.*,u.full_name FROM transactions t JOIN users u ON u.id=t.cashier_id WHERE DATE(t.created_at) BETWEEN ? AND ? ORDER BY t.created_at DESC");
$stmt->execute([$start,$end]);$rows=$stmt->fetchAll();
require __DIR__ . '/../includes/header.php'; ?>
<h1>Sales</h1><p class="lead">Review transactions across all cashiers.</p>
<form class="filter-bar"><label>From<input type="date" name="start" value="<?= $start ?>"></label><label>To<input type="date" name="end" value="<?= $end ?>"></label><button class="btn primary">Filter</button></form>
<div class="table-wrap"><table><thead><tr><th>ID</th><th>Date</th><th>Cashier</th><th>Total</th><th>Status</th><th>Action</th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr><td>#<?= $r['id'] ?></td><td><?= htmlspecialchars($r['created_at']) ?></td><td><?= htmlspecialchars($r['full_name']) ?></td><td>₱<?= number_format((float)$r['total_amount'],2) ?></td><td><?= htmlspecialchars($r['status']) ?></td><td><?php if($r['status']==='completed'): ?><form method="post" action="void.php" onsubmit="return confirm('Void transaction #<?= $r['id'] ?>?')"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="link-danger">Void</button></form><?php endif; ?></td></tr><?php endforeach; ?>
</tbody></table></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
