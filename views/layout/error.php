<div class="card">
  <h1><?= e($title ?? 'Oups') ?></h1>
  <p><?= e($message ?? '') ?></p>
  <a class="btn secondary" href="<?= url('/espace') ?>">Retour à mon espace</a>
</div>
