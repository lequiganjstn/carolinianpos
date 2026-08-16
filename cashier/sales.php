<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_login(); $page_title='My Sales';
$stmt=$pdo->prepare("SELECT t.*,u.full_name FROM transactions t JOIN users u ON u.id=t.cashier_id WHERE t.cashier_id=? ORDER BY t.created_at DESC LIMIT 100");
$stmt->execute([current_user()['id']]); $rows=$stmt->fetchAll();
require __DIR__ . '/../includes/header.php'; ?>
<h1>My sales</h1><p class="lead">Transactions recorded under your cashier account.</p>
<div class="table-wrap"><table><thead><tr><th>ID</th><th>Date</th><th>Total</th><th>Status</th><th></th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr><td>#<?= $r['id'] ?></td><td><?= htmlspecialchars($r['created_at']) ?></td><td>₱<?= number_format((float)$r['total_amount'],2) ?></td><td><span class="status"><?= htmlspecialchars($r['status']) ?></span></td><td><a class="text-link" href="/carolinianpos/cashier/receipt.php?id=<?= $r['id'] ?>">Receipt →</a></td></tr><?php endforeach; ?>
</tbody></table></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
