<?php
use App\Services\Signs;
include ROOT . '/views/admin/_nav.php';
$n = count($subs);
?>
<h1>L’examen du code</h1>
<p><?= $n ?> équipe(s) ont saisi leur plateau.</p>

<h2>Par carte (pour le débrief)</h2>
<p class="small">Les cartes les moins réussies, ou les plus dispersées, sont les meilleures candidates pour la discussion.</p>
<div class="table-wrap">
<table class="data">
  <tr><th>Carte</th><th>Réussite</th><?php foreach ($cfg['options'] as $code => $label): ?><th class="center"><?= Signs::svg($code, 24) ?><br><span class="small"><?= e($label) ?></span></th><?php endforeach; ?></tr>
  <?php foreach ($cfg['cartes'] as $no => $carte): $st = $stats[$no]; ?>
    <tr>
      <td><strong><?= $no ?>.</strong> <span class="small"><?= e($carte['texte']) ?></span></td>
      <td><strong><?= $n ? round(100 * $st['ok'] / $n) : 0 ?> %</strong></td>
      <?php foreach ($st['zones'] as $code => $count): $accepted = in_array($code, $carte['accepte'], true); ?>
        <td class="center" style="<?= $accepted ? 'background:#DFF7EF;font-weight:900' : '' ?>"><?= $count ?: '' ?></td>
      <?php endforeach; ?>
    </tr>
  <?php endforeach; ?>
</table>
</div>
<p class="help">Cases vertes : réponses acceptées.</p>

<h2>Par équipe</h2>
<div class="table-wrap">
<table class="data">
  <tr><th>Équipe</th><th>Score</th><th>Saisi le</th></tr>
  <?php foreach ($subs as $s): ?><tr><td><?= e($s['team_name']) ?></td><td><strong><?= (int) $s['score'] ?></strong> / <?= count($cfg['cartes']) ?></td><td class="small"><?= e($s['submitted_at']) ?></td></tr><?php endforeach; ?>
</table>
</div>
