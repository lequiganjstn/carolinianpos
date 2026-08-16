<?php
require_once __DIR__ . '/includes/auth_check.php';
require_login();
$role = current_user()['role'];
if ($role === 'Cashier') header('Location: /carolinianpos/cashier/pos.php');
else header('Location: /carolinianpos/manager/dashboard.php');
exit;
