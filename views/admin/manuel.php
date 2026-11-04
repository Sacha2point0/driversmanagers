<?php
use App\Services\Signs;
include ROOT . '/views/admin/_nav.php';
$nbValid = count(array_filter($byTeam, fn ($t) => $t['validation']));
?>
<h1>Le manuel du conducteur</h1>
<p><strong><?= $total ?></strong> profils de drivers renseignés · <strong><?= $nbValid ?></strong> / <?= count($byTeam) ?> équipes ont validé leur manuel.</p>

<h2>Par équipe</h2>
<div class="table-wrap">
<table class="data">
  <tr><th>Équipe</th><th>Profils renseignés</th><th>Manuel</th></tr>
  <?php foreach ($byTeam as $t): ?>
    <tr>
      <td><?= e($t['name']) ?></td>
      <td><strong><?= $t['done'] ?> / 5</strong></td>
      <td><?= $t['validation'] ? '✓ validé à ' . e(date('H:i', strtotime((string) $t['validation']['validated_at']))) : '<span class="muted">en cours</span>' ?></td>
    </tr>
  <?php endforeach; ?>
</table>
</div>

<h2>Projeter pour le débrief</h2>
<p class="small">Affiche 9 réponses tirées au hasard, sans nom d’équipe. Rechargez la page projetée pour en tirer d’autres.</p>
<div class="table-wrap">
<table class="data">
  <tr><th>Driver</th><?php foreach ($rubriques as $r): ?><th><?= $r['icone'] ?> <?= e($r['titre']) ?></th><?php endforeach; ?></tr>
  <?php foreach ($drivers as $code => $d): ?>
    <tr>
      <td><?= Signs::svg($code, 22) ?> <strong><?= e($d['nom']) ?></strong></td>
      <?php foreach ($rubriques as $key => $r): ?>
        <td><a target="_blank" href="<?= url('/admin/projection/manuel?driver=' . $code . '&q=' . $key) ?>">Projeter ↗</a></td>
      <?php endforeach; ?>
    </tr>
  <?php endforeach; ?>
</table>
</div>
<p><a class="btn secondary small" href="<?= url('/admin/export/manuel.csv') ?>">Exporter tous les manuels (CSV)</a></p>

<h2>Toutes les réponses, driver par driver</h2>
<?php foreach ($drivers as $code => $d): $rows = $answers[$code] ?? []; ?>
  <h3><?= Signs::svg($code, 22) ?> <?= e($d['nom']) ?> <span class="pill gris"><?= count($rows) ?> équipe(s)</span></h3>
  <?php if (!$rows): ?>
    <p class="muted small">Pas encore de réponse.</p>
  <?php else: ?>
    <div class="table-wrap">
    <table class="data">
      <tr><th>Équipe</th><?php foreach ($rubriques as $r): ?><th><?= $r['icone'] ?> <?= e($r['titre']) ?></th><?php endforeach; ?></tr>
      <?php foreach ($rows as $row): ?>
        <tr>
          <td><?= e($row['team_name']) ?><?= $row['complete'] ? '' : '<br><span class="small muted">incomplet</span>' ?></td>
          <?php foreach (array_keys($rubriques) as $key): ?><td style="white-space:pre-line"><?= e($row[$key]) ?></td><?php endforeach; ?>
        </tr>
      <?php endforeach; ?>
    </table>
    </div>
  <?php endif; ?>
<?php endforeach; ?>
