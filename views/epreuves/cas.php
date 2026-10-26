<?php $old = $_SESSION['cas_old'] ?? ''; unset($_SESSION['cas_old']); ?>
<span class="kicker">L’épreuve de conduite · Cas <?= (int) $no ?> sur 3</span>
<h1><?= e($c['titre']) ?></h1>

<?php if ($submission): ?>
  <div class="card">
    <span class="kicker">Réponse de l’équipe</span>
    <p style="white-space:pre-line"><?= e($submission['answer']) ?></p>
    <p class="small muted"><?= fr('Envoyée par ' . e($submission['first_name'] . ' ' . $submission['last_name']) . '.') ?></p>
  </div>
  <?php if ($submission['ai_status'] === 'done' || $submission['score_override'] !== null): ?>
    <div class="card center">
      <span class="kicker">L’avis de l’examinateur</span>
      <p class="score-big"><?= (int) $submission['final_score'] ?> <span style="font-size:22px">/ 10</span></p>
    </div>
    <?php if ($submission['feedback_ok']): ?>
      <div class="feu-vert"><h3>🟢 Ce qui fonctionne</h3><p class="mt0"><?= fr(e($submission['feedback_ok'])) ?></p></div>
    <?php endif; ?>
    <?php if ($submission['feedback_more']): ?>
      <div class="card soft"><h3>➜ Pour aller plus loin</h3><p class="mt0"><?= fr(e($submission['feedback_more'])) ?></p></div>
    <?php endif; ?>
  <?php else: ?>
    <div class="flash info"><?= fr('Votre réponse est enregistrée. L’examinateur la notera dans quelques minutes : revenez sur cette page un peu plus tard.') ?></div>
  <?php endif; ?>
<?php else: ?>
  <div class="card soft">
    <p class="mt0"><?= fr(e($c['resume'])) ?></p>
    <p class="mt0"><strong><?= fr(e($c['mission'])) ?></strong></p>
    <p class="small mt0"><?= fr('Le détail du cas est sur votre fiche papier. Rédigez en 5 à 8 lignes, comme si vous lui parliez.') ?></p>
  </div>
  <form method="post" action="<?= url('/cas/' . (int) $no) ?>" id="cas-form">
    <?= csrf_field() ?>
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
