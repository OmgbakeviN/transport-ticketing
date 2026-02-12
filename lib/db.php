<?php
declare(strict_types=1);

function db(): PDO {
  static $pdo = null;
  if ($pdo) return $pdo;

  $host = "127.0.0.1";
  $dbname = "transport_ticketing";
  $user = "root";
  $pass = "";
  $charset = "utf8mb4";

  $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
  return $pdo;
}

function db_one(string $sql, array $params = []): ?array {
  $st = db()->prepare($sql);
  $st->execute($params);
  $row = $st->fetch();
  return $row ?: null;
}

function db_all(string $sql, array $params = []): array {
  $st = db()->prepare($sql);
  $st->execute($params);
  return $st->fetchAll();
}

function db_exec(string $sql, array $params = []): int {
  $st = db()->prepare($sql);
  $st->execute($params);
  return $st->rowCount();
}
