<?php
declare(strict_types=1);
require_once __DIR__ . "/db.php";

function ledger_last_hash(): ?string {
  $row = db_one("SELECT hash FROM ledger ORDER BY id DESC LIMIT 1");
  return $row ? $row["hash"] : null;
}

function ledger_add(string $event_type, array $payload): void {
  $prev = ledger_last_hash();
  $payloadJson = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  $created = (new DateTimeImmutable("now"))->format("Y-m-d H:i:s");
  $material = ($prev ?? "") . "|" . $event_type . "|" . $payloadJson . "|" . $created;
  $hash = hash("sha256", $material);

  db_exec(
    "INSERT INTO ledger (event_type, payload, prev_hash, hash, created_at) VALUES (?,?,?,?,?)",
    [$event_type, $payloadJson, $prev, $hash, $created]
  );
}
