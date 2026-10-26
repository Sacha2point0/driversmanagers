<?php
use App\Services\Signs;
$old = $_SESSION['code_old'] ?? [];
unset($_SESSION['code_old']);
$total = count($jeu['cartes']);
?>
<span class="kicker">Étape 2 · En équipe</span>
<h1>L’examen du code</h1>

<?php if ($submission): ?>
  <div class="card center">
    <span class="kicker">Résultat de l’équipe</span>
    <p class="score-big"><?= (int) $submission['score'] ?> <span style="font-size:22px">/ <?= $total ?></span></p>
    <p><?= fr('Saisi par ' . e($submission['first_name'] . ' ' . $submission['last_name']) . '.') ?></p>
    <p class="small"><?= fr('Le corrigé sera discuté pendant le débrief : gardez vos arguments sur les cartes qui ont fait débat !') ?></p>
    <a class="btn secondary" href="<?= url('/espace') ?>">Retour à mon espace</a>
  </div>
<?php else: ?>
  <p class="lead"><?= fr('Quand votre équipe s’est mise d’accord sur le plateau, un seul représentant reporte ici la zone de chaque carte.') ?></p>
  <div class="flash info"><?= fr('Attention : la première saisie validée compte pour toute l’équipe et ne peut plus être modifiée.') ?></div>
  <form method="post" action="<?= url('/code') ?>" id="code-form">
    <?= csrf_field() ?>
    <div class="progress"><div class="small"><span id="c-count">0</span> / <?= $total ?> cartes placées</div><div class="bar"><i id="c-bar"></i></div></div>
    <?php foreach ($jeu['cartes'] as $no => $carte): ?>
      <div class="carte-row">
        <span class="n"><?= (int) $no ?></span><strong>Carte <?= (int) $no ?></strong>
        <p><?= fr(e($carte['texte'])) ?></p>
        <div class="zones">
          <?php foreach ($jeu['options'] as $code => $label): ?>
            <label>
              <input type="radio" name="c[<?= (int) $no ?>]" value="<?= e($code) ?>" <?= (($old[$no] ?? '') === $code) ? 'checked' : '' ?> required>
              <span><?= Signs::svg($code, 30) ?><?= e($label) ?></span>
            </label>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>
    <button class="btn block" data-confirm="Valider le placement des <?= $total ?> cartes pour toute l’équipe ?">Valider pour l’équipe</button>
  </form>
  <script>
  (function () {
    var f = document.getElementById('code-form'), t = <?= $total ?>;
    function u() { var n = f.querySelectorAll('input[type=radio]:checked').length; document.getElementById('c-count').textContent = n; document.getElementById('c-bar').style.width = (100 * n / t) + '%'; }
    f.addEventListener('change', u); u();
  })();
  </script>
<?php endif; ?>
