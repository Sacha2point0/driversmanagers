<?php include ROOT . '/views/admin/_nav.php'; ?>
<p><a href="<?= url('/admin/participants') ?>">← Retour à la liste</a></p>
<h1><?= e($p['first_name'] . ' ' . $p['last_name']) ?></h1>
<?php if ($p['dominant']): ?>
  <p><a class="btn small" href="<?= url('/admin/participants/' . (int) $p['id'] . '/bilan') ?>">Voir son bilan</a></p>
<?php endif; ?>

<div class="card">
  <h3>Modifier</h3>
  <form method="post" action="<?= url('/admin/participants/' . (int) $p['id']) ?>" class="add-participant">
    <?= csrf_field() ?>
    <label>Prénom<input type="text" name="prenom" value="<?= e($old['prenom'] ?? $p['first_name']) ?>" required autocomplete="off"></label>
    <label>Nom<input type="text" name="nom" value="<?= e($old['nom'] ?? $p['last_name']) ?>" required autocomplete="off"></label>
    <label>Email<input type="email" name="email" value="<?= e($old['email'] ?? $p['email']) ?>" required autocomplete="off"></label>
    <label>Équipe<input type="text" name="equipe" value="<?= e($old['equipe'] ?? ($p['team_name'] ?? '')) ?>" list="teams-list" autocomplete="off" placeholder="Choisir ou saisir"></label>
    <datalist id="teams-list">
      <?php foreach ($teams as $t): ?><option value="<?= e($t['name']) ?>"><?php endforeach; ?>
    </datalist>
    <button class="btn small">Enregistrer</button>
  </form>
  <p class="help">Un changement d’email modifie l’adresse avec laquelle il se connecte.</p>
</div>

<div class="card soft">
  <h3>Supprimer</h3>
  <p class="small mt0">
    Bilan de conduite : <?= $p['dominant'] ? 'fait (' . e(\App\Services\Questionnaire::label($p)) . ')' : 'à faire' ?>.
  </p>
  <p class="small">La suppression efface définitivement le participant et son bilan de conduite. Les saisies d’équipe (code, manuel, manœuvres, cas) sont conservées.</p>
  <form method="post" action="<?= url('/admin/participants/' . (int) $p['id'] . '/supprimer') ?>">
    <?= csrf_field() ?>
    <button class="btn small corail" data-confirm="Supprimer définitivement <?= e($p['first_name'] . ' ' . $p['last_name']) ?> ? Cette action est irréversible.">Supprimer ce participant</button>
  </form>
</div>
