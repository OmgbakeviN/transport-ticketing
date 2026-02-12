<?php
declare(strict_types=1);
require_once __DIR__ . "/../lib/db.php";
require_once __DIR__ . "/../lib/auth.php";

$u = require_role(["passenger"]);

$stats = db_one(
  "SELECT
    SUM(status = 'paid') AS paid,
    SUM(status = 'used') AS used,
    SUM(status = 'void') AS voided,
    COUNT(*) AS total
  FROM tickets
  WHERE user_id = ?",
  [$u["id"]]
);

$title = "Dashboard Passager";
require_once __DIR__ . "/../partials/head.php";
require_once __DIR__ . "/../partials/nav.php";
?>
<main class="max-w-5xl mx-auto px-4 py-8 space-y-6">
  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-2xl font-semibold">Bonjour, <?= htmlspecialchars($u["name"]) ?></h1>
      <p class="text-slate-600 mt-1">Gère tes achats et consulte tes tickets.</p>
    </div>
    <div class="flex gap-2">
      <a class="px-4 py-2 rounded-lg bg-slate-900 text-white" href="/transport-ticketing/passenger/buy.php">Acheter un ticket</a>
      <a class="px-4 py-2 rounded-lg bg-slate-100" href="/transport-ticketing/passenger/tickets.php">Mes tickets</a>
    </div>
  </div>

  <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white border rounded-xl p-4">
      <div class="text-sm text-slate-600">Total</div>
      <div class="text-2xl font-semibold mt-1"><?= (int)($stats["total"] ?? 0) ?></div>
    </div>
    <div class="bg-white border rounded-xl p-4">
      <div class="text-sm text-slate-600">Payés</div>
      <div class="text-2xl font-semibold mt-1"><?= (int)($stats["paid"] ?? 0) ?></div>
    </div>
    <div class="bg-white border rounded-xl p-4">
      <div class="text-sm text-slate-600">Utilisés</div>
      <div class="text-2xl font-semibold mt-1"><?= (int)($stats["used"] ?? 0) ?></div>
    </div>
    <div class="bg-white border rounded-xl p-4">
      <div class="text-sm text-slate-600">Annulés</div>
      <div class="text-2xl font-semibold mt-1"><?= (int)($stats["voided"] ?? 0) ?></div>
    </div>
  </div>

  <div class="bg-white border rounded-xl p-6">
    <h2 class="font-semibold">Actions rapides</h2>
    <div class="mt-4 grid sm:grid-cols-2 gap-3">
      <a class="p-4 border rounded-xl hover:bg-slate-50" href="/transport-ticketing/passenger/buy.php">
        <div class="font-medium">Acheter un ticket</div>
        <div class="text-sm text-slate-600 mt-1">Choisir un trajet et générer un QR.</div>
      </a>
      <a class="p-4 border rounded-xl hover:bg-slate-50" href="/transport-ticketing/passenger/tickets.php">
        <div class="font-medium">Voir mes tickets</div>
        <div class="text-sm text-slate-600 mt-1">Liste, statut, QR et détails.</div>
      </a>
    </div>
  </div>
</main>
</body></html>
