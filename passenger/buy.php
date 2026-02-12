<?php
declare(strict_types=1);
require_once __DIR__ . "/../lib/auth.php";
require_once __DIR__ . "/../lib/db.php";
require_once __DIR__ . "/../lib/crypto.php";
require_once __DIR__ . "/../lib/ledger.php";

$u = require_role(["passenger"]);

$trips = db_all("
  SELECT t.id, t.depart_at, t.vehicle_no, r.name as route_name, r.price_xaf
  FROM trips t
  JOIN routes r ON r.id = t.route_id
  ORDER BY t.depart_at ASC
");

$ticket = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $trip_id = (int)($_POST["trip_id"] ?? 0);
  $trip = db_one("
    SELECT t.id, t.depart_at, r.price_xaf
    FROM trips t JOIN routes r ON r.id = t.route_id
    WHERE t.id = ?
  ", [$trip_id]);

  if ($trip) {
    db_exec("INSERT INTO tickets (user_id, trip_id, status, token) VALUES (?,?, 'paid', 'PENDING')", [$u["id"], $trip_id]);
    $ticket_id = (int)db()->lastInsertId();

    $issued_at = date("Y-m-d H:i:s");
    $token = make_ticket_token($ticket_id, (int)$u["id"], $trip_id, $issued_at);

    db_exec("UPDATE tickets SET token = ? WHERE id = ?", [$token, $ticket_id]);

    ledger_add("ticket_issued", [
      "ticket_id" => $ticket_id,
      "user_id" => (int)$u["id"],
      "trip_id" => $trip_id,
      "issued_at" => $issued_at
    ]);

    $ticket = db_one("SELECT * FROM tickets WHERE id = ?", [$ticket_id]);
  }
}

$title = "Acheter un ticket";
require_once __DIR__ . "/../partials/head.php";
require_once __DIR__ . "/../partials/nav.php";
?>
<main class="max-w-5xl mx-auto px-4 py-10">
  <div class="bg-white border rounded-xl p-6">
    <h1 class="text-xl font-semibold">Acheter un ticket</h1>

    <form method="post" class="mt-6 flex gap-3 flex-wrap items-end">
      <div class="min-w-[280px]">
        <label class="text-sm text-slate-600">Trajet / Départ</label>
        <select name="trip_id" class="mt-1 w-full border rounded-lg px-3 py-2" required>
          <?php foreach ($trips as $t): ?>
            <option value="<?= (int)$t["id"] ?>">
              <?= htmlspecialchars($t["route_name"]) ?> — <?= htmlspecialchars($t["depart_at"]) ?> — <?= (int)$t["price_xaf"] ?> XAF
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <button class="bg-slate-900 text-white rounded-lg px-4 py-2">Payer (demo)</button>
    </form>

    <?php if ($ticket): ?>
      <div class="mt-8 grid md:grid-cols-2 gap-6">
        <div class="p-4 border rounded-xl bg-slate-50">
          <div class="font-medium">Ticket généré</div>
          <div class="text-sm text-slate-600 mt-1">ID: <?= (int)$ticket["id"] ?></div>
          <div class="text-sm text-slate-600">Status: <?= htmlspecialchars($ticket["status"]) ?></div>
          <div class="text-xs text-slate-500 mt-3 break-all"><?= htmlspecialchars($ticket["token"]) ?></div>
        </div>

        <div class="p-4 border rounded-xl bg-white">
          <div class="font-medium">QR Code</div>
          <div class="text-sm text-slate-600 mt-1">Présente ce QR au contrôleur.</div>
          <div class="mt-4" id="qrcode"></div>
        </div>
      </div>

      <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
      <script>
        new QRCode(document.getElementById("qrcode"), {
          text: <?= json_encode($ticket["token"]) ?>,
          width: 220,
          height: 220
        });
      </script>
    <?php endif; ?>
  </div>
</main>
</body></html>
