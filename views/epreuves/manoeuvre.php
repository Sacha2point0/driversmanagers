<span class="kicker">L’épreuve de conduite · Manœuvre <?= (int) $no ?> sur 3</span>
<h1><?= e($m['titre']) ?></h1>

<?php if ($submission): ?>
  <div class="card center">
    <span class="kicker">Classement de l’équipe</span>
    <p class="score-big" style="letter-spacing:.2em"><?= e(implode(' ', str_split($submission['ranking']))) ?></p>
    <p><strong><?= (int) $submission['score'] ?> / 5 points</strong></p>
    <p class="small"><?= fr('Saisi par ' . e($submission['first_name'] . ' ' . $submission['last_name']) . '.') ?></p>
  </div>

  <h2>Le corrigé</h2>
  <p><?= fr(e($m['resume'])) ?></p>
  <?php $ranking = (string) $submission['ranking'];
  foreach (str_split($m['ordre']) as $i => $l):
    $ok = ($ranking[$i] ?? '') === $l; ?>
    <div class="carte-row corrige <?= $ok ? 'ok' : 'ko' ?>">
      <span class="n"><?= $i + 1 ?></span><strong><?= $i + 1 ?><?= $i === 0 ? 're' : 'e' ?> : réaction <?= e($l) ?></strong>
      <span class="pill <?= $ok ? '' : 'corail' ?>"><?= $ok ? '✓ +1 point' . ($i === 0 ? ' (+1 bonus)' : '') : '✗ votre équipe : ' . e($ranking[$i] ?? '–') ?></span>
      <p><?= fr(e($m['reactions'][$l])) ?></p>
      <p class="small corrige-texte"><?= fr(e($m['pourquoi'][$l] ?? '')) ?></p>
    </div>
  <?php endforeach; ?>
  <div class="card soft">
    <strong>À retenir</strong>
    <p class="mt0"><?= fr(e($m['a_retenir'] ?? '')) ?></p>
    <p class="small mb0"><?= fr('Les rangs 2 et 3 se discutent, c’est voulu : l’essentiel est de repérer la réaction la plus aidante, et de comprendre pourquoi la pire est la pire.') ?></p>
  </div>
<?php else: ?>
  <p><?= fr(e($m['resume'])) ?></p>
  <div class="card">
    <h3>Rappel des 4 fiches</h3>
    <?php foreach ($m['reactions'] as $l => $txt): ?>
      <p class="small mt0"><strong><?= e($l) ?></strong> · <?= fr(e($txt)) ?></p>
    <?php endforeach; ?>
  </div>
  <form method="post" action="<?= url('/manoeuvres/' . (int) $no) ?>" class="card">
    <?= csrf_field() ?>
    <h3>Votre classement, de la plus aidante (1) à la moins aidante (4)</h3>
    <div class="rank-grid">
      <?php for ($i = 1; $i <= 4; $i++): ?>
        <label><?= $i ?><?= $i === 1 ? 're' : 'e' ?>
          <select name="rang[<?= $i ?>]" required>
            <option value="">–</option>
            <?php foreach (['A', 'B', 'C', 'D'] as $l): ?><option value="<?= $l ?>"><?= $l ?></option><?php endforeach; ?>
          </select>
        </label>
      <?php endfor; ?>
    </div>
    <p class="help">Chaque lettre ne doit apparaître qu’une fois.</p>
    <button class="btn block" data-confirm="Valider ce classement pour toute l’équipe ?">Valider pour l’équipe</button>
  </form>
<?php endif; ?>
<p class="center"><a href="<?= url('/epreuve') ?>">← Retour à l’épreuve</a></p>
