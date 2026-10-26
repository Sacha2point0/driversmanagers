<?php include ROOT . '/views/admin/_nav.php'; ?>
<p><a href="<?= url('/admin/participants') ?>">← Retour à la liste</a></p>
<h1><?= e($p['first_name'] . ' ' . $p['last_name']) ?></h1>

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
  <p class="help">Changer d’équipe déplace aussi les points de rencontres du participant vers sa nouvelle équipe. Un changement d’email modifie l’adresse avec laquelle il se connecte.</p>
</div>

<div class="card soft">
  <h3>Supprimer</h3>
  <p class="small mt0">
    Bilan de conduite : <?= $p['dominant'] ? 'fait (' . e($drivers[$p['dominant']]['nom']) . ')' : 'à faire' ?>.
    Rencontres enregistrées par <?= e($p['first_name']) ?> : <?= $made ?>. Rencontres où d’autres l’ont rencontré(e) : <?= $received ?>.
  </p>
  <p class="small">La suppression efface définitivement le participant, son bilan de conduite et toutes ses rencontres<?= $received ? ', y compris ' . $received . ' rencontre(s) enregistrée(s) par d’autres participants : leurs points et ceux de leur équipe baisseront' : '' ?>. Les saisies d’équipe (code, manœuvres, cas) sont conservées.</p>
  <form method="post" action="<?= url('/admin/participants/' . (int) $p['id'] . '/supprimer') ?>">
    <?= csrf_field() ?>
    <button class="btn small corail" data-confirm="Supprimer définitivement <?= e($p['first_name'] . ' ' . $p['last_name']) ?> ? Cette action est irréversible.">Supprimer ce participant</button>
  </form>
</div>
