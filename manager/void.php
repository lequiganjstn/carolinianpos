<?php
require_once __DIR__ . '/../config/db.php'; require_once __DIR__ . '/../includes/auth_check.php'; require_role(['Manager','Admin']);
$id=(int)($_POST['id']??0);$pdo->beginTransaction();
$s=$pdo->prepare("SELECT status FROM transactions WHERE id=? FOR UPDATE");$s->execute([$id]);$t=$s->fetch();
if($t && $t['status']==='completed'){
  $items=$pdo->prepare('SELECT product_id,quantity FROM transaction_items WHERE transaction_id=?');$items->execute([$id]);
  foreach($items as $i)$pdo->prepare('UPDATE products SET stock_qty=stock_qty+? WHERE id=?')->execute([$i['quantity'],$i['product_id']]);
  $pdo->prepare("UPDATE transactions SET status='voided' WHERE id=?")->execute([$id]);
}
$pdo->commit();header('Location: sales.php');exit;
