<?php
use App\Services\Questionnaire;
use App\Services\Signs;

$p = $res['profil'];
$scores = $res['scores'];
$lecture = content('questionnaire')['lecture'];
$combis = content('combinaisons');
$niv = fn (string $c) => Questionnaire::LEVEL_LABELS[$p['niveaux'][$c]];
$noms = fn (array $codes) => implode(' et ', array_map(fn ($c) => $drivers[$c]['nom'], $codes));
$principaux = $p['principaux'];
$top = $scores[$principaux[0]];
$sorted = $scores;
arsort($sorted);
?>
<span class="kicker">Mon bilan de conduite</span>
<div class="card">
  <?php if ($p['egalite']): ?>
    <span class="small"><?= fr('Vos drivers principaux, à égalité') ?></span>
    <div class="result-tie">
      <?php foreach ($principaux as $c): ?>
        <div class="result-hero">
          <?= Signs::svg($c, 64) ?>
          <div>
            <h1 class="mt0" style="margin-bottom:2px"><?= e($drivers[$c]['nom']) ?></h1>
            <span class="pill gris"><?= e($drivers[$c]['panneau']) ?></span>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <p><?= fr(e($noms($principaux)) . ' obtiennent le même score (' . $top . '/40) : ils s’allument chez vous avec la même intensité. Lisez chacun des profils ci-dessous.') ?></p>
  <?php else: $d = $drivers[$principaux[0]]; ?>
    <div class="result-hero">
      <?= Signs::svg($principaux[0], 84) ?>
      <div>
        <span class="small"><?= $p['faible'] ? 'Votre tendance la plus présente' : 'Votre driver principal' ?></span>
        <h1 class="mt0" style="margin-bottom:2px"><?= e($d['nom']) ?></h1>
        <span class="pill gris">Votre panneau : <?= e($d['panneau']) ?></span>
      </div>
    </div>
  <?php endif; ?>
  <p class="niveau niveau-<?= e($p['niveaux'][$principaux[0]]) ?>"><?= fr('Intensité : ' . $niv($principaux[0]) . ' (' . $top . '/40)') ?></p>

  <?php if ($p['faible']): ?>
    <div class="lecture">
      <?= fr('Aucun de vos drivers ne dépasse ' . $lecture['seuil'] . '/40 : aucun ne s’impose vraiment chez vous. Sous pression, vos réactions varient sans doute selon les situations. Lisez le profil de ' . e($noms($principaux)) . ' comme une piste à observer, pas comme un portrait.') ?>
    </div>
  <?php elseif ($p['secondaires']): ?>
    <div class="lecture">
      <?php
      $parts = array_map(function ($c) use ($drivers, $scores, $niv, $top, $lecture) {
          $proche = $top - $scores[$c] <= $lecture['ecart_proche'];
          return $drivers[$c]['nom'] . ' (' . $scores[$c] . '/40, ' . $niv($c) . ($proche ? ', proche du principal' : '') . ')';
      }, $p['secondaires']);
      ?>
      <?= fr(($p['egalite'] ? 'En plus de vos drivers principaux, ' : 'Votre profil ne se résume pas à un seul driver. ')
          . (count($parts) > 1 ? ($p['egalite'] ? 'd’autres drivers sont présents : ' : 'D’autres drivers sont aussi présents : ') : ($p['egalite'] ? 'un autre driver est présent : ' : 'Un autre driver est aussi présent : '))
          . e(implode(', ', $parts)) . '. Sous pression, ils peuvent s’allumer ensemble ou à tour de rôle selon les situations.') ?>
    </div>
  <?php else: ?>
    <div class="lecture">
      <?= fr('Votre driver principal se détache nettement des autres : c’est lui qui risque le plus de prendre le volant quand la pression monte.') ?>
    </div>
  <?php endif; ?>
</div>

