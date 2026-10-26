<?php
$dir = array_map(fn ($p) => [
    'id'   => (int) $p['id'],
    'n'    => $p['first_name'] . ' ' . $p['last_name'],
    't'    => (string) $p['team_name'],
    'done' => in_array((int) $p['id'], $metIds, true),
], $directory);
?>
<span class="kicker">Étape 3 · Individuel</span>
<h1>La conduite accompagnée</h1>

<div class="stat-row">
  <div class="stat"><span class="small">Mes rencontres</span><b><?= count($mine) ?></b></div>
  <div class="stat"><span class="small">Mes points de permis</span><b><?= (int) $points ?></b></div>
  <div class="stat"><span class="small">Rencontres de mon équipe</span><b><?= (int) $teamCount ?></b></div>
</div>

<div class="card">
  <h2 class="mt0">J’ai rencontré…</h2>
  <p class="small mt0"><?= fr('Allez voir quelqu’un d’une autre équipe (et hors de votre ligne hiérarchique). Échangez sur vos tableaux de bord, puis cherchez son nom ici, notez ce qu’il ou elle vous a dit et devinez son driver principal.') ?></p>
  <input type="search" id="search" placeholder="Tapez un prénom ou un nom…" autocomplete="off" aria-label="Rechercher un participant">
  <div class="search-results" id="results"></div>
</div>

<?php if ($mine): ?>
  <h2>Mes rencontres</h2>
  <?php foreach ($mine as $m): ?>
    <div class="person done"><span><strong><?= e($m['first_name'] . ' ' . $m['last_name']) ?></strong><br><span class="team"><?= e($m['team_name']) ?></span></span><span><?= $m['guess_ok'] === 1 ? '🎯 bien deviné' : '✓' ?></span></div>
  <?php endforeach; ?>
<?php endif; ?>

<script>
(function () {
  var people = <?= json_encode($dir, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;
  var base = <?= json_encode(url('/rencontres/avec/')) ?>;
  var input = document.getElementById('search'), out = document.getElementById('results');
  function norm(s) { return s.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase(); }
  function esc(s) { var d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
  function render() {
    var q = norm(input.value.trim());
    if (q.length < 2) { out.innerHTML = '<p class="help">Tapez au moins 2 lettres.</p>'; return; }
    var list = people.filter(function (p) { return norm(p.n).indexOf(q) !== -1; }).slice(0, 30);
    if (!list.length) { out.innerHTML = '<p class="help">Aucun participant trouvé dans les autres équipes.</p>'; return; }
    out.innerHTML = list.map(function (p) {
      if (p.done) return '<div class="person done"><span><strong>' + esc(p.n) + '</strong><br><span class="team">' + esc(p.t) + '</span></span><span>✓ déjà rencontré</span></div>';
      return '<a class="person" href="' + base + p.id + '"><span><strong>' + esc(p.n) + '</strong><br><span class="team">' + esc(p.t) + '</span></span><span>→</span></a>';
    }).join('');
  }
  input.addEventListener('input', render); render();
})();
</script>
