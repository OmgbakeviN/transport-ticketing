<?php
declare(strict_types=1);
require_once __DIR__ . "/../lib/auth.php";
require_once __DIR__ . "/../lib/db.php";
require_once __DIR__ . "/../lib/crypto.php";
require_once __DIR__ . "/../lib/ledger.php";

$u = require_role(["controller"]);

$result = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $token = trim($_POST["token"] ?? "");
  $parsed = parse_and_verify_token($token);

  if (!$parsed) {
    $result = ["ok" => false, "msg" => "QR/token invalide (signature)."];
  } else {
    $tk = db_one("SELECT id, status, user_id, trip_id FROM tickets WHERE id = ? AND token = ?", [$parsed["ticket_id"], $token]);

    if (!$tk) {
      $result = ["ok" => false, "msg" => "Ticket introuvable."];
    } elseif ($tk["status"] !== "paid") {
      $result = ["ok" => false, "msg" => "Ticket déjà utilisé ou invalide."];
    } else {
      db_exec("UPDATE tickets SET status = 'used', used_at = ? WHERE id = ?", [date("Y-m-d H:i:s"), (int)$tk["id"]]);

      db_exec(
        "INSERT INTO verifications (ticket_id, controller_id, result, message) VALUES (?,?, 'success', 'OK')",
        [(int)$tk["id"], (int)$u["id"]]
      );

      ledger_add("ticket_verified", [
        "ticket_id" => (int)$tk["id"],
        "controller_id" => (int)$u["id"],
        "verified_at" => date("Y-m-d H:i:s")
      ]);

      $result = ["ok" => true, "msg" => "Ticket valide ✅ Marqué comme utilisé."];
    }
  }
}

$title = "Vérifier un ticket";
require_once __DIR__ . "/../partials/head.php";
require_once __DIR__ . "/../partials/nav.php";
?>
<main class="max-w-xl mx-auto px-4 py-10">
  <div class="bg-white border rounded-xl p-6">
    <h1 class="text-xl font-semibold">Vérifier un ticket</h1>

    <?php if ($result): ?>
      <div class="mt-4 p-3 rounded text-sm <?= $result["ok"] ? "bg-green-50 text-green-700" : "bg-red-50 text-red-700" ?>">
        <?= htmlspecialchars($result["msg"]) ?>
      </div>
    <?php endif; ?>

    <form method="post" class="mt-6 space-y-3">
      <div>
        <label class="text-sm text-slate-600">Token (copier/coller depuis le QR)</label>
        <textarea name="token" class="mt-1 w-full border rounded-lg px-3 py-2 h-28" required></textarea>
      </div>
      <button class="bg-slate-900 text-white rounded-lg px-4 py-2">Vérifier</button>
    </form>
  </div>
</main>
</body></html>
