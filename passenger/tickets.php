<?php
declare(strict_types=1);
require_once __DIR__ . "/../lib/auth.php";
require_once __DIR__ . "/../lib/db.php";

$u = require_role(["passenger"]);

$tickets = db_all("
  SELECT tk.*, r.name as route_name, t.depart_at
  FROM tickets tk
  JOIN trips t ON t.id = tk.trip_id
  JOIN routes r ON r.id = t.route_id
  WHERE tk.user_id = ?
  ORDER BY tk.id DESC
", [$u["id"]]);

$title = "Mes tickets";
require_once __DIR__ . "/../partials/head.php";
require_once __DIR__ . "/../partials/nav.php";
?>
<main class="max-w-5xl mx-auto px-4 py-10">
  <div class="bg-white border rounded-xl p-6">
    <h1 class="text-xl font-semibold">Mes tickets</h1>

    <div class="mt-6 overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="text-left text-slate-600">
          <tr>
            <th class="py-2">ID</th>
            <th>Trajet</th>
            <th>Départ</th>
            <th>Status</th>
            <th>Used at</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($tickets as $tk): ?>
            <tr class="border-t">
              <td class="py-2"><?= (int)$tk["id"] ?></td>
              <td><?= htmlspecialchars($tk["route_name"]) ?></td>
              <td><?= htmlspecialchars($tk["depart_at"]) ?></td>
              <td><?= htmlspecialchars($tk["status"]) ?></td>
              <td><?= htmlspecialchars((string)($tk["used_at"] ?? "")) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  </div>
</main>
</body></html>
