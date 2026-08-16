<?php
require_once __DIR__ . '/auth_check.php';
require_login();
$user = current_user();
$page_title = $page_title ?? 'CarolinianPOS';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= htmlspecialchars($page_title) ?> · CarolinianPOS</title>
<link rel="stylesheet" href="/carolinianpos/assets/css/app.css">
</head>
<body>
<header class="global-nav">
  <a class="brand" href="/carolinianpos/index.php">CarolinianPOS</a>
  <div class="nav-user">
    <span><?= htmlspecialchars($user['full_name']) ?></span>
    <span class="role-pill"><?= htmlspecialchars($user['role']) ?></span>
    <a href="/carolinianpos/auth/logout.php">Sign out</a>
  </div>
</header>
<div class="app-shell">
<aside class="sidebar">
  <div class="mono-label">SYSTEM</div>
  <?php if (in_array($user['role'], ['Manager','Admin'], true)): ?>
    <a href="/carolinianpos/manager/dashboard.php">Dashboard</a>
  <?php endif; ?>
  <a href="/carolinianpos/cashier/pos.php">POS</a>
  <a href="/carolinianpos/cashier/sales.php">My sales</a>
  <?php if (in_array($user['role'], ['Manager','Admin'], true)): ?>
    <div class="side-rule"></div>
    <div class="mono-label">MANAGEMENT</div>
    <a href="/carolinianpos/manager/products.php">Products</a>
    <a href="/carolinianpos/manager/inventory.php">Inventory</a>
    <a href="/carolinianpos/manager/sales.php">Sales</a>
    <a href="/carolinianpos/manager/reports.php">Reports</a>
  <?php endif; ?>
  <?php if ($user['role'] === 'Admin'): ?>
    <div class="side-rule"></div>
    <div class="mono-label">ADMIN</div>
    <a href="/carolinianpos/admin/users.php">Users</a>
  <?php endif; ?>
</aside>
<main class="main">
