<?php
$items = [
    'admin/dashboard'    => ['/admin', 'Tableau de bord'],
    'admin/participants' => ['/admin/participants', 'Participants'],
    'admin/code'         => ['/admin/code', 'Examen du code'],
    'admin/rencontres'   => ['/admin/rencontres', 'Rencontres'],
    'admin/manoeuvres'   => ['/admin/manoeuvres', 'Manœuvres'],
    'admin/cas'          => ['/admin/cas', 'Cas (IA)'],
    'admin/donnees'      => ['/admin/donnees', 'Données'],
];
?>
<nav class="admin-nav">
  <?php foreach ($items as $key => [$href, $label]): ?>
    <a href="<?= url($href) ?>" class="<?= ($nav ?? '') === $key ? 'active' : '' ?>"><?= e($label) ?></a>
  <?php endforeach; ?>
  <a href="<?= url('/pleniere') ?>" target="_blank">Écran plénière ↗</a>
</nav>
