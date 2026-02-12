<?php
declare(strict_types=1);
$title = "Accueil";
require_once __DIR__ . "/partials/head.php";
require_once __DIR__ . "/partials/nav.php";
?>
<main class="max-w-5xl mx-auto px-4 py-10">
  <div class="bg-white rounded-xl border p-6">
    <h1 class="text-2xl font-semibold">Paiement & vérification de tickets</h1>
    <p class="mt-2 text-slate-600">
      MVP local PHP + MySQL. Les tickets sont signés (HMAC) et chaque événement est chaîné dans un ledger (hash).
    </p>
    <div class="mt-6 grid md:grid-cols-3 gap-4">
      <div class="p-4 rounded-lg bg-slate-50 border">
        <div class="font-medium">Passager</div>
        <div class="text-sm text-slate-600 mt-1">Achète un ticket → QR code</div>
      </div>
      <div class="p-4 rounded-lg bg-slate-50 border">
        <div class="font-medium">Contrôleur</div>
        <div class="text-sm text-slate-600 mt-1">Vérifie ticket → marque “used”</div>
      </div>
      <div class="p-4 rounded-lg bg-slate-50 border">
        <div class="font-medium">Audit</div>
        <div class="text-sm text-slate-600 mt-1">Ledger chaîné en base</div>
      </div>
    </div>
  </div>
</main>
</body></html>