<h2>Mon tableau de bord</h2>
<div class="card">
  <div class="gauges">
    <?php foreach ($sorted as $code => $score):
      $pct = $max > $min ? round(100 * ($score - $min) / ($max - $min)) : 0; ?>
      <div class="gauge niveau-<?= e($p['niveaux'][$code]) ?> <?= in_array($code, $principaux, true) ? 'top' : '' ?>">
        <?= Signs::svg($code, 30) ?>
        <span><?= e($drivers[$code]['nom']) ?></span>
        <span class="track" style="--seuil:<?= round(100 * $lecture['seuil'] / max(1, $max)) ?>%"><i style="width:<?= max(4, $pct) ?>%"></i></span>
        <span class="right"><?= (int) $score ?></span>
      </div>
    <?php endforeach; ?>
  </div>
  <p class="help"><?= fr('Le trait sur chaque jauge marque ' . $lecture['seuil'] . '/40. Jusqu’à ' . $lecture['seuil'] . ', un driver est peu déterminant ; de ' . ($lecture['seuil'] + 1) . ' à ' . $lecture['tres_marque'] . ', il est marqué ; au-delà, très marqué. Chacun de nous a les 5 drivers en lui, à des intensités différentes : un driver n’est pas un trait de caractère, c’est un comportement qui s’allume sous pression.') ?></p>
</div>

<?php foreach ($principaux as $c): $d = $drivers[$c]; ?>
  <?php if ($p['egalite']): ?><h2 class="profil-titre"><?= Signs::svg($c, 34) ?> Profil <?= e($d['nom']) ?></h2><?php endif; ?>
  <div class="card<?= $p['egalite'] ? ' mt0' : '' ?>">
    <p class="mt0 mb0"><?= fr(e($d['synthese'])) ?></p>
  </div>

  <h2><?= $p['egalite'] ? 'Dans mon rôle de manager · ' . e($d['nom']) : 'Dans mon rôle de manager' ?></h2>
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

  <h2>Mes conseils de conduite<?= $p['egalite'] ? ' · ' . e($d['nom']) : '' ?></h2>
  <div class="card">
    <ol class="clean"><?php foreach ($d['conseils'] as $a): ?><li><?= fr(e($a)) ?></li><?php endforeach; ?></ol>
  </div>
<?php endforeach; ?>

<?php if ($p['secondaires']): ?>
  <h2>Mes autres drivers à surveiller</h2>
  <p class="small mt0"><?= fr($p['faible']
      ? 'Ces drivers sont proches de votre tendance principale, avec une intensité faible eux aussi.'
      : 'Ces drivers sont marqués ou proches de votre driver principal : ils peuvent aussi prendre le volant sous pression.') ?></p>
  <?php foreach ($p['secondaires'] as $c): $d = $drivers[$c]; ?>
    <div class="card secondaire">
      <div class="result-hero">
        <?= Signs::svg($c, 44) ?>
        <div>
          <h3 class="mt0" style="margin-bottom:2px"><?= e($d['nom']) ?></h3>
          <span class="niveau niveau-<?= e($p['niveaux'][$c]) ?>"><?= fr($niv($c) . ' (' . $scores[$c] . '/40)') ?></span>
        </div>
      </div>
      <p><?= fr(e($d['synthese'])) ?></p>
      <p class="small mb0"><strong>Voyant d’alerte :</strong> <?= fr(e(implode(' ', $d['voyant']))) ?></p>
      <p class="small mb0"><strong>Feu vert :</strong> <?= fr(e($d['feu_vert'] . '. ' . $d['feu_vert_texte'])) ?></p>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<?php if ($p['paires']): ?>
  <h2>Quand mes drivers se combinent</h2>
  <?php foreach ($p['paires'] as $key): [$a, $b] = explode('_', $key); ?>
    <div class="card soft">
      <h3 class="mt0 combi-titre"><?= Signs::svg($a, 28) ?><?= Signs::svg($b, 28) ?> <?= e($drivers[$a]['nom'] . ' + ' . $drivers[$b]['nom']) ?></h3>
      <p class="mt0 mb0"><?= fr(e($combis[$key] ?? '')) ?></p>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<div class="card soft center">
  <p class="mt0"><strong><?= fr('Rendez-vous au séminaire pour la suite du permis !') ?></strong><br>
  <span class="small"><?= fr('Votre équipe : ' . e($me['team_name'] ?: 'bientôt communiquée')) ?></span></p>
  <a class="btn secondary" href="<?= url('/espace') ?>">Retour à mon espace</a>
</div>
