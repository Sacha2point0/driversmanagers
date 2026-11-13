<?php
/**
 * Génère le plateau rond de « L'examen du code » pour tables rondes (HTML A1 portrait, 1 page, disque de 58 cm à découper) :
 * 6 secteurs, les 5 drivers et « Pas forcément un driver », à partir de content/cartes.php et content/drivers.php.
 * Chaque intitulé est tourné vers l'extérieur, pour être lu par les personnes assises de ce côté de la table.
 * Les cartes du jeu imprimé (environ 95 × 67 mm) se posent dans la bande pointillée de chaque secteur.
 *
 *   php tools/plateau_code.php > plateau-code.html
 *   puis ouvrir dans Chrome et imprimer en PDF (A1, sans en-têtes ni pieds de page, graphiques d'arrière-plan activés).
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

// Géométrie (mm) : page A1 portrait, disque centré
$W = 594; $H = 841;
$cx = $W / 2; $cy = $H / 2;
$R = 290;          // rayon du plateau
$rCentre = 88;     // disque central
$rDepot = [100, 224]; // bande où l'on pose les cartes
$rTitre = 261;     // intitulé du secteur

$zones = [];
foreach ($drivers as $code => $d) {
    $zones[] = ['sign' => Signs::svg($code, 22), 'nom' => $d['nom'], 'sous' => $d['panneau'], 'nd' => false];
}
$zones[] = [
    'sign' => '<svg width="22" height="22" viewBox="0 0 100 100"><rect x="8" y="8" width="84" height="84" rx="14" fill="#fff" stroke="#1226AA" stroke-width="6"/><text x="50" y="70" text-anchor="middle" font-family="Nunito, sans-serif" font-weight="900" font-size="56" fill="#1226AA">?</text></svg>',
    'nom' => $cartes['options']['nd'], 'sous' => 'Le métier ou le contexte l’explique', 'nd' => true,
];
$n = count($zones);
$pas = 360 / $n;

$pt = fn (float $r, float $deg) => [round($cx + $r * cos(deg2rad($deg)), 2), round($cy + $r * sin(deg2rad($deg)), 2)];
// Secteur d'anneau entre r1 et r2, de a1 à a2 degrés
$anneau = function (float $r1, float $r2, float $a1, float $a2) use ($pt): string {
    [$x1, $y1] = $pt($r2, $a1); [$x2, $y2] = $pt($r2, $a2);
    [$x3, $y3] = $pt($r1, $a2); [$x4, $y4] = $pt($r1, $a1);
    return "M$x1 $y1 A$r2 $r2 0 0 1 $x2 $y2 L$x3 $y3 A$r1 $r1 0 0 0 $x4 $y4 Z";
};
?><!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Satom · L’examen du code · Plateau rond</title>
<style>
<?php foreach ([600, 700, 800, 900] as $w): ?>
@font-face { font-family: Nunito; font-weight: <?= $w ?>; src: <?= $font($w) ?>; }
<?php endforeach; ?>
@page { size: 594mm 841mm; margin: 0; }
body { margin: 0; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
svg.plateau { display: block; width: <?= $W ?>mm; height: <?= $H ?>mm; font-family: Nunito, sans-serif; }
</style>
</head>
<body>
<svg class="plateau" viewBox="0 0 <?= $W ?> <?= $H ?>" xmlns="http://www.w3.org/2000/svg">
  <!-- Repère de découpe -->
  <circle cx="<?= $cx ?>" cy="<?= $cy ?>" r="<?= $R + 3 ?>" fill="none" stroke="#9aa3dd" stroke-width=".5" stroke-dasharray="3 3"/>
  <text x="<?= $cx ?>" y="<?= $cy - $R - 8 ?>" text-anchor="middle" font-size="5" font-weight="700" fill="#9aa3dd">✂ Découper le long du cercle pointillé</text>

  <circle cx="<?= $cx ?>" cy="<?= $cy ?>" r="<?= $R ?>" fill="#fff" stroke="#1226AA" stroke-width="2"/>
  <?php foreach ($zones as $i => $z):
      $a1 = 90 - $pas / 2 + $i * $pas; // secteur 0 centré en bas, puis sens des aiguilles d'une montre
      $a2 = $a1 + $pas;
      $mid = $a1 + $pas / 2;
      $fond = $z['nd'] ? '#FFE6E1' : ($i % 2 ? '#EEF0FB' : '#F6F7FD');
      [$tx, $ty] = $pt($rTitre, $mid);
      $rot = $mid - 90;
  ?>
    <path d="<?= $anneau($rCentre, $R, $a1, $a2) ?>" fill="<?= $fond ?>" stroke="#1226AA" stroke-width="1.2"/>
    <path d="<?= $anneau($rDepot[0], $rDepot[1], $a1 + 3, $a2 - 3) ?>" fill="#fff" stroke="<?= $z['nd'] ? '#FF8672' : '#C9CEEB' ?>" stroke-width="1" stroke-dasharray="4 3"/>
    <g transform="translate(<?= $tx ?> <?= $ty ?>) rotate(<?= $rot ?>)">
      <g transform="translate(-11 -30)"><?= $z['sign'] ?></g>
      <text y="5" text-anchor="middle" font-size="12" font-weight="900" fill="#1226AA"><?= $e($z['nom']) ?></text>
      <text y="14" text-anchor="middle" font-size="6" font-weight="700" fill="#1226AA"><?= $e($z['sous']) ?></text>
    </g>
    <?php [$dx, $dy] = $pt(($rDepot[0] + $rDepot[1]) / 2, $mid); ?>
    <text transform="translate(<?= $dx ?> <?= $dy ?>) rotate(<?= $rot ?>)" text-anchor="middle" font-size="6" font-weight="700" fill="#9aa3dd">Déposez les cartes ici</text>
  <?php endforeach; ?>

  <!-- Centre -->
  <circle cx="<?= $cx ?>" cy="<?= $cy ?>" r="<?= $rCentre ?>" fill="#3BD4AE" stroke="#1226AA" stroke-width="2"/>
  <image href="<?= $logo ?>" x="<?= $cx - 9 ?>" y="<?= $cy - 66 ?>" width="18" height="18"/>
  <text x="<?= $cx ?>" y="<?= $cy - 30 ?>" text-anchor="middle" font-size="15" font-weight="900" fill="#1226AA">L’examen du code</text>
  <text text-anchor="middle" font-size="6.2" font-weight="700" fill="#1226AA">
    <tspan x="<?= $cx ?>" y="<?= $cy - 15 ?>"><?= $e("Placez chacune des $nbCartes cartes") ?></tspan>
    <tspan x="<?= $cx ?>" y="<?= $cy - 6 ?>"><?= $e('dans une seule zone.') ?></tspan>
    <tspan x="<?= $cx ?>" y="<?= $cy + 6 ?>"><?= $fr($e('Puis le représentant saisit le plateau')) ?></tspan>
    <tspan x="<?= $cx ?>" y="<?= $cy + 15 ?>"><?= $fr($e('dans l’application : 1 point par carte.')) ?></tspan>
  </text>
  <text x="<?= $cx - 50 ?>" y="<?= $cy + 42 ?>" font-size="7" font-weight="800" fill="#1226AA">Équipe</text>
  <line x1="<?= $cx - 25 ?>" y1="<?= $cy + 43 ?>" x2="<?= $cx + 52 ?>" y2="<?= $cy + 43 ?>" stroke="#1226AA" stroke-width=".6" stroke-dasharray="1.5 1.5"/>
</svg>
</body>
</html>
