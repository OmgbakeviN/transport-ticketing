<?php
declare(strict_types=1);
require_once __DIR__ . "/../lib/db.php";
require_once __DIR__ . "/../lib/auth.php";

$u = require_role(["admin"]);

$counts = [
  "users" => (int)(db_one("SELECT COUNT(*) AS c FROM users")["c"] ?? 0),
  "routes" => (int)(db_one("SELECT COUNT(*) AS c FROM routes")["c"] ?? 0),
  "trips" => (int)(db_one("SELECT COUNT(*) AS c FROM trips")["c"] ?? 0),
  "tickets" => (int)(db_one("SELECT COUNT(*) AS c FROM tickets")["c"] ?? 0),
  "used" => (int)(db_one("SELECT COUNT(*) AS c FROM tickets WHERE status = 'used'")["c"] ?? 0),
];

$last_ledger = db_one("SELECT id, event_type, created_at, hash FROM ledger ORDER BY id DESC LIMIT 1");

$title = "Dashboard Admin";
require_once __DIR__ . "/../partials/head.php";
require_once __DIR__ . "/../partials/nav.php";
?>
<main class="max-w-5xl mx-auto px-4 py-8 space-y-6">
  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-2xl font-semibold">Administration</h1>
      <p class="text-slate-600 mt-1">Gestion des données, seed et suivi global.</p>
    </div>
    <div class="flex gap-2">
      <a class="px-4 py-2 rounded-lg bg-slate-900 text-white" href="/transport-ticketing/admin/seed.php">Seed routes & trips</a>
    </div>
  </div>

  <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-4">
    <div class="bg-white border rounded-xl p-4">
      <div class="text-sm text-slate-600">Users</div>
      <div class="text-2xl font-semibold mt-1"><?= $counts["users"] ?></div>
    </div>
    <div class="bg-white border rounded-xl p-4">
      <div class="text-sm text-slate-600">Routes</div>
      <div class="text-2xl font-semibold mt-1"><?= $counts["routes"] ?></div>
    </div>
    <div class="bg-white border rounded-xl p-4">
      <div class="text-sm text-slate-600">Trips</div>
      <div class="text-2xl font-semibold mt-1"><?= $counts["trips"] ?></div>
    </div>
    <div class="bg-white border rounded-xl p-4">
      <div class="text-sm text-slate-600">Tickets</div>
      <div class="text-2xl font-semibold mt-1"><?= $counts["tickets"] ?></div>
    </div>
    <div class="bg-white border rounded-xl p-4">
      <div class="text-sm text-slate-600">Used</div>
      <div class="text-2xl font-semibold mt-1"><?= $counts["used"] ?></div>
    </div>
  </div>

  <div class="bg-white border rounded-xl p-6">
    <h2 class="font-semibold">Ledger (dernier événement)</h2>
    <?php if ($last_ledger): ?>
      <div class="mt-4 grid sm:grid-cols-2 gap-4 text-sm">
        <div class="p-4 border rounded-xl">
          <div class="text-slate-600">Event</div>
          <div class="font-medium mt-1"><?= htmlspecialchars((string)$last_ledger["event_type"]) ?></div>
          <div class="text-slate-600 mt-2">Date</div>
          <div class="font-medium mt-1"><?= htmlspecialchars((string)$last_ledger["created_at"]) ?></div>
        </div>
        <div class="p-4 border rounded-xl">
          <div class="text-slate-600">Hash</div>
          <div class="font-mono text-xs mt-1 break-all"><?= htmlspecialchars((string)$last_ledger["hash"]) ?></div>
        </div>
      </div>
    <?php else: ?>
      <div class="mt-4 text-slate-600 text-sm">Aucun événement ledger pour l’instant.</div>
    <?php endif; ?>
  </div>
</main>
</body></html>
