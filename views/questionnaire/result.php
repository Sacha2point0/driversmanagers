<?php
use App\Services\Signs;
$main = $res['dominants'][0];
$d = $drivers[$main];
$others = array_slice($res['dominants'], 1);
$sorted = $res['scores'];
arsort($sorted);
?>
<span class="kicker">Mon bilan de conduite</span>
<div class="card">
  <div class="result-hero">
    <?= Signs::svg($main, 84) ?>
    <div>
      <span class="small">Votre driver dominant</span>
      <h1 class="mt0" style="margin-bottom:2px"><?= e($d['nom']) ?></h1>
      <span class="pill gris">Votre panneau : <?= e($d['panneau']) ?></span>
    </div>
  </div>
  <?php if ($others): ?>
    <p class="small"><?= fr('À égalité avec : ' . e(implode(', ', array_map(fn ($c) => $drivers[$c]['nom'], $others))) . '. Les deux s’allument chez vous avec la même intensité.') ?></p>
  <?php endif; ?>
  <p><?= fr(e($d['synthese'])) ?></p>
</div>

<h2>Mon tableau de bord</h2>
<div class="card">
  <div class="gauges">
    <?php foreach ($sorted as $code => $score):
      $pct = $max > $min ? round(100 * ($score - $min) / ($max - $min)) : 0; ?>
      <div class="gauge <?= in_array($code, $res['dominants'], true) ? 'top' : '' ?>">
        <?= Signs::svg($code, 30) ?>
        <span><?= e($drivers[$code]['nom']) ?></span>
        <span class="track"><i style="width:<?= max(4, $pct) ?>%"></i></span>
        <span class="right"><?= (int) $score ?></span>
      </div>
    <?php endforeach; ?>
  </div>
  <p class="help"><?= fr('Chacun de nous a les 5 drivers en lui, à des intensités différentes. Un driver n’est pas un trait de caractère : c’est un comportement qui s’allume sous pression.') ?></p>
</div>

<h2>Dans mon rôle de manager</h2>
<div class="cols">
  <div class="card mt0">
    <h3>Mes atouts</h3>
    <ul class="clean"><?php foreach ($d['atouts'] as $a): ?><li><?= fr(e($a)) ?></li><?php endforeach; ?></ul>
  </div>
  <div class="voyant">
    <h3>🔴 Mon voyant d’alerte</h3>
    <p class="small mt0">Quand la pression monte, je risque de…</p>
    <ul class="clean"><?php foreach ($d['voyant'] as $a): ?><li><?= fr(e($a)) ?></li><?php endforeach; ?></ul>
  </div>
</div>
<div class="card soft">
  <h3>Ce que vit mon équipe</h3>
  <p class="mt0"><?= fr(e($d['equipe'])) ?></p>
</div>

<div class="feu-vert">
  <span class="kicker">🟢 Mon feu vert</span>
  <h2 class="mt0"><?= fr(e($d['feu_vert'])) ?></h2>
  <p><?= fr(e($d['feu_vert_texte'])) ?></p>
</div>

<h2>Mes conseils de conduite</h2>
<div class="card">
  <ol class="clean"><?php foreach ($d['conseils'] as $a): ?><li><?= fr(e($a)) ?></li><?php endforeach; ?></ol>
</div>

<div class="card soft center">
  <p class="mt0"><strong><?= fr('Rendez-vous au séminaire pour la suite du permis !') ?></strong><br>
  <span class="small"><?= fr('Votre équipe : ' . e($me['team_name'] ?: 'bientôt communiquée')) ?></span></p>
  <a class="btn secondary" href="<?= url('/espace') ?>">Retour à mon espace</a>
</div>
