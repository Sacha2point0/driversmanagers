<?php $last = $_SESSION['last_email'] ?? ''; unset($_SESSION['last_email']); ?>
<div class="card" style="margin-top:28px">
  <span class="kicker">Séminaire managers · <?= e(\App\Core\Config::get('app.client', '')) ?></span>
  <h1>Bienvenue à l’auto-école des drivers</h1>
  <p class="lead"><?= fr('Avant de prendre le volant, faites votre bilan de conduite : découvrez votre driver et ce qu’il change dans votre façon de manager.') ?></p>
  <form method="post" action="<?= url('/login') ?>">
    <?= csrf_field() ?>
    <div class="field">
      <label for="email">Votre adresse email professionnelle</label>
      <input id="email" type="email" name="email" required autocomplete="email" inputmode="email" value="<?= e($last) ?>" placeholder="prenom.nom@entreprise.com">
    </div>
    <button class="btn block">Entrer dans mon espace</button>
  </form>
  <p class="help"><?= fr('Utilisez l’adresse à laquelle vous avez reçu l’invitation. Vous resterez connecté sur cet appareil jusqu’au jour du séminaire.') ?></p>
</div>
