<?php
$ROOT = dirname(__DIR__);

require_once $ROOT . "/lib/auth.php";
require_once $ROOT . "/lib/db.php";

require_login();
require_role(["admin"]);

$flash = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    if ($action === "create") {
        $origin = trim($_POST["origin"] ?? "");
        $destination = trim($_POST["destination"] ?? "");
        $price_xaf = (int)($_POST["price_xaf"] ?? 0);

        if ($origin === "" || $destination === "" || $price_xaf <= 0) {
            $flash = ["type" => "error", "msg" => "Champs invalides. Vérifie origin, destination et prix."];
        } else {
            db_exec(
                "INSERT INTO routes (origin, destination, price_xaf) VALUES (?, ?, ?)",
                [$origin, $destination, $price_xaf]
            );
            $flash = ["type" => "success", "msg" => "Route ajoutée avec succès."];
        }
    }

    if ($action === "update") {
        $id = (int)($_POST["id"] ?? 0);
        $origin = trim($_POST["origin"] ?? "");
        $destination = trim($_POST["destination"] ?? "");
        $price_xaf = (int)($_POST["price_xaf"] ?? 0);

        if ($id <= 0 || $origin === "" || $destination === "" || $price_xaf <= 0) {
            $flash = ["type" => "error", "msg" => "Modification invalide."];
        } else {
            db_exec(
                "UPDATE routes SET origin = ?, destination = ?, price_xaf = ? WHERE id = ?",
                [$origin, $destination, $price_xaf, $id]
            );
            $flash = ["type" => "success", "msg" => "Route modifiée avec succès."];
        }
    }

    if ($action === "delete") {
        $id = (int)($_POST["id"] ?? 0);
        if ($id > 0) {
            db_exec("DELETE FROM routes WHERE id = ?", [$id]);
            $flash = ["type" => "success", "msg" => "Route supprimée."];
        }
    }
}

$routes = db_all("SELECT id, origin, destination, price_xaf, created_at FROM routes ORDER BY id DESC");
?>
<!doctype html>
<html lang="fr">
<?php require_once __DIR__ . "/../head.php"; ?>
<body class="bg-slate-950 text-slate-100">
<?php require_once __DIR__ . "/../nav.php"; ?>

<main class="max-w-5xl mx-auto px-4 py-8">
  <div class="flex items-center justify-between gap-4">
    <h1 class="text-2xl font-semibold">Admin · Gestion des routes</h1>
    <a href="../admin/dashboard.php" class="text-sky-300 hover:text-sky-200 underline">← Dashboard</a>
  </div>

  <?php if ($flash): ?>
    <div class="mt-4 p-3 rounded border <?php echo $flash["type"] === "success" ? "border-emerald-500/40 bg-emerald-500/10" : "border-rose-500/40 bg-rose-500/10"; ?>">
      <?php echo htmlspecialchars($flash["msg"]); ?>
    </div>
  <?php endif; ?>

  <section class="mt-6 p-4 rounded-xl border border-slate-800 bg-slate-900/40">
    <h2 class="text-lg font-medium">Ajouter une route</h2>

    <form method="POST" class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-3">
      <input type="hidden" name="action" value="create" />

      <div class="md:col-span-1">
        <label class="text-sm text-slate-300">Origin</label>
        <input name="origin" class="mt-1 w-full rounded bg-slate-950 border border-slate-800 px-3 py-2" placeholder="Yaoundé" required />
      </div>

      <div class="md:col-span-1">
        <label class="text-sm text-slate-300">Destination</label>
        <input name="destination" class="mt-1 w-full rounded bg-slate-950 border border-slate-800 px-3 py-2" placeholder="Douala" required />
      </div>

      <div class="md:col-span-1">
        <label class="text-sm text-slate-300">Prix (XAF)</label>
        <input name="price_xaf" type="number" min="1" class="mt-1 w-full rounded bg-slate-950 border border-slate-800 px-3 py-2" placeholder="3000" required />
      </div>

      <div class="md:col-span-1 flex items-end">
        <button class="w-full rounded bg-sky-600 hover:bg-sky-500 px-4 py-2 font-medium">Ajouter</button>
      </div>
    </form>
  </section>

  <section class="mt-6">
    <h2 class="text-lg font-medium">Routes existantes</h2>

    <div class="mt-3 overflow-auto rounded-xl border border-slate-800">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-900">
          <tr class="text-left">
            <th class="p-3">ID</th>
            <th class="p-3">Origin</th>
            <th class="p-3">Destination</th>
            <th class="p-3">Prix (XAF)</th>
            <th class="p-3">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-slate-950">
          <?php foreach ($routes as $r): ?>
            <tr class="border-t border-slate-800">
              <td class="p-3"><?php echo (int)$r["id"]; ?></td>

              <td class="p-3">
                <form method="POST" class="flex items-center gap-2">
                  <input type="hidden" name="action" value="update" />
                  <input type="hidden" name="id" value="<?php echo (int)$r["id"]; ?>" />
                  <input name="origin" value="<?php echo htmlspecialchars($r["origin"]); ?>" class="w-40 rounded bg-slate-950 border border-slate-800 px-2 py-1" />
              </td>

              <td class="p-3">
                  <input name="destination" value="<?php echo htmlspecialchars($r["destination"]); ?>" class="w-40 rounded bg-slate-950 border border-slate-800 px-2 py-1" />
              </td>

              <td class="p-3">
                  <input name="price_xaf" type="number" min="1" value="<?php echo (int)$r["price_xaf"]; ?>" class="w-32 rounded bg-slate-950 border border-slate-800 px-2 py-1" />
              </td>

              <td class="p-3 flex items-center gap-2">
                  <button class="rounded bg-slate-800 hover:bg-slate-700 px-3 py-1">Enregistrer</button>
                </form>

                <form method="POST" onsubmit="return confirm('Supprimer cette route ?');">
                  <input type="hidden" name="action" value="delete" />
                  <input type="hidden" name="id" value="<?php echo (int)$r["id"]; ?>" />
                  <button class="rounded bg-rose-600/80 hover:bg-rose-500 px-3 py-1">Supprimer</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>

          <?php if (!$routes): ?>
            <tr><td class="p-3 text-slate-400" colspan="5">Aucune route pour le moment.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>
</body>
</html>
