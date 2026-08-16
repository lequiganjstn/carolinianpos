<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_login();

$cart = json_decode($_POST['cart'] ?? '[]', true);
$paid = (float)($_POST['amount_paid'] ?? 0);
if (!is_array($cart) || !$cart) exit('Invalid cart.');

try {
    $pdo->beginTransaction();
    $total = 0;
    $validated = [];

    foreach ($cart as $item) {
        $stmt=$pdo->prepare('SELECT id,name,price,stock_qty FROM products WHERE id=? FOR UPDATE');
        $stmt->execute([(int)$item['id']]);
        $p=$stmt->fetch();
        $qty=(int)$item['qty'];
        if(!$p || $qty<1 || $qty>(int)$p['stock_qty']) throw new RuntimeException('Insufficient stock for '.($p['name']??'product').'.');
        $subtotal=(float)$p['price']*$qty;
        $total += $subtotal;
        $validated[]=['product_id'=>(int)$p['id'],'qty'=>$qty,'unit_price'=>(float)$p['price'],'subtotal'=>$subtotal];
    }

    if($paid < $total) throw new RuntimeException('Insufficient payment.');
    $change=$paid-$total;

    $stmt=$pdo->prepare('INSERT INTO transactions (cashier_id,total_amount,amount_paid,change_amount) VALUES (?,?,?,?)');
    $stmt->execute([current_user()['id'],$total,$paid,$change]);
    $transactionId=(int)$pdo->lastInsertId();

    foreach($validated as $item){
        $pdo->prepare('INSERT INTO transaction_items (transaction_id,product_id,quantity,unit_price,subtotal) VALUES (?,?,?,?,?)')
            ->execute([$transactionId,$item['product_id'],$item['qty'],$item['unit_price'],$item['subtotal']]);
        $pdo->prepare('UPDATE products SET stock_qty=stock_qty-? WHERE id=?')
            ->execute([$item['qty'],$item['product_id']]);
    }
    $pdo->commit();
    header("Location: receipt.php?id=$transactionId");
    exit;
} catch(Throwable $e) {
    if($pdo->inTransaction()) $pdo->rollBack();
    http_response_code(400);
    exit('Transaction failed: '.htmlspecialchars($e->getMessage()));
}
