<?php
declare(strict_types=1);

const APP_SECRET = "CHANGE_ME_TO_A_LONG_RANDOM_SECRET";

function b64url_encode(string $data): string {
  return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function b64url_decode(string $data): string {
  $pad = strlen($data) % 4;
  if ($pad) $data .= str_repeat('=', 4 - $pad);
  return base64_decode(strtr($data, '-_', '+/')) ?: "";
}

function make_ticket_token(int $ticket_id, int $user_id, int $trip_id, string $issued_at): string {
  $data = "{$ticket_id}|{$user_id}|{$trip_id}|{$issued_at}";
  $sig = hash_hmac("sha256", $data, APP_SECRET);
  return b64url_encode($data . "|" . $sig);
}

function parse_and_verify_token(string $token): ?array {
  $raw = b64url_decode($token);
  if (!$raw) return null;

  $parts = explode("|", $raw);
  if (count($parts) !== 5) return null;

  [$ticket_id, $user_id, $trip_id, $issued_at, $sig] = $parts;
  if (!ctype_digit($ticket_id) || !ctype_digit($user_id) || !ctype_digit($trip_id)) return null;

  $data = "{$ticket_id}|{$user_id}|{$trip_id}|{$issued_at}";
  $expected = hash_hmac("sha256", $data, APP_SECRET);
  if (!hash_equals($expected, $sig)) return null;

  return [
    "ticket_id" => (int)$ticket_id,
    "user_id" => (int)$user_id,
    "trip_id" => (int)$trip_id,
    "issued_at" => $issued_at,
  ];
}
