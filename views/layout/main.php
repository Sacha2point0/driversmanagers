<?php
/** @var string $content */
$me = \App\Core\Auth::participant();
$isAdminArea = !empty($admin);
?><!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= e(($title ?? '') !== '' ? $title . ' · ' : '') ?>Le permis de manager</title>
<link rel="icon" href="<?= asset('img/cachet.svg') ?>">
<link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body>
<header class="topbar">
  <a class="brand" href="<?= url($isAdminArea ? '/admin' : '/espace') ?>">
    <img src="<?= asset('img/cachet.svg') ?>" alt="">
    <span>Le permis de manager<?= $isAdminArea ? ' · animateur' : '' ?></span>
  </a>
  <?php if ($isAdminArea && \App\Core\Auth::isAdmin()): ?>
    <div class="who">
      <form method="post" action="<?= url('/admin/logout') ?>"><?= csrf_field() ?><button class="linkbtn">Déconnexion</button></form>
    </div>
  <?php elseif (!$isAdminArea && $me): ?>
    <div class="who">
      <strong><?= e($me['first_name'] . ' ' . $me['last_name']) ?></strong>
      <?= $me['team_name'] ? e($me['team_name']) . '<br>' : '' ?>
      <form class="inline" method="post" action="<?= url('/logout') ?>"><?= csrf_field() ?><button class="linkbtn">Déconnexion</button></form>
    </div>
  <?php endif; ?>
</header>
<main class="<?= !empty($wide) ? 'wide' : '' ?>">
<?php foreach (take_flashes() as $f): ?>
  <div class="flash <?= e($f['type']) ?>"><?= e($f['message']) ?></div>
<?php endforeach; ?>
<?= $content ?>
</main>
<footer class="footer">
  <img src="<?= asset('img/cachet.svg') ?>" alt="WORKLAB">
  <p>WORKLAB · Séminaire managers <?= e(\App\Core\Config::get('app.client', '')) ?></p>
</footer>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
