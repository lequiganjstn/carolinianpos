<?php
session_start();
$_SESSION = [];
session_destroy();
header('Location: /carolinianpos/auth/login.php');
exit;
