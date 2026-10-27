<?php
use App\Services\Diagnostic;
use App\Services\Questionnaire;
use App\Services\Signs;

$p = $res['profil'];
$scores = $res['scores'];
$lecture = content('questionnaire')['lecture'];
$diag_c = content('diagnostic');
$diag = Diagnostic::build($scores);
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
      <?= fr($p['egalite']
          ? 'Ces drivers se détachent nettement des autres : ce sont eux qui risquent le plus de prendre le volant quand la pression monte, ensemble ou à tour de rôle.'
          : 'Votre driver principal se détache nettement des autres : c’est lui qui risque le plus de prendre le volant quand la pression monte.') ?>
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
  <p class="help"><?= fr('Le trait sur chaque jauge marque ' . $lecture['seuil'] . '/40. Jusqu’à ' . $lecture['seuil'] . ', un driver est peu déterminant ; de ' . ($lecture['seuil'] + 1) . ' à ' . $lecture['tres_marque'] . ', il est marqué ; au-delà, très marqué.') ?></p>
  <div class="definition">
    <h3 class="mt0">Qu’est-ce qu’un driver ?</h3>
    <?php foreach ($diag_c['definition'] as $para): ?><p><?= fr(e($para)) ?></p><?php endforeach; ?>
  </div>
</div>

<h2>Mon diagnostic</h2>
<div class="card diagnostic">
  <?php foreach ($diag['paragraphes'] as $para): ?><p><?= fr(e($para)) ?></p><?php endforeach; ?>
</div>

<?php
$sig = $diag['significatifs'];
$pr = $diag['principaux'];
$nomsSig = Diagnostic::liste(array_map(fn ($c) => $drivers[$c]['nom'], $sig));
?>
<h2>Dans mon rôle de manager</h2>
<?php if (count($sig) > 1): ?><p class="small mt0"><?= fr('À partir de vos drivers les plus présents : ' . e($nomsSig) . '.') ?></p><?php endif; ?>
<div class="cols">
  <div class="card mt0">
    <h3>Mes atouts</h3>
    <ul class="clean"><?php foreach (Diagnostic::pick($sig, $pr, 'atouts') as $a): ?><li><?= fr(e($a)) ?></li><?php endforeach; ?></ul>
  </div>
  <div class="voyant">
    <h3>🔴 Mon voyant d’alerte</h3>
    <p class="small mt0">Quand la pression monte, je risque de…</p>
    <ul class="clean"><?php foreach (Diagnostic::pick($sig, $pr, 'voyant') as $a): ?><li><?= fr(e($a)) ?></li><?php endforeach; ?></ul>
  </div>
</div>
<div class="card soft">
  <h3>Ce que vit mon équipe</h3>
  <p class="mt0"><?= fr(e($drivers[$sig[0]]['equipe'])) ?><?php foreach (array_slice($sig, 1) as $c): ?> <?= fr(e($drivers[$c]['equipe_court'])) ?><?php endforeach; ?></p>
</div>

<div class="feu-vert">
  <span class="kicker">🟢 Mon feu vert</span>
  <h2 class="mt0"><?= fr(e($drivers[$sig[0]]['feu_vert'])) ?></h2>
  <p><?= fr(e($drivers[$sig[0]]['feu_vert_texte'])) ?></p>
  <?php foreach (array_slice($sig, 1) as $c): ?>
    <p class="mb0"><strong><?= fr(e($drivers[$c]['feu_vert'])) ?></strong> : <?= fr(e($drivers[$c]['feu_vert_texte'])) ?></p>
  <?php endforeach; ?>
</div>

<h2>Mes conseils de conduite</h2>
<div class="card">
  <ol class="clean"><?php foreach (Diagnostic::pick($sig, $pr, 'conseils') as $a): ?><li><?= fr(e($a)) ?></li><?php endforeach; ?></ol>
</div>

<div class="card soft center">
  <p class="mt0"><strong><?= fr('Rendez-vous au séminaire pour la suite du permis !') ?></strong><br>
  <span class="small"><?= fr('Votre équipe : ' . e($me['team_name'] ?: 'bientôt communiquée')) ?></span></p>
  <a class="btn secondary" href="<?= url('/espace') ?>">Retour à mon espace</a>
</div>
