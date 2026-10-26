<span class="kicker">La conduite accompagnée</span>
<h1>Ma rencontre avec <?= e($other['first_name'] . ' ' . $other['last_name']) ?></h1>
<p class="small"><?= e($other['team_name']) ?></p>

<form method="post" action="<?= url('/rencontres/avec/' . (int) $other['id']) ?>">
  <?= csrf_field() ?>
  <?php foreach ($q['questions'] as $key => $question): ?>
    <div class="card">
      <label for="a-<?= e($key) ?>"><span class="q-icon"><?= $question['icone'] ?></span><?= e($question['titre']) ?></label>
      <p class="help mt0"><?= fr(e($question['aide'])) ?></p>
      <textarea id="a-<?= e($key) ?>" name="a[<?= e($key) ?>]" required minlength="<?= (int) $q['min_caracteres'] ?>" maxlength="600" style="min-height:80px"><?= e($old[$key] ?? '') ?></textarea>
    </div>
  <?php endforeach; ?>
  <div class="card" style="box-shadow:0 0 0 2px var(--vert)">
    <label>🎯 Selon vous, quel est son driver principal ?</label>
    <p class="help mt0"><?= fr('Fiez-vous à ce que vous avez entendu. Si c’est bien vu, vous gagnez un point bonus pour vous et votre équipe.') ?></p>
    <div class="zones zones-5">
      <?php foreach ($drivers as $code => $d): ?>
        <label>
          <input type="radio" name="guess" value="<?= e($code) ?>" <?= $oldGuess === $code ? 'checked' : '' ?> required>
          <span><?= \App\Services\Signs::svg($code, 30) ?><?= e($d['nom']) ?></span>
        </label>
      <?php endforeach; ?>
    </div>
  </div>
  <button class="btn block">Valider la rencontre</button>
  <p class="center"><a href="<?= url('/rencontres') ?>">Annuler</a></p>
</form>
