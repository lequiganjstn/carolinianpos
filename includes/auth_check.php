<?php
declare(strict_types=1);
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

function require_login(): void {
    if (empty($_SESSION['user'])) {
        header('Location: /carolinianpos/auth/login.php');
        exit;
    }
}

function require_role(array $roles): void {
    require_login();
    if (!in_array($_SESSION['user']['role'], $roles, true)) {
        http_response_code(403);
        exit('403 — You do not have permission to access this page.');
    }
}

function current_user(): array {
    return $_SESSION['user'] ?? [];
}
