<?php
use App\Services\Signs;
$drivers = content('drivers');
$epreuveOpen = $open['manoeuvres_open'] || $open['cas_open'];
?>
<span class="kicker">Mon espace</span>
<h1>Bonjour <?= e($me['first_name']) ?></h1>

<div class="card soft">
  <div style="display:flex;gap:14px;align-items:center;flex-wrap:wrap">
    <div style="flex:1;min-width:220px">
      <span class="kicker">Mon équipe</span>
      <h2 class="mt0" style="margin-bottom:4px"><?= e($me['team_name'] ?: 'Équipe à venir') ?></h2>
      <?php if ($teammates): ?>
        <p class="small mt0"><?= e(implode(' · ', array_map(fn ($t) => $t['first_name'] . ' ' . $t['last_name'], $teammates))) ?></p>
      <?php endif; ?>
    </div>
    <?php if ($team): ?>
      <div class="stat"><span class="small">Points de l’équipe</span><b><?= (int) $team['total'] ?></b><span class="small"><?= (int) $team['rank'] ?><?= $team['rank'] == 1 ? 're' : 'e' ?> place</span></div>
    <?php endif; ?>
  </div>
</div>

<div class="tiles">
  <?php // 1. Bilan de conduite ?>
  <?php if ($me['dominant']): ?>
    <a class="tile" href="<?= url('/resultat') ?>" style="--tile-color:var(--corail)">
      <span class="num">1</span>
      <h3>Mon bilan de conduite</h3>
      <span class="small">Mon driver : <strong><?= e($drivers[$me['dominant']]['nom']) ?></strong></span>
      <span class="status">✓ Fait · voir mon résultat →</span>
    </a>
  <?php elseif ($open['questionnaire_open']): ?>
    <a class="tile" href="<?= url('/questionnaire') ?>" style="--tile-color:var(--corail)">
      <span class="num">1</span>
      <h3>Mon bilan de conduite</h3>
      <span class="small"><?= fr('Le questionnaire des drivers : ' . count(content('questionnaire')['questions']) . ' affirmations, environ 10 minutes.') ?></span>
      <span class="status">À faire avant le séminaire →</span>
    </a>
  <?php else: ?>
    <div class="tile locked" style="--tile-color:var(--corail)"><span class="num">1</span><h3>Mon bilan de conduite</h3><span class="status">Fermé</span></div>
  <?php endif; ?>

  <?php // 2. L'examen du code ?>
  <a class="tile <?= $open['code_open'] ? '' : 'locked' ?>" href="<?= url('/code') ?>" style="--tile-color:var(--vert)">
    <span class="num">2</span>
    <h3>L’examen du code</h3>
    <span class="small">En équipe : reconnaître les panneaux du jeu de cartes.</span>
    <span class="status"><?php
      if (!$open['code_open']) echo 'Le jour J';
      elseif ($code) echo '✓ Saisi par ' . e($code['first_name'] ?? 'l’équipe') . ' · ' . (int) $code['score'] . ' / 20';
      else echo 'À saisir par un membre de l’équipe →';
    ?></span>
  </a>

  <?php // 3. La conduite accompagnée ?>
  <a class="tile <?= $open['rencontres_open'] ? '' : 'locked' ?>" href="<?= url('/rencontres') ?>" style="--tile-color:var(--corail)">
    <span class="num">3</span>
    <h3>La conduite accompagnée</h3>
    <span class="small">Allez à la rencontre des autres conducteurs.</span>
    <span class="status"><?= $open['rencontres_open'] ? $rencontres . ' rencontre' . ($rencontres > 1 ? 's' : '') . ' · ' . $points . ' point' . ($points > 1 ? 's' : '') . ' →' : 'Le jour J' ?></span>
  </a>

  <?php // 4. L'épreuve de conduite ?>
  <a class="tile <?= $epreuveOpen ? '' : 'locked' ?>" href="<?= url('/epreuve') ?>" style="--tile-color:var(--vert)">
    <span class="num">4</span>
    <h3>L’épreuve de conduite</h3>
    <span class="small">En équipe : les manœuvres, puis la conduite.</span>
    <span class="status"><?= $epreuveOpen ? 'Manœuvres ' . $manDone . '/3 · Cas ' . $casDone . '/3 →' : 'Le jour J' ?></span>
  </a>
</div>
