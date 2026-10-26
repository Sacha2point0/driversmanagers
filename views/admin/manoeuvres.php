<?php include ROOT . '/views/admin/_nav.php'; ?>
<h1>Les manœuvres</h1>
<?php foreach ($cfg as $no => $m): $rows = array_filter($subs, fn ($s) => (int) $s['manoeuvre_no'] === $no); $tot = count($rows) ?: 1; ?>
  <div class="card">
    <h2 class="mt0">Manœuvre <?= $no ?> · <?= e($m['titre']) ?> <span class="pill gris">référence : <?= e(implode(' ', str_split($m['ordre']))) ?></span></h2>
    <p class="small">Réaction classée en premier par les équipes :</p>
    <div class="stat-row">
      <?php foreach ($first[$no] as $l => $c): ?>
        <div class="stat" style="<?= $l === $m['ordre'][0] ? 'background:#DFF7EF' : '' ?>"><span class="small">Fiche <?= $l ?></span><b><?= $c ?></b><span class="small"><?= count($rows) ? round(100 * $c / $tot) : 0 ?> %</span></div>
      <?php endforeach; ?>
    </div>
    <?php if ($rows): ?>
    <div class="table-wrap" style="margin-top:12px">
      <table class="data"><tr><th>Équipe</th><th>Classement</th><th>Points</th></tr>
        <?php foreach ($rows as $s): ?><tr><td><?= e($s['team_name']) ?></td><td><strong><?= e(implode(' ', str_split($s['ranking']))) ?></strong></td><td><?= (int) $s['score'] ?> / 5</td></tr><?php endforeach; ?>
      </table>
    </div>
    <?php endif; ?>
  </div>
<?php endforeach; ?>
