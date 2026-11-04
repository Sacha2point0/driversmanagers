<?php
use App\Services\Manuel;
use App\Services\Signs;

$done = count(array_filter($entries, fn ($e) => Manuel::isComplete($e)));
$total = count($drivers);
?>
<span class="kicker">Étape 3 · En équipe</span>
<h1><?= e($cfg['titre']) ?></h1>
<p class="lead"><?= fr(e($cfg['consigne'])) ?></p>

<div class="stat-row">
  <div class="stat"><span class="small">Profils renseignés</span><b><?= $done ?> / <?= $total ?></b></div>
  <div class="stat"><span class="small">Points de l’équipe</span><b><?= $done * (int) \App\Core\Config::get('points.manuel', 1) ?></b></div>
</div>

<?php if ($validation): ?>
  <div class="flash success"><?= fr('Manuel validé par ' . e(trim(($validation['first_name'] ?? '') . ' ' . ($validation['last_name'] ?? ''))) . ' : il ne peut plus être modifié.') ?></div>
<?php else: ?>
  <div class="flash info"><?= fr('Chaque fiche s’enregistre séparément : vous pouvez la compléter et la corriger jusqu’à la validation du manuel par l’équipe.') ?></div>
<?php endif; ?>

<?php foreach ($drivers as $code => $d):
  $entry = $entries[$code] ?? null;
  $ok = Manuel::isComplete($entry);
  $val = fn (string $k) => (string) ($old[$code][$k] ?? ($entry[$k] ?? '')); ?>
  <div class="card manuel-fiche <?= $ok ? 'ok' : '' ?>" id="d-<?= e($code) ?>">
    <div class="manuel-tete">
      <?= Signs::svg($code, 40) ?>
      <div>
        <h2 class="mt0 mb0"><?= e($d['nom']) ?></h2>
        <span class="small"><?= e($d['panneau']) ?></span>
      </div>
      <span class="pill <?= $ok ? '' : 'gris' ?>"><?= $ok ? '✓ renseigné' : 'à compléter' ?></span>
    </div>
    <?php if ($validation): ?>
      <?php foreach ($cfg['rubriques'] as $k => $r): ?>
        <p class="mb0"><strong><?= $r['icone'] ?> <?= e($r['titre']) ?></strong></p>
        <p style="white-space:pre-line"><?= $val($k) !== '' ? e($val($k)) : '<span class="muted">Non renseigné</span>' ?></p>
      <?php endforeach; ?>
    <?php else: ?>
      <form method="post" action="<?= url('/manuel/' . $code) ?>">
        <?= csrf_field() ?>
        <div class="cols">
          <?php foreach ($cfg['rubriques'] as $k => $r): ?>
            <div class="field mt0">
              <label for="<?= e($code . '-' . $k) ?>"><?= $r['icone'] ?> <?= e($r['titre']) ?></label>
              <textarea id="<?= e($code . '-' . $k) ?>" name="<?= e($k) ?>" maxlength="<?= (int) $cfg['max_caracteres'] ?>" placeholder="<?= e($r['aide']) ?>" style="min-height:90px"><?= e($val($k)) ?></textarea>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="manuel-pied">
          <span class="small muted"><?= $entry && $entry['first_name'] ? fr(e('Enregistré par ' . $entry['first_name'] . ' ' . $entry['last_name'] . '.')) : '' ?></span>
          <button class="btn small">Enregistrer</button>
        </div>
      </form>
    <?php endif; ?>
  </div>
<?php endforeach; ?>

<?php if (!$validation): ?>
  <form method="post" action="<?= url('/manuel/valider') ?>" class="card soft center">
    <?= csrf_field() ?>
    <p class="mt0"><?= fr('Quand votre équipe a terminé, validez le manuel : ' . $done . ' profil' . ($done > 1 ? 's' : '') . ' sur ' . $total . ' renseigné' . ($done > 1 ? 's' : '') . ' à ce stade.') ?></p>
    <button class="btn" data-confirm="Valider le manuel pour toute l’équipe ? <?= $done ?> profil(s) sur <?= $total ?> renseigné(s). Plus aucune modification ne sera possible.">Valider notre manuel</button>
  </form>
<?php endif; ?>

<p class="center" style="margin-top:24px"><a class="btn secondary" href="<?= url('/espace') ?>">Retour à mon espace</a></p>
