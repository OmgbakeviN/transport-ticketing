<?php
declare(strict_types=1);
require_once __DIR__ . "/db.php";

session_start();

function current_user(): ?array {
  if (!isset($_SESSION["uid"])) return null;
  return db_one("SELECT id, name, email, role FROM users WHERE id = ?", [$_SESSION["uid"]]);
}

function require_login(): array {
  $u = current_user();
  if (!$u) {
    header("Location: /transport-ticketing/login.php");
    exit;
  }
  return $u;
}

function require_role(array $allowed): array {
  $u = require_login();
  if (!in_array($u["role"], $allowed, true)) {
    http_response_code(403);
    echo "403 Forbidden";
    exit;
  }
  return $u;
}
