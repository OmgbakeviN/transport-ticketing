<?php
declare(strict_types=1);
require_once __DIR__ . "/../lib/db.php";
require_once __DIR__ . "/../lib/auth.php";

$u = require_role(["controller"]);

$today = db_one(
  "SELECT
    COUNT(*) AS total,
    SUM(result = 'valid') AS valid_count,
    SUM(result = 'invalid') AS invalid_count
  FROM verifications
  WHERE controller_id = ? AND DATE(verified_at) = CURDATE()",
  [$u["id"]]
);

$recent = db_all(
  "SELECT v.verified_at, v.result, t.id AS ticket_id
   FROM verifications v
   LEFT JOIN tickets t ON t.id = v.ticket_id
   WHERE v.controller_id = ?
   ORDER BY v.verified_at DESC
   LIMIT 8",
  [$u["id"]]
);

$title = "Dashboard Contrôleur";
require_once __DIR__ . "/../partials/head.php";
require_once __DIR__ . "/../partials/nav.php";
?>
<main class="max-w-5xl mx-auto px-4 py-8 space-y-6">
  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-2xl font-semibold">Contrôle</h1>
      <p class="text-slate-600 mt-1">Scanne et valide les tickets en temps réel.</p>
    </div>
    <div class="flex gap-2">
      <a class="px-4 py-2 rounded-lg bg-slate-900 text-white" href="/transport-ticketing/controller/verify.php">Scanner / Vérifier</a>
    </div>
  </div>

  <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white border rounded-xl p-4">
      <div class="text-sm text-slate-600">Vérifs aujourd’hui</div>
      <div class="text-2xl font-semibold mt-1"><?= (int)($today["total"] ?? 0) ?></div>
    </div>
    <div class="bg-white border rounded-xl p-4">
      <div class="text-sm text-slate-600">Valides</div>
      <div class="text-2xl font-semibold mt-1"><?= (int)($today["valid_count"] ?? 0) ?></div>
    </div>
    <div class="bg-white border rounded-xl p-4">
      <div class="text-sm text-slate-600">Invalides</div>
      <div class="text-2xl font-semibold mt-1"><?= (int)($today["invalid_count"] ?? 0) ?></div>
    </div>
    <div class="bg-white border rounded-xl p-4">
      <div class="text-sm text-slate-600">Compte</div>
      <div class="text-base font-medium mt-1"><?= htmlspecialchars($u["email"]) ?></div>
    </div>
  </div>

  <div class="bg-white border rounded-xl p-6">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold">Dernières vérifications</h2>
      <a class="text-sm text-slate-700 underline" href="/transport-ticketing/controller/verify.php">Ouvrir le scanner</a>
    </div>

    <div class="mt-4 overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="text-left text-slate-600">
          <tr>
            <th class="py-2">Date</th>
            <th class="py-2">Ticket</th>
            <th class="py-2">Résultat</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <?php foreach ($recent as $r): ?>
            <tr>
              <td class="py-2"><?= htmlspecialchars((string)$r["verified_at"]) ?></td>
              <td class="py-2">#<?= (int)($r["ticket_id"] ?? 0) ?></td>
              <td class="py-2">
                <?php if (($r["result"] ?? "") === "valid"): ?>
                  <span class="px-2 py-1 rounded bg-green-50 text-green-700">valid</span>
                <?php else: ?>
                  <span class="px-2 py-1 rounded bg-red-50 text-red-700"><?= htmlspecialchars((string)($r["result"] ?? "invalid")) ?></span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$recent): ?>
            <tr><td class="py-3 text-slate-600" colspan="3">Aucune vérification récente.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>
</body></html>
