<?php
declare(strict_types=1);
require_once __DIR__ . "/../lib/auth.php";
require_once __DIR__ . "/../lib/db.php";

require_role(["admin"]);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  db_exec("INSERT INTO routes (name, from_location, to_location, price_xaf) VALUES (?,?,?,?)", ["Melen → Ngoa-Ekelle", "Melen", "Ngoa-Ekelle", 300]);
  db_exec("INSERT INTO routes (name, from_location, to_location, price_xaf) VALUES (?,?,?,?)", ["Obili → Mokolo", "Obili", "Mokolo", 400]);

  $routes = db_all("SELECT id FROM routes ORDER BY id DESC LIMIT 2");
  foreach ($routes as $r) {
    $rid = (int)$r["id"];
    db_exec("INSERT INTO trips (route_id, depart_at, vehicle_no, seats) VALUES (?,?,?,?)", [$rid, date("Y-m-d 08:00:00"), "BUS-001", 40]);
    db_exec("INSERT INTO trips (route_id, depart_at, vehicle_no, seats) VALUES (?,?,?,?)", [$rid, date("Y-m-d 16:00:00"), "BUS-002", 40]);
  }

  header("Location: /transport-ticketing/passenger/buy.php");
  exit;
}

$title = "Seed";
require_once __DIR__ . "/../partials/head.php";
require_once __DIR__ . "/../partials/nav.php";
?>
<main class="max-w-xl mx-auto px-4 py-10">
  <div class="bg-white border rounded-xl p-6">
    <h1 class="text-xl font-semibold">Seed (routes & trips)</h1>
    <form method="post" class="mt-6">
      <button class="bg-slate-900 text-white rounded-lg px-4 py-2">Créer des données</button>
    </form>
  </div>
</main>
</body></html>
