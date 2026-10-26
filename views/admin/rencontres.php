<?php
use App\Services\Signs;
include ROOT . '/views/admin/_nav.php';
?>
<h1>La conduite accompagnée</h1>
<p><strong><?= $total ?></strong> rencontres enregistrées.</p>

<h2>🎯 Quels drivers se devinent le mieux ?</h2>
<p class="small">Pour chaque driver réel de la personne rencontrée : part des participants qui l’ont bien deviné, et confusion la plus fréquente. Une bonne amorce de débrief : pourquoi certains drivers passent-ils inaperçus ?</p>
<div class="table-wrap">
<table class="data">
  <tr><th>Driver réel</th><th>Devinettes</th><th>Bien devinés</th><th>Confondu le plus souvent avec</th></tr>
  <?php foreach ($guesses as $code => $g): arsort($g['wrong']); $top = array_key_first($g['wrong']); ?>
    <tr>
      <td><?= Signs::svg($code, 22) ?> <strong><?= e($drivers[$code]['nom']) ?></strong></td>
      <td><?= $g['n'] ?></td>
      <td><strong><?= $g['n'] ? round(100 * $g['ok'] / $g['n']) . ' %' : '–' ?></strong></td>
      <td><?= $top ? e($drivers[$top]['nom']) . ' (' . $g['wrong'][$top] . ')' : '–' ?></td>
    </tr>
  <?php endforeach; ?>
</table>
</div>

<h2>Projeter pour le débrief</h2>
<p class="small">Affiche, de façon anonyme, 9 réponses tirées au hasard sur ce que disent les personnes d’un driver donné. Rechargez la page projetée pour tirer d’autres réponses.</p>
<div class="table-wrap">
<table class="data">
  <tr><th>Driver de la personne rencontrée</th><?php foreach ($questions as $key => $q): ?><th><?= $q['icone'] ?> <?= e($q['titre']) ?></th><?php endforeach; ?></tr>
  <?php foreach ($drivers as $code => $d): ?>
    <tr>
      <td><?= Signs::svg($code, 22) ?> <strong><?= e($d['nom']) ?></strong></td>
      <?php foreach ($questions as $key => $q): ?>
        <td><a target="_blank" href="<?= url('/admin/projection/rencontres?driver=' . $code . '&q=' . $key) ?>">Projeter ↗</a></td>
      <?php endforeach; ?>
    </tr>
  <?php endforeach; ?>
</table>
</div>
<p><a class="btn secondary small" href="<?= url('/admin/export/rencontres-anonymes.csv') ?>">Exporter les réponses anonymisées (CSV)</a></p>

<h2>Par équipe</h2>
<div class="table-wrap">
<table class="data">
  <tr><th>Équipe</th><th>Rencontres</th><th>Membres actifs</th></tr>
  <?php foreach ($byTeam as $t): ?><tr><td><?= e($t['name']) ?></td><td><strong><?= (int) $t['n'] ?></strong></td><td><?= (int) $t['actifs'] ?> / <?= (int) $t['membres'] ?></td></tr><?php endforeach; ?>
</table>
</div>
