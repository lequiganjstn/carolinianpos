<?php
require_once __DIR__ . '/../config/db.php'; require_once __DIR__ . '/../includes/auth_check.php'; require_role(['Manager','Admin']);
$page_title='Products'; $edit=null;
if(isset($_GET['edit'])){$s=$pdo->prepare('SELECT * FROM products WHERE id=?');$s->execute([(int)$_GET['edit']]);$edit=$s->fetch();}
if($_SERVER['REQUEST_METHOD']==='POST'){
  $action=$_POST['action'];
  if($action==='save'){
    $id=(int)($_POST['id']??0); $data=[trim($_POST['name']),trim($_POST['sku']),trim($_POST['category']),(float)$_POST['price'],(int)$_POST['stock_qty'],(int)$_POST['low_stock_threshold']];
    if($id){$pdo->prepare('UPDATE products SET name=?,sku=?,category=?,price=?,stock_qty=?,low_stock_threshold=? WHERE id=?')->execute([...$data,$id]);}
    else{$pdo->prepare('INSERT INTO products (name,sku,category,price,stock_qty,low_stock_threshold) VALUES (?,?,?,?,?,?)')->execute($data);}
  } elseif($action==='delete'){ $pdo->prepare('DELETE FROM products WHERE id=?')->execute([(int)$_POST['id']]);}
  header('Location: products.php');exit;
}
$products=$pdo->query('SELECT * FROM products ORDER BY name')->fetchAll();
require __DIR__ . '/../includes/header.php'; ?>
<div class="page-head"><div><h1>Products</h1><p class="lead">Manage product catalog and stock values.</p></div><a class="btn primary" href="products.php">New product</a></div>
<section class="panel">
<form method="post" class="form-grid"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= $edit['id']??'' ?>">
<label>Name<input name="name" required value="<?= htmlspecialchars($edit['name']??'') ?>"></label>
<label>SKU<input name="sku" required value="<?= htmlspecialchars($edit['sku']??'') ?>"></label>
<label>Category<input name="category" required value="<?= htmlspecialchars($edit['category']??'') ?>"></label>
<label>Price<input type="number" step="0.01" min="0" name="price" required value="<?= $edit['price']??'' ?>"></label>
<label>Stock quantity<input type="number" min="0" name="stock_qty" required value="<?= $edit['stock_qty']??0 ?>"></label>
<label>Low-stock threshold<input type="number" min="0" name="low_stock_threshold" required value="<?= $edit['low_stock_threshold']??5 ?>"></label>
<div><button class="btn primary" type="submit"><?= $edit?'Save changes':'Add product' ?></button></div>
</form></section>
<div class="table-wrap"><table><thead><tr><th>Product</th><th>SKU</th><th>Category</th><th>Price</th><th>Stock</th><th></th></tr></thead><tbody>
<?php foreach($products as $p): ?><tr><td><strong><?= htmlspecialchars($p['name']) ?></strong></td><td><?= htmlspecialchars($p['sku']) ?></td><td><?= htmlspecialchars($p['category']) ?></td><td>₱<?= number_format((float)$p['price'],2) ?></td><td><?= $p['stock_qty'] ?></td><td class="actions"><a class="text-link" href="?edit=<?= $p['id'] ?>">Edit</a><form method="post" onsubmit="return confirm('Delete this product?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $p['id'] ?>"><button class="link-danger">Delete</button></form></td></tr><?php endforeach; ?>
</tbody></table></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
