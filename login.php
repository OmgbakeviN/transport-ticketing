<?php
declare(strict_types=1);
require_once __DIR__ . "/lib/db.php";
require_once __DIR__ . "/lib/auth.php";

$error = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = trim($_POST["email"] ?? "");
  $password = (string)($_POST["password"] ?? "");

  $u = db_one("SELECT id, password_hash FROM users WHERE email = ?", [$email]);
  if (!$u || !password_verify($password, $u["password_hash"])) {
    $error = "Identifiants invalides.";
  } else {
    $_SESSION["uid"] = (int)$u["id"];
    header("Location: /transport-ticketing/dashboard.php");
    exit;
  }
}

$title = "Connexion";
require_once __DIR__ . "/partials/head.php";
require_once __DIR__ . "/partials/nav.php";
?>
<main class="max-w-md mx-auto px-4 py-10">
  <div class="bg-white border rounded-xl p-6">
    <h1 class="text-xl font-semibold">Connexion</h1>
    <?php if ($error): ?>
      <div class="mt-4 p-3 rounded bg-red-50 text-red-700 text-sm"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" class="mt-6 space-y-4">
      <div>
        <label class="text-sm text-slate-600">Email</label>
        <input name="email" type="email" class="mt-1 w-full border rounded-lg px-3 py-2" />
      </div>
      <div>
        <label class="text-sm text-slate-600">Mot de passe</label>
        <input name="password" type="password" class="mt-1 w-full border rounded-lg px-3 py-2" />
      </div>
      <button class="w-full bg-slate-900 text-white rounded-lg py-2">Se connecter</button>
    </form>
  </div>
</main>
</body></html>
