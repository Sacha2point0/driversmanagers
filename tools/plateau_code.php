<?php
/**
 * Génère le plateau de jeu de « L'examen du code » (HTML A3 paysage, 1 page) : 6 zones, les 5 drivers
 * et « Pas forcément un driver », à partir de content/cartes.php et content/drivers.php.
 * Les cartes du jeu imprimé (environ 95 × 67 mm) se posent en éventail dans chaque zone.
 *
 *   php tools/plateau_code.php > plateau-code.html
 *   puis ouvrir dans Chrome et imprimer en PDF (A3 paysage, sans en-têtes ni pieds de page, graphiques d'arrière-plan activés).
 */
declare(strict_types=1);

$root = dirname(__DIR__);
require $root . '/src/Services/Signs.php';

use App\Services\Signs;

$cartes = require $root . '/content/cartes.php';
$drivers = require $root . '/content/drivers.php';
$nbCartes = count($cartes['cartes']);
$e = fn (string $s) => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
$fr = fn (string $s) => preg_replace(['/ ([:;!?»%])/u', '/« /u'], ["\u{202F}$1", "«\u{202F}"], $s);
$font = fn (int $w) => 'url(data:font/woff2;base64,' . base64_encode((string) file_get_contents($root . "/public/assets/fonts/nunito-latin-$w-normal.woff2")) . ') format("woff2")';
$logo = 'data:image/svg+xml;base64,' . base64_encode((string) file_get_contents($root . '/public/assets/img/cachet.svg'));

// Panneau de la zone « Pas forcément un driver » : panneau d'indication blanc à point d'interrogation
$panneauNd = '<svg width="64" height="64" viewBox="0 0 100 100" aria-hidden="true"><rect x="8" y="8" width="84" height="84" rx="14" fill="#fff" stroke="#1226AA" stroke-width="6"/><text x="50" y="70" text-anchor="middle" font-family="Nunito, sans-serif" font-weight="900" font-size="56" fill="#1226AA">?</text></svg>';
?><!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Satom · L’examen du code · Plateau de jeu</title>
<style>
<?php foreach ([600, 700, 800, 900] as $w): ?>
@font-face { font-family: Nunito; font-weight: <?= $w ?>; src: <?= $font($w) ?>; }
<?php endforeach; ?>
@page { size: A3 landscape; margin: 0; }
* { box-sizing: border-box; }
body { margin: 0; font-family: Nunito, sans-serif; color: #1226AA; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
.page { width: 420mm; height: 297mm; padding: 10mm 12mm; display: flex; flex-direction: column; position: relative; overflow: hidden; }
.tete { display: flex; align-items: center; gap: 8mm; background: #3BD4AE; border-radius: 12mm; padding: 3.5mm 4mm 3.5mm 9mm; }
.tete h1 { margin: 0; font-size: 24pt; font-weight: 900; }
.tete .consigne { flex: 1; font-size: 10.5pt; font-weight: 700; line-height: 1.35; }
.tete .equipe { font-size: 11pt; font-weight: 800; white-space: nowrap; }
.tete .equipe span { display: inline-block; width: 55mm; border-bottom: 1.5px dotted #1226AA; margin-left: 2mm; }
.tete img { width: 13mm; height: 13mm; background: #fff; border-radius: 50%; }
.zones { flex: 1; display: grid; grid-template-columns: repeat(3, 1fr); grid-template-rows: repeat(2, 1fr); gap: 6mm; margin-top: 6mm; }
.zone { border: 2px solid #1226AA; border-radius: 7mm; display: flex; flex-direction: column; overflow: hidden; }
.zone .titre { display: flex; align-items: center; gap: 4mm; padding: 4mm 5mm; background: #EEF0FB; }
.zone .titre strong { display: block; font-size: 20pt; font-weight: 900; line-height: 1.05; }
.zone .titre small { display: block; font-size: 10pt; font-weight: 700; margin-top: 1mm; }
.zone .depot { flex: 1; margin: 4mm; border: 2px dashed #C9CEEB; border-radius: 5mm; display: flex; align-items: center; justify-content: center; font-size: 10pt; font-weight: 700; color: #9aa3dd; }
.zone.nd { border-color: #FF8672; border-style: dashed; }
.zone.nd .titre { background: #FFE6E1; }
.pied { margin: 4mm 0 0; text-align: center; font-size: 9.5pt; font-weight: 700; }
</style>
</head>
<body>
<section class="page">
  <div class="tete">
    <h1>L’examen du code</h1>
    <div class="consigne"><?= $fr($e("Placez chacune des $nbCartes cartes dans une seule zone. Si le comportement s’explique par le métier ou le contexte, choisissez « Pas forcément un driver ».")) ?></div>
    <div class="equipe">Équipe <span></span></div>
    <img src="<?= $logo ?>" alt="">
  </div>
  <div class="zones">
    <?php foreach ($drivers as $code => $d): ?>
      <div class="zone">
        <div class="titre"><?= Signs::svg($code, 64) ?><div><strong><?= $e($d['nom']) ?></strong><small><?= $e($d['panneau']) ?></small></div></div>
        <div class="depot">Déposez les cartes ici</div>
      </div>
    <?php endforeach; ?>
    <div class="zone nd">
      <div class="titre"><?= $panneauNd ?><div><strong><?= $e($cartes['options']['nd']) ?></strong><small><?= $e('Le métier ou le contexte l’explique') ?></small></div></div>
      <div class="depot">Déposez les cartes ici</div>
    </div>
  </div>
  <p class="pied"><?= $fr($e('Quand toutes les cartes sont placées, le représentant de l’équipe saisit le plateau dans l’application : 1 point par carte bien placée.')) ?></p>
</section>
</body>
</html>
