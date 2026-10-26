<span class="kicker">Étape 4 · En équipe</span>
<h1>L’épreuve de conduite</h1>
<p class="lead"><?= fr('Débattez autour des fiches papier de votre table. Quand l’équipe est d’accord, un seul représentant saisit la réponse : la première saisie compte pour toute l’équipe.') ?></p>

<h2>Les manœuvres <span class="pill gris">5 points chacune</span></h2>
<?php if (!$manOpen): ?>
  <p class="muted">Les manœuvres s’ouvriront au signal de l’examinateur.</p>
<?php else: foreach ($manoeuvres as $no => $m): $done = $manDone[$no] ?? null; ?>
  <a class="person" href="<?= url('/manoeuvres/' . $no) ?>">
    <span><strong>Manœuvre <?= (int) $no ?> · <?= e($m['titre']) ?></strong><br><span class="team"><?= fr(e($m['resume'])) ?></span></span>
    <span><?= $done ? '✓ ' . (int) $done['score'] . '/5' : 'À classer →' ?></span>
  </a>
<?php endforeach; endif; ?>

<h2>La conduite <span class="pill gris">10 points chacun</span></h2>
<?php if (!$casOpen): ?>
  <p class="muted">Les cas s’ouvriront au signal de l’examinateur.</p>
<?php else: foreach ($cas as $no => $c): $done = $casDone[$no] ?? null; ?>
  <a class="person" href="<?= url('/cas/' . $no) ?>">
    <span><strong>Cas <?= (int) $no ?> · <?= e($c['titre']) ?></strong><br><span class="team"><?= fr(e($c['resume'])) ?></span></span>
    <span><?php
      if (!$done) echo 'À rédiger →';
      elseif ($done['ai_status'] === 'done' || $done['score'] > 0) echo '✓ ' . (int) $done['score'] . '/10';
      else echo '✓ Envoyé';
    ?></span>
  </a>
<?php endforeach; endif; ?>

<p class="center" style="margin-top:24px"><a class="btn secondary" href="<?= url('/espace') ?>">Retour à mon espace</a></p>
