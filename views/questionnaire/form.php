<?php $total = count($q['questions']); ?>
<span class="kicker">Étape 1 · Avant le séminaire</span>
<h1>Mon bilan de conduite</h1>
<p class="lead"><?= fr(e($q['intro'])) ?></p>

<form method="post" action="<?= url('/questionnaire') ?>" id="questionnaire">
  <?= csrf_field() ?>
  <div class="progress"><div class="small"><span id="q-count">0</span> / <?= $total ?> réponses</div><div class="bar"><i id="q-bar"></i></div></div>
  <?php foreach ($q['questions'] as $i => $question): ?>
    <fieldset class="question" style="border:0">
      <p><span class="qn"><?= $i + 1 ?>.</span><?= fr(e($question['texte'])) ?></p>
      <div class="scale" role="radiogroup" style="--n: <?= count($q['echelle']) ?>">
        <?php foreach ($q['echelle'] as $v => $libelle): ?>
          <label>
            <input type="radio" name="r[<?= e($question['id']) ?>]" value="<?= (int) $v ?>" <?= (($old[$question['id']] ?? null) === $v) ? 'checked' : '' ?> required>
            <span><?= e($libelle) ?></span>
          </label>
        <?php endforeach; ?>
      </div>
    </fieldset>
  <?php endforeach; ?>
  <p class="help"><?= fr('Une fois validé, votre bilan ne peut plus être modifié.') ?></p>
  <button class="btn block">Voir mon résultat</button>
</form>
<script>
(function () {
  var form = document.getElementById('questionnaire'), total = <?= $total ?>;
  function update() {
    var n = form.querySelectorAll('input[type=radio]:checked').length;
    document.getElementById('q-count').textContent = n;
    document.getElementById('q-bar').style.width = (100 * n / total) + '%';
  }
  form.addEventListener('change', update); update();
})();
</script>
