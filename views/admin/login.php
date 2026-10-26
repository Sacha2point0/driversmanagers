<div class="card" style="max-width:420px;margin:40px auto">
  <span class="kicker">Espace animateur</span>
  <h1>Connexion</h1>
  <form method="post" action="<?= url('/admin/login') ?>">
    <?= csrf_field() ?>
    <div class="field">
      <label for="password">Mot de passe</label>
      <input id="password" type="password" name="password" required autocomplete="current-password">
    </div>
    <button class="btn block">Entrer</button>
  </form>
</div>
