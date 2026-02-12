<?php
declare(strict_types=1);
require_once __DIR__ . "/../lib/auth.php";
$u = current_user();
?>
<nav class="border-b bg-white">
  <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
    <a href="/transport-ticketing/index.php" class="font-semibold">TicketChain</a>
    <div class="flex items-center gap-3 text-sm">
      <?php if ($u): ?>
        <span class="text-slate-600"><?= htmlspecialchars($u["name"]) ?> (<?= htmlspecialchars($u["role"]) ?>)</span>
        <a class="px-3 py-1 rounded bg-slate-100" href="/transport-ticketing/dashboard.php">Dashboard</a>
        <?php if ($u["role"] === "passenger"): ?>
          <a class="px-3 py-1 rounded bg-slate-100" href="/transport-ticketing/passenger/buy.php">Acheter</a>
          <a class="px-3 py-1 rounded bg-slate-100" href="/transport-ticketing/passenger/tickets.php">Mes tickets</a>
        <?php endif; ?>
        <?php if ($u["role"] === "controller"): ?>
          <a class="px-3 py-1 rounded bg-slate-100" href="/transport-ticketing/controller/verify.php">Vérifier</a>
        <?php endif; ?>
        <?php if ($u["role"] === "admin"): ?>
          <a class="px-3 py-1 rounded bg-slate-100" href="/transport-ticketing/admin/seed.php">Seed</a>
          <a href="/transport-ticketing/admin/routes.php" class="...">Routes</a>
        <?php endif; ?>
        <a class="px-3 py-1 rounded bg-red-50 text-red-700" href="/transport-ticketing/logout.php">Déconnexion</a>
      <?php else: ?>
        <a class="px-3 py-1 rounded bg-slate-100" href="/transport-ticketing/login.php">Connexion</a>
        <a class="px-3 py-1 rounded bg-slate-900 text-white" href="/transport-ticketing/register.php">Créer un compte</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
