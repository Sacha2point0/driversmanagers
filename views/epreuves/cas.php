<?php
use App\Services\Signs;
$old = $_SESSION['cas_old'] ?? '';
$oldDriver = $_SESSION['cas_old_driver'] ?? '';
unset($_SESSION['cas_old'], $_SESSION['cas_old_driver']);
$drivers = content('drivers');
$maxR = (int) $limits['max_redaction'];
$bonus = (int) $limits['bonus_driver'];
$nb = count($limits['cas']);
$criteres = [
    'crit_accueil'    => 'Accueillir sa réaction',
    'crit_valorise'   => 'Valoriser son driver',
    'crit_permission' => 'Donner la permission',
    'crit_cadre'      => 'Proposer un cadre concret',
    'crit_piege'      => 'Éviter les pièges',
];
?>
<span class="kicker">L’épreuve de conduite · Situation <?= (int) $no ?> sur <?= $nb ?></span>
<h1><?= e($c['titre']) ?></h1>

<?php if ($submission): $evalue = $submission['ai_status'] === 'done' || $submission['score_override'] !== null; ?>
  <details class="card">
    <summary><strong>Relire la situation</strong></summary>
    <p><?= fr(e($c['contexte'])) ?></p>
    <?php foreach ($c['situation'] as $para): ?><p><?= fr(e($para)) ?></p><?php endforeach; ?>
    <p class="mb0"><strong><?= fr(e($c['mission'])) ?></strong></p>
  </details>
  <div class="card">
    <span class="kicker">Réponse de l’équipe</span>
    <p style="white-space:pre-line"><?= e($submission['answer']) ?></p>
    <p class="small muted"><?= fr('Envoyée par ' . e($submission['first_name'] . ' ' . $submission['last_name']) . '.') ?></p>
  </div>

  <?php $guess = $submission['driver_guess']; $ok = (bool) $submission['driver_ok']; ?>
  <div class="carte-row corrige <?= $ok ? 'ok' : 'ko' ?>">
    <strong>Le driver en jeu</strong>
    <span class="pill <?= $ok ? 'vert' : 'corail' ?>"><?= $ok ? '✓ +' . $bonus . ' point bonus' : '✗ 0 point' ?></span>
    <div class="corrige-reponses" style="margin-top:8px">
      <span><?= Signs::svg($c['driver'], 26) ?> <strong><?= e($drivers[$c['driver']]['nom']) ?></strong></span>
      <?php if (!$ok && $guess): ?><span class="small">Votre équipe : <?= e($drivers[$guess]['nom'] ?? $guess) ?></span><?php endif; ?>
    </div>
  </div>

  <?php if ($evalue): ?>
    <div class="card center">
      <span class="kicker">L’avis de l’examinateur</span>
      <p class="score-big"><?= (int) $submission['final_score'] ?> <span style="font-size:22px">/ <?= $maxR + $bonus ?></span></p>
      <p class="small mt0"><?= fr('Réponse : ' . (int) $submission['redaction'] . ' / ' . $maxR . ' · driver : ' . ($ok ? $bonus : 0) . ' / ' . $bonus) ?></p>
      <?php if ($submission['score_override'] === null && $submission['crit_accueil'] !== null): ?>
        <div class="criteres">
          <?php foreach ($criteres as $col => $label): $v = (int) $submission[$col]; ?>
            <span class="<?= $v ? 'ok' : 'ko' ?>"><?= $v ? '✓' : '✗' ?> <?= e($label) ?></span>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
    <?php if ($submission['feedback_ok']): ?>
      <div class="feu-vert"><h3>🟢 Ce qui fonctionne</h3><p class="mt0"><?= fr(e($submission['feedback_ok'])) ?></p></div>
    <?php endif; ?>
    <?php if ($submission['feedback_more']): ?>
      <div class="card soft"><h3>➜ Pour aller plus loin</h3><p class="mt0"><?= fr(e($submission['feedback_more'])) ?></p></div>
    <?php endif; ?>
    <h2>Une réponse possible</h2>
    <div class="card">
      <p class="mt0" style="font-weight:700"><?= fr(e($c['reference'])) ?></p>
      <p class="small corrige-texte"><strong>À retenir :</strong> <?= fr(e($c['a_retenir'])) ?></p>
      <p class="small mb0"><?= fr('Ce n’est pas la seule bonne réponse : l’examinateur juge l’intention et l’effet du message, pas les mots exacts.') ?></p>
    </div>
  <?php else: ?>
    <div class="flash info"><?= fr('Votre réponse est enregistrée. L’examinateur la notera dans quelques minutes : revenez sur cette page un peu plus tard.') ?></div>
  <?php endif; ?>
<?php else: ?>
  <div class="card">
    <span class="kicker">Le contexte</span>
    <p><?= fr(e($c['contexte'])) ?></p>
    <span class="kicker">La situation</span>
    <?php foreach ($c['situation'] as $para): ?><p><?= fr(e($para)) ?></p><?php endforeach; ?>
  </div>
  <div class="card soft">
    <span class="kicker">Votre mission</span>
    <p class="mt0"><strong><?= fr(e($c['mission'])) ?></strong></p>
    <p class="small mt0"><?= fr('En 5 à 8 lignes, avec vos propres mots, comme si vous lui parliez.') ?></p>
  </div>
  <form method="post" action="<?= url('/cas/' . (int) $no) ?>" id="cas-form">
    <?= csrf_field() ?>
    <div class="field">
      <label>Le driver en jeu <span class="small">(+<?= $bonus ?> point si c’est le bon)</span></label>
      <div class="zones zones-5">
        <?php foreach ($drivers as $code => $d): ?>
          <label>
            <input type="radio" name="driver" value="<?= e($code) ?>" <?= $oldDriver === $code ? 'checked' : '' ?> required>
            <span><?= Signs::svg($code, 30) ?><?= e($d['nom']) ?></span>
          </label>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="field">
      <label for="answer">Ce que vous lui dites</label>
      <textarea id="answer" name="answer" required minlength="<?= (int) $limits['longueur_min'] ?>" maxlength="<?= (int) $limits['longueur_max'] ?>" style="min-height:200px"><?= e($old) ?></textarea>
      <p class="help"><span id="len">0</span> / <?= (int) $limits['longueur_max'] ?> caractères</p>
    </div>
    <button class="btn block" id="cas-btn" data-confirm="Envoyer cette réponse à l’examinateur pour toute l’équipe ?" data-busy="L’examinateur lit votre réponse…">Envoyer à l’examinateur</button>
  </form>
  <script>
  (function () { var t = document.getElementById('answer'), l = document.getElementById('len');
    function u() { l.textContent = t.value.length; } t.addEventListener('input', u); u(); })();
  </script>
<?php endif; ?>
<p class="center"><a href="<?= url('/epreuve') ?>">← Retour à l’épreuve</a></p>
