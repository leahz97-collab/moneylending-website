<?php
require_once __DIR__ . "/config.php";

function requireLogin() {
    if (empty($_SESSION["user_id"])) {
        header("Location: login.php");
        exit;
    }
}

function requireRole(...$allowed) {
    requireLogin();
    $role = $_SESSION["role"] ?? "";
    if (!in_array($role, $allowed, true)) {
        http_response_code(403);
        exit("Access denied.");
    }
}

function currentRole() {
    return $_SESSION["role"] ?? "";
}

function currentUsername() {
    return $_SESSION["username"] ?? "";
}