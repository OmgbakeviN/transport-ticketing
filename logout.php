<?php
declare(strict_types=1);
require_once __DIR__ . "/lib/auth.php";
session_destroy();
header("Location: /transport-ticketing/index.php");
exit;
