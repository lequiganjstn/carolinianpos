<?php
require_once __DIR__ . '/../config/db.php'; require_once __DIR__ . '/../includes/auth_check.php'; require_role(['Manager','Admin']);
$page_title='Inventory';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $id=(int)$_POST['product_id'];$change=(int)$_POST['quantity_change'];$reason=trim($_POST['reason']);
  $pdo->beginTransaction();
  $pdo->prepare('UPDATE products SET stock_qty=GREATEST(0,stock_qty+?) WHERE id=?')->execute([$change,$id]);
  $pdo->prepare('INSERT INTO stock_adjustments(product_id,adjusted_by,quantity_change,reason) VALUES(?,?,?,?)')->execute([$id,current_user()['id'],$change,$reason]);
  $pdo->commit(); header('Location: inventory.php');exit;
}
$products=$pdo->query('SELECT * FROM products ORDER BY stock_qty ASC,name')->fetchAll();
require __DIR__ . '/../includes/header.php'; ?>
<h1>Inventory</h1><p class="lead">Review stock levels and make manual restock or correction adjustments.</p>
<div class="table-wrap"><table><thead><tr><th>Product</th><th>SKU</th><th>Stock</th><th>Threshold</th><th>Adjustment</th></tr></thead><tbody>
<?php foreach($products as $p): ?><tr><td><strong><?= htmlspecialchars($p['name']) ?></strong></td><td><?= htmlspecialchars($p['sku']) ?></td><td><span class="<?= $p['stock_qty']<=$p['low_stock_threshold']?'low':'' ?>"><?= $p['stock_qty'] ?></span></td><td><?= $p['low_stock_threshold'] ?></td><td><form method="post" class="inline-form"><input type="hidden" name="product_id" value="<?= $p['id'] ?>"><input type="number" name="quantity_change" placeholder="+10 / -2" required><input name="reason" placeholder="Reason" required><button class="btn secondary">Apply</button></form></td></tr><?php endforeach; ?>
</tbody></table></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
