<?php
/**
 * Génère la fiche papier « Le manuel du conducteur » (HTML A4, 1 page) à partir de content/manuel.php
 * et content/drivers.php, pour qu'elle reste alignée avec l'appli.
 *
 *   php tools/fiche_manuel.php > fiche-manuel.html
 *   puis ouvrir dans Chrome et imprimer en PDF (A4, sans en-têtes ni pieds de page, graphiques d'arrière-plan activés).
 */
declare(strict_types=1);

$root = dirname(__DIR__);
require $root . '/src/Services/Signs.php';

use App\Services\Signs;

$cfg = require $root . '/content/manuel.php';
$drivers = require $root . '/content/drivers.php';
$e = fn (string $s) => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
// Espaces insécables avant la ponctuation haute et dans les guillemets français
$fr = fn (string $s) => preg_replace(['/ ([:;!?»%])/u', '/« /u'], ["\u{202F}$1", "«\u{202F}"], $s);
$font = fn (int $w) => 'url(data:font/woff2;base64,' . base64_encode((string) file_get_contents($root . "/public/assets/fonts/nunito-latin-$w-normal.woff2")) . ') format("woff2")';
$logo = 'data:image/svg+xml;base64,' . base64_encode((string) file_get_contents($root . '/public/assets/img/cachet.svg'));
?><!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Satom · Le manuel du conducteur · Fiche d’équipe</title>
<style>
<?php foreach ([400, 600, 700, 800, 900] as $w): ?>
@font-face { font-family: Nunito; font-weight: <?= $w ?>; src: <?= $font($w) ?>; }
<?php endforeach; ?>
@page { size: A4; margin: 0; }
* { box-sizing: border-box; }
body { margin: 0; font-family: Nunito, sans-serif; color: #1226AA; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
.page { width: 210mm; height: 297mm; padding: 12mm 13mm 10mm; position: relative; overflow: hidden; display: flex; flex-direction: column; }
.deco { position: absolute; top: -38mm; right: -38mm; width: 80mm; height: 80mm; border-radius: 50%; background: #FFE6E1; z-index: 0; }
.bandeau { position: relative; z-index: 1; display: flex; align-items: center; justify-content: space-between; background: #3BD4AE; border-radius: 10mm; padding: 3mm 3mm 3mm 7mm; font-weight: 800; font-size: 11pt; }
.bandeau img { width: 9mm; height: 9mm; background: #fff; border-radius: 50%; }
.kicker { font-weight: 800; font-size: 8pt; letter-spacing: .18em; text-transform: uppercase; margin-top: 7mm; }
h1 { font-size: 24pt; font-weight: 900; margin: 1.5mm 0 2mm; line-height: 1.1; }
.consigne { font-size: 10pt; line-height: 1.5; margin: 0 0 4mm; }
.equipe { font-size: 9pt; font-weight: 800; margin-bottom: 3mm; }
.equipe span { display: inline-block; width: 70mm; border-bottom: 1px dotted #6b77c9; margin-left: 2mm; }
.grille { flex: 1; display: grid; grid-template-columns: 38mm 1fr 1fr; grid-template-rows: auto repeat(5, 1fr); border: 1.5px solid #1226AA; border-radius: 4mm; overflow: hidden; }
.grille > div { border-top: 1px solid #D5D9F0; border-left: 1px solid #D5D9F0; padding: 3mm; }
.grille > .th { border-top: 0; background: #EEF0FB; font-weight: 800; font-size: 10pt; display: flex; flex-direction: column; justify-content: center; }
.grille > .th small { font-weight: 600; font-size: 7.5pt; line-height: 1.35; margin-top: 1mm; }
.grille > div:nth-child(3n+1) { border-left: 0; }
.driver { display: flex; flex-direction: column; align-items: flex-start; justify-content: center; gap: 1.5mm; }
.driver strong { font-size: 10.5pt; font-weight: 900; line-height: 1.15; }
.driver small { font-size: 7.5pt; line-height: 1.3; }
.pied { margin-top: 3mm; font-size: 8pt; text-align: center; font-weight: 700; }
</style>
</head>
<body>
<section class="page">
  <div class="deco"></div>
  <div class="bandeau"><span><?= $e($cfg['titre']) ?></span><img src="<?= $logo ?>" alt=""></div>
  <div class="kicker">Étape 3 · En équipe</div>
  <h1><?= $e($cfg['titre']) ?></h1>
  <p class="consigne"><?= $fr($e($cfg['consigne'])) ?></p>
  <div class="equipe">Équipe <span></span></div>
  <div class="grille">
    <div class="th">Driver</div>
    <?php foreach ($cfg['rubriques'] as $r): ?>
      <div class="th"><?= $r['icone'] ?> <?= $e($r['titre']) ?><small><?= $fr($e($r['aide'])) ?></small></div>
    <?php endforeach; ?>
    <?php foreach ($drivers as $code => $d): ?>
      <div class="driver"><?= Signs::svg($code, 34) ?><strong><?= $e($d['nom']) ?></strong><small><?= $e($d['panneau']) ?></small></div>
      <?php foreach ($cfg['rubriques'] as $r): ?><div class="case"></div><?php endforeach; ?>
    <?php endforeach; ?>
  </div>
  <p class="pied"><?= $fr($e('Quand l’équipe est d’accord, un représentant saisit chaque profil dans l’application, puis valide le manuel.')) ?></p>
</section>
</body>
</html>
