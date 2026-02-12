<?php
declare(strict_types=1);
require_once __DIR__ . "/lib/db.php";
require_once __DIR__ . "/lib/auth.php";

$error = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = trim($_POST["name"] ?? "");
  $email = trim($_POST["email"] ?? "");
  $password = (string)($_POST["password"] ?? "");
  $role = $_POST["role"] ?? "passenger";

  if ($name === "" || $email === "" || $password === "") {
    $error = "Tous les champs sont obligatoires.";
  } elseif (!in_array($role, ["passenger","controller","admin"], true)) {
    $error = "Rôle invalide.";
  } else {
    $exists = db_one("SELECT id FROM users WHERE email = ?", [$email]);
    if ($exists) {
      $error = "Email déjà utilisé.";
    } else {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      db_exec("INSERT INTO users (name, email, password_hash, role) VALUES (?,?,?,?)", [$name, $email, $hash, $role]);
      header("Location: /transport-ticketing/login.php");
      exit;
    }
  }
}

$title = "Créer un compte";
require_once __DIR__ . "/partials/head.php";
require_once __DIR__ . "/partials/nav.php";
?>
<main class="max-w-md mx-auto px-4 py-10">
  <div class="bg-white border rounded-xl p-6">
    <h1 class="text-xl font-semibold">Créer un compte</h1>
    <?php if ($error): ?>
      <div class="mt-4 p-3 rounded bg-red-50 text-red-700 text-sm"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" class="mt-6 space-y-4">
      <div>
        <label class="text-sm text-slate-600">Nom</label>
        <input name="name" class="mt-1 w-full border rounded-lg px-3 py-2" />
      </div>
      <div>
        <label class="text-sm text-slate-600">Email</label>
        <input name="email" type="email" class="mt-1 w-full border rounded-lg px-3 py-2" />
      </div>
      <div>
        <label class="text-sm text-slate-600">Mot de passe</label>
        <input name="password" type="password" class="mt-1 w-full border rounded-lg px-3 py-2" />
      </div>
      <div>
        <label class="text-sm text-slate-600">Rôle</label>
        <select name="role" class="mt-1 w-full border rounded-lg px-3 py-2">
          <option value="passenger">passenger</option>
          <option value="controller">controller</option>
          <option value="admin">admin</option>
        </select>
      </div>
      <button class="w-full bg-slate-900 text-white rounded-lg py-2">Créer</button>
    </form>
  </div>
</main>
</body></html>
