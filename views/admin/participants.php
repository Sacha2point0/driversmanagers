<?php include ROOT . '/views/admin/_nav.php'; ?>
<h1>Participants</h1>

<div class="card soft">
  <h3>Importer ou mettre à jour la liste</h3>
  <p class="small mt0">Fichier CSV avec une ligne d’en-tête : <code>email;prenom;nom;equipe</code>. Les équipes sont créées automatiquement. Un email déjà présent est mis à jour (nom, équipe) sans toucher à son questionnaire.</p>
  <form method="post" action="<?= url('/admin/import') ?>" enctype="multipart/form-data" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
    <?= csrf_field() ?>
    <input type="file" name="csv" accept=".csv,text/csv" required>
    <button class="btn small">Importer</button>
  </form>
</div>

<p>
  <a class="btn small <?= $filter ? 'secondary' : '' ?>" href="<?= url('/admin/participants') ?>">Tous<?= $filter ? '' : ' (' . count($list) . ')' ?></a>
  <a class="btn small <?= $filter === 'a-relancer' ? '' : 'secondary' ?>" href="<?= url('/admin/participants?filtre=a-relancer') ?>">Questionnaire à faire<?= $filter ? ' (' . count($list) . ')' : '' ?></a>
  <a class="btn small secondary" href="<?= url('/admin/export/participants.csv') ?>">Exporter (CSV)</a>
  <a class="btn small secondary" href="<?= url('/admin/export/scores-questionnaire.csv') ?>">Scores détaillés (CSV)</a>
</p>
<?php if ($filter === 'a-relancer' && $list): ?>
  <div class="card soft"><span class="small">Emails à relancer (copier-coller) :</span>
    <textarea readonly style="min-height:70px;font-size:13px"><?= e(implode('; ', array_column($list, 'email'))) ?></textarea></div>
<?php endif; ?>

<div class="table-wrap">
<table class="data">
  <tr><th>Nom</th><th>Email</th><th>Équipe</th><th>Driver</th><th>Dernière connexion</th><th></th></tr>
  <?php foreach ($list as $p): ?>
    <tr>
      <td><strong><?= e($p['last_name']) ?></strong> <?= e($p['first_name']) ?></td>
      <td><?= e($p['email']) ?></td>
      <td><?= e($p['team_name'] ?? '–') ?></td>
      <td><?= $p['dominant'] ? e($drivers[$p['dominant']]['nom']) : '<span class="muted">à faire</span>' ?></td>
      <td class="small"><?= e($p['last_login_at'] ?? '–') ?></td>
      <td>
        <?php if ($p['dominant']): ?>
          <form method="post" action="<?= url('/admin/participants/' . (int) $p['id'] . '/reset') ?>" class="inline">
            <?= csrf_field() ?><button class="btn small secondary" data-confirm="Effacer le questionnaire de <?= e($p['first_name']) ?> ? Il ou elle pourra le refaire.">Réinitialiser</button>
          </form>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
</div>
