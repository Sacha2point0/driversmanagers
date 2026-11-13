<?php
use App\Services\Signs;
$drivers = content('drivers');
$epreuveOpen = $open['cas_open'];
$nbCas = count(content('cas')['cas']);
?>
<span class="kicker">Mon espace</span>
<h1>Bonjour <?= e($me['first_name']) ?></h1>

<div class="card soft">
  <div style="display:flex;gap:14px;align-items:center;flex-wrap:wrap">
    <div style="flex:1;min-width:220px">
      <span class="kicker">Mon équipe</span>
      <h2 class="mt0" style="margin-bottom:4px"><?= e($me['team_label'] ?: 'Équipe à venir') ?></h2>
      <?php if ($teammates): ?>
        <p class="small mt0"><?= e(implode(' · ', array_map(fn ($t) => $t['first_name'] . ' ' . $t['last_name'], $teammates))) ?></p>
      <?php endif; ?>
      <?php if ($me['team_id']): ?>
        <?php $nameForm = function (string $label) use ($teamInfo, $me) { ?>
          <form method="post" action="<?= url('/equipe/nom') ?>" class="team-name">
            <?= csrf_field() ?>
            <input type="text" name="nickname" value="<?= e($teamInfo['nickname'] ?? '') ?>" maxlength="<?= \App\Models\Team::MAX ?>" minlength="<?= \App\Models\Team::MIN ?>" required autocomplete="off" placeholder="Le nom de votre équipe" aria-label="Le nom de votre équipe">
            <button class="btn small"><?= e($label) ?></button>
          </form>
        <?php }; ?>
        <?php if (empty($teamInfo['nickname'])): ?>
          <p class="small" style="margin-bottom:6px"><strong><?= fr('Donnez un nom à votre équipe :') ?></strong> <?= fr('il s’affichera dans le classement de la plénière. Un seul membre le saisit, après accord de l’équipe.') ?></p>
          <?php $nameForm('Enregistrer'); ?>
        <?php else: ?>
          <details class="small">
            <summary><?= e('Nom choisi' . ($teamInfo['by_first'] ? ' par ' . $teamInfo['by_first'] : '') . ' · modifier') ?></summary>
            <?php $nameForm('Modifier'); ?>
          </details>
        <?php endif; ?>
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
      <?php $lbl = \App\Services\Questionnaire::label($me); ?>
      <span class="small"><?= str_contains($lbl, '=') ? 'Mes drivers, à égalité' : 'Mon driver' ?> : <strong><?= e($lbl) ?></strong></span>
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

  <?php // 3. Le manuel du conducteur ?>
  <a class="tile <?= $open['manuel_open'] ? '' : 'locked' ?>" href="<?= url('/manuel') ?>" style="--tile-color:var(--corail)">
    <span class="num">3</span>
    <h3>Le manuel du conducteur</h3>
    <span class="small">En équipe : le mode d’emploi de chaque driver.</span>
    <span class="status"><?php
      if (!$open['manuel_open']) echo 'Le jour J';
      else echo ($manuelOk ? '✓ Validé · ' : '') . $manuel . ' / 5 profils renseignés →';
    ?></span>
  </a>

  <?php // 4. L'épreuve de conduite ?>
  <a class="tile <?= $epreuveOpen ? '' : 'locked' ?>" href="<?= url('/epreuve') ?>" style="--tile-color:var(--vert)">
    <span class="num">4</span>
    <h3>L’épreuve de conduite</h3>
    <span class="small">En équipe : que dire au collaborateur dans 6 situations ?</span>
    <span class="status"><?= $epreuveOpen ? $casDone . ' / ' . $nbCas . ' situations envoyées →' : 'Le jour J' ?></span>
  </a>
</div>
