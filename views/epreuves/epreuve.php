<?php
$cfg = content('cas');
$max = (int) $cfg['max_redaction'] + (int) $cfg['bonus_driver'];
?>
<span class="kicker">Étape 4 · En équipe</span>
<h1>L’épreuve de conduite</h1>
<p class="lead"><?= fr('Six situations, six collaborateurs. Pour chacune, débattez autour de la fiche papier : quel driver est en jeu, et que lui dites-vous ? Quand l’équipe est d’accord, un seul représentant saisit la réponse : la première saisie compte pour toute l’équipe.') ?></p>
<p class="small"><?= fr('Chaque situation vaut ' . $max . ' points : ' . (int) $cfg['max_redaction'] . ' pour votre réponse, notée par l’examinateur, et ' . (int) $cfg['bonus_driver'] . ' point bonus si vous identifiez le bon driver.') ?></p>

<?php foreach ($cas as $no => $c): $done = $casDone[$no] ?? null; ?>
  <a class="person" href="<?= url('/cas/' . $no) ?>">
    <span><strong>Situation <?= (int) $no ?> · <?= e($c['titre']) ?></strong><br><span class="team"><?= fr(e($c['resume'])) ?></span></span>
    <span><?php
      if (!$done) echo 'À rédiger →';
      elseif ($done['ai_status'] === 'done' || $done['score_override'] !== null) echo '✓ ' . (int) $done['score'] . '/' . $max;
      else echo '✓ Envoyée';
    ?></span>
  </a>
<?php endforeach; ?>

<p class="center" style="margin-top:24px"><a class="btn secondary" href="<?= url('/espace') ?>">Retour à mon espace</a></p>
