<?php
declare(strict_types=1);
require_once __DIR__ . "/lib/auth.php";

$u = require_login();

switch ($u["role"]) {
  case "admin":
    header("Location: /transport-ticketing/admin/dashboard.php");
    exit;
  case "controller":
    header("Location: /transport-ticketing/controller/dashboard.php");
    exit;
  default:
    header("Location: /transport-ticketing/passenger/dashboard.php");
    exit;
}
