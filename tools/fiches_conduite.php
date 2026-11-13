<?php
/**
 * Génère les fiches papier de « L'épreuve de conduite » (HTML A4, 1 page par situation) à partir de
 * content/cas.php et content/drivers.php, pour qu'elles restent alignées avec l'appli.
 *
 *   php tools/fiches_conduite.php > fiches-conduite.html
 *   puis ouvrir dans Chrome et imprimer en PDF (A4, sans en-têtes ni pieds de page, graphiques d'arrière-plan activés).
 */
declare(strict_types=1);

$root = dirname(__DIR__);
require $root . '/src/Services/Signs.php';

use App\Services\Signs;

$cfg = require $root . '/content/cas.php';
$drivers = require $root . '/content/drivers.php';
$total = count($cfg['cas']);
$e = fn (string $s) => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
// Espaces insécables avant la ponctuation haute, dans les guillemets français et avant « h »
$fr = fn (string $s) => preg_replace(['/ ([:;!?»%])/u', '/« /u', '/(\d) (h\b)/u'], ["\u{202F}$1", "«\u{202F}", "$1\u{00A0}$2"], $s);
$font = fn (int $w) => 'url(data:font/woff2;base64,' . base64_encode((string) file_get_contents($root . "/public/assets/fonts/nunito-latin-$w-normal.woff2")) . ') format("woff2")';
$logo = 'data:image/svg+xml;base64,' . base64_encode((string) file_get_contents($root . '/public/assets/img/cachet.svg'));
$points = (int) $cfg['max_redaction'] + (int) $cfg['bonus_driver'];
?><!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Satom · L’épreuve de conduite · Les 6 situations</title>
<style>
<?php foreach ([400, 600, 700, 800, 900] as $w): ?>
@font-face { font-family: Nunito; font-weight: <?= $w ?>; src: <?= $font($w) ?>; }
<?php endforeach; ?>
@page { size: A4; margin: 0; }
* { box-sizing: border-box; }
body { margin: 0; font-family: Nunito, sans-serif; color: #1226AA; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
.page { width: 210mm; height: 297mm; padding: 12mm 13mm 10mm; position: relative; overflow: hidden; page-break-after: always; display: flex; flex-direction: column; }
.page:last-child { page-break-after: auto; }
.deco { position: absolute; top: -38mm; right: -38mm; width: 80mm; height: 80mm; border-radius: 50%; background: #FFE6E1; z-index: 0; }
.bandeau { position: relative; z-index: 1; display: flex; align-items: center; justify-content: space-between; background: #3BD4AE; border-radius: 10mm; padding: 3mm 3mm 3mm 7mm; font-weight: 800; font-size: 11pt; }
.bandeau img { width: 9mm; height: 9mm; background: #fff; border-radius: 50%; }
.tete { display: flex; align-items: center; gap: 4mm; margin-top: 7mm; }
.num { width: 9mm; height: 9mm; border-radius: 50%; background: #FF8672; color: #fff; font-weight: 900; font-size: 14pt; display: flex; align-items: center; justify-content: center; }
.kicker { font-weight: 800; font-size: 8pt; letter-spacing: .18em; text-transform: uppercase; }
h1 { font-size: 24pt; font-weight: 900; margin: 2mm 0 3mm; line-height: 1.1; }
h2 { font-weight: 800; font-size: 8pt; letter-spacing: .18em; text-transform: uppercase; margin: 3mm 0 1.5mm; }
.texte p { margin: 0 0 2mm; font-size: 10pt; line-height: 1.5; }
.mission { background: #EEF0FB; border-radius: 5mm; padding: 4mm 5mm; margin-top: 3mm; }
.mission strong { display: block; font-size: 11.5pt; line-height: 1.4; margin-top: 1mm; }
.mission small { display: block; font-size: 8pt; font-weight: 600; margin-top: 1.5mm; }
.drivers { margin-top: 4mm; border: 1.5px solid #1226AA; border-radius: 4mm; padding: 3mm 4mm; }
.drivers .titre { font-weight: 800; font-size: 9.5pt; }
.drivers .titre span { font-weight: 600; font-size: 8pt; }
.drivers .choix { display: flex; justify-content: space-between; margin-top: 2mm; }
.drivers .choix div { display: flex; align-items: center; gap: 1.5mm; font-weight: 800; font-size: 8.5pt; }
.drivers .case { width: 4mm; height: 4mm; border: 1.5px solid #1226AA; border-radius: 1mm; flex: none; }
.aides { display: grid; grid-template-columns: repeat(3, 1fr); gap: 3mm; margin-top: 4mm; }
.aides div { border: 1px solid #9aa3dd; border-radius: 3mm; padding: 2.5mm 3mm; font-size: 7.5pt; line-height: 1.35; font-weight: 600; }
.aides strong { display: block; font-size: 8.5pt; margin-bottom: .5mm; }
.brouillon { flex: 1; margin-top: 1mm; background-image: repeating-linear-gradient(to bottom, transparent 0, transparent 8.6mm, #C9CEEB 8.6mm, #C9CEEB 9mm); }
.pied { margin-top: 2mm; font-size: 8pt; text-align: center; font-weight: 700; }
</style>
</head>
<body>
<?php foreach ($cfg['cas'] as $no => $c): ?>
<section class="page">
  <div class="deco"></div>
  <div class="bandeau"><span>L’épreuve de conduite</span><img src="<?= $logo ?>" alt=""></div>
  <div class="tete"><span class="num"><?= (int) $no ?></span><span class="kicker">Situation <?= (int) $no ?> sur <?= $total ?></span></div>
  <h1><?= $e($c['titre']) ?></h1>
  <div class="texte">
    <h2>Le contexte</h2>
    <p><?= $fr($e($c['contexte'])) ?></p>
    <h2>La situation</h2>
    <?php foreach ($c['situation'] as $p): ?><p><?= $fr($e($p)) ?></p><?php endforeach; ?>
  </div>
  <div class="mission">
    <span class="kicker">Votre mission</span>
    <strong><?= $fr($e($c['mission'])) ?></strong>
    <small><?= $fr($e('En 5 à 8 lignes, avec vos propres mots, comme si vous lui parliez.')) ?></small>
  </div>
  <div class="drivers">
    <div class="titre">Le driver en jeu <span><?= $fr($e('· cochez-en un seul ; 1 point bonus s’il est juste')) ?></span></div>
    <div class="choix">
      <?php foreach ($drivers as $code => $d): ?>
        <div><span class="case"></span><?= Signs::svg($code, 22) ?><?= $e($d['nom']) ?></div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="aides">
    <div><strong>🔴 Ses voyants</strong><?= $fr($e('Qu’est-ce qui s’allume chez lui ou chez elle dans cette situation ?')) ?></div>
    <div><strong>🟢 Son feu vert</strong><?= $fr($e('Que reconnaître, et quelle permission lui donner ?')) ?></div>
    <div><strong>⛔ Son panneau stop</strong><?= $fr($e('Qu’est-ce qui risquerait d’aggraver les choses ?')) ?></div>
  </div>
  <h2 style="margin-top:4mm">Votre brouillon</h2>
  <div class="brouillon"></div>
  <p class="pied"><?= $fr($e('Quand toute l’équipe est d’accord, le représentant saisit le driver et la réponse dans l’application (' . $points . ' points).')) ?></p>
</section>
<?php endforeach; ?>
</body>
</html>
