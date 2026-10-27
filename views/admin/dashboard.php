<?php
use App\Core\Settings;
use App\Services\Signs;
$drivers = content('drivers');
$pct = $participants ? round(100 * $done / $participants) : 0;
$totalDist = array_sum($distribution) ?: 1;
include ROOT . '/views/admin/_nav.php';
?>
<h1>Tableau de bord</h1>

<div class="stat-row">
  <div class="stat"><span class="small">Participants</span><b><?= $participants ?></b><span class="small"><?= $teams ?> équipes<?= $noTeam ? ' · ' . $noTeam . ' sans équipe' : '' ?></span></div>
  <div class="stat"><span class="small">Questionnaires remplis</span><b><?= $done ?> <span style="font-size:16px">(<?= $pct ?> %)</span></b><a class="small" href="<?= url('/admin/participants?filtre=a-relancer') ?>">Voir qui relancer →</a></div>
  <div class="stat"><span class="small">Rencontres</span><b><?= $encounters ?></b></div>
  <div class="stat"><span class="small">Saisies d’équipe</span><b><?= $codeCount + $manCount + $casCount ?></b><span class="small">code <?= $codeCount ?> · manœuvres <?= $manCount ?> · cas <?= $casCount ?></span></div>
</div>
<?php if ($casErrors): ?>
  <div class="flash error"><?= $casErrors ?> réponse(s) aux cas sans note : <a href="<?= url('/admin/cas') ?>">les traiter</a>.</div>
<?php endif; ?>

<div class="cols">
  <div class="card">
    <h2 class="mt0">Ouverture des séquences</h2>
    <form method="post" action="<?= url('/admin/sequences') ?>">
      <?= csrf_field() ?>
      <?php foreach (Settings::SEQUENCES as $key => $label): ?>
        <label class="switch-row" style="font-weight:700">
          <span><?= e($label) ?></span>
          <input type="checkbox" name="seq[<?= e($key) ?>]" value="1" <?= ($settings[$key] ?? '0') === '1' ? 'checked' : '' ?> style="width:24px;height:24px">
        </label>
      <?php endforeach; ?>
      <button class="btn block" style="margin-top:10px">Enregistrer</button>
    </form>
    <p class="help">Une séquence fermée n’accepte plus de saisie : fermez-la à la fin du temps imparti.</p>
  </div>

  <div class="card">
    <h2 class="mt0">Répartition des drivers dans la salle</h2>
    <div class="gauges">
      <?php foreach ($distribution as $code => $n): ?>
        <?php if ($code === 'egalite' && !$n) continue; ?>
        <div class="gauge">
          <?= $code === 'egalite' ? '<span class="center" style="font-weight:900">=</span>' : Signs::svg($code, 30) ?>
          <span><?= $code === 'egalite' ? 'Égalité entre drivers' : e($drivers[$code]['nom']) ?></span>
          <span class="track"><i style="width:<?= max(2, round(100 * $n / $totalDist)) ?>%"></i></span>
          <span class="right"><?= $n ?></span>
        </div>
      <?php endforeach; ?>
    </div>
    <p class="help">Statistique anonyme, partageable avec le client.</p>
  </div>
</div>

<h2>Classement des équipes</h2>
<div class="table-wrap">
<table class="data">
  <tr><th>#</th><th>Équipe</th><th>Code</th><th>Rencontres</th><th>Manœuvres</th><th>Cas</th><th>Total</th></tr>
  <?php foreach ($leaderboard as $i => $r): ?>
    <tr><td><?= $i + 1 ?></td><td><strong><?= e($r['name']) ?></strong></td><td><?= $r['code'] ?></td><td><?= $r['rencontres'] ?></td><td><?= $r['manoeuvres'] ?></td><td><?= $r['cas'] ?></td><td><strong><?= $r['total'] ?></strong></td></tr>
  <?php endforeach; ?>
</table>
</div>
<p><a class="btn secondary small" href="<?= url('/admin/export/classement.csv') ?>">Exporter le classement (CSV)</a></p>
