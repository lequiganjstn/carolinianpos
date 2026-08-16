<?php
require_once __DIR__ . '/../config/db.php'; require_once __DIR__ . '/../includes/auth_check.php'; require_role(['Admin']);
$page_title='Users';$edit=null;
if(isset($_GET['edit'])){$s=$pdo->prepare('SELECT * FROM users WHERE id=?');$s->execute([(int)$_GET['edit']]);$edit=$s->fetch();}
if($_SERVER['REQUEST_METHOD']==='POST'){
  $action=$_POST['action'];
  if($action==='save'){
    $id=(int)($_POST['id']??0);$username=trim($_POST['username']);$name=trim($_POST['full_name']);$role=$_POST['role'];$status=$_POST['status'];
    if($id){
      $pdo->prepare('UPDATE users SET username=?,full_name=?,role=?,status=? WHERE id=?')->execute([$username,$name,$role,$status,$id]);
      if(trim($_POST['password']??''))$pdo->prepare('UPDATE users SET password_hash=? WHERE id=?')->execute([password_hash($_POST['password'],PASSWORD_DEFAULT),$id]);
    }else{
      $pdo->prepare('INSERT INTO users(username,password_hash,full_name,role,status) VALUES(?,?,?,?,?)')->execute([$username,password_hash($_POST['password']?:'password',PASSWORD_DEFAULT),$name,$role,$status]);
    }
  }
  header('Location: users.php');exit;
}
$users=$pdo->query('SELECT id,username,full_name,role,status,created_at FROM users ORDER BY full_name')->fetchAll();
require __DIR__ . '/../includes/header.php'; ?>
<div class="page-head"><div><h1>User accounts</h1><p class="lead">Admin-only account and role management.</p></div><a class="btn primary" href="users.php">New user</a></div>
<section class="panel"><form method="post" class="form-grid"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= $edit['id']??'' ?>">
<label>Username<input name="username" required value="<?= htmlspecialchars($edit['username']??'') ?>"></label><label>Full name<input name="full_name" required value="<?= htmlspecialchars($edit['full_name']??'') ?>"></label>
<label>Role<select name="role"><option <?= (($edit['role']??'')==='Cashier')?'selected':'' ?>>Cashier</option><option <?= (($edit['role']??'')==='Manager')?'selected':'' ?>>Manager</option><option <?= (($edit['role']??'')==='Admin')?'selected':'' ?>>Admin</option></select></label>
<label>Status<select name="status"><option value="active" <?= (($edit['status']??'active')==='active')?'selected':'' ?>>Active</option><option value="inactive" <?= (($edit['status']??'')==='inactive')?'selected':'' ?>>Inactive</option></select></label>
<label>Password<?= $edit?' (leave blank to keep current)':'' ?><input type="password" name="password" <?= $edit?'':'required' ?>></label><div><button class="btn primary">Save user</button></div></form></section>
<div class="table-wrap"><table><thead><tr><th>User</th><th>Name</th><th>Role</th><th>Status</th><th></th></tr></thead><tbody><?php foreach($users as $u): ?><tr><td><?= htmlspecialchars($u['username']) ?></td><td><?= htmlspecialchars($u['full_name']) ?></td><td><?= htmlspecialchars($u['role']) ?></td><td><?= htmlspecialchars($u['status']) ?></td><td><a class="text-link" href="?edit=<?= $u['id'] ?>">Edit →</a></td></tr><?php endforeach; ?></tbody></table></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
