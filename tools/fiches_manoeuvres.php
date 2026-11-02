<?php
/**
 * Génère les fiches papier des manœuvres (HTML A4, 1 page par manœuvre) à partir de content/manoeuvres.php,
 * pour qu'elles restent alignées avec l'appli.
 *
 *   php tools/fiches_manoeuvres.php > fiches.html
 *   puis ouvrir dans Chrome et imprimer en PDF (A4, sans en-têtes ni pieds de page, graphiques d'arrière-plan activés).
 */
declare(strict_types=1);

$root = dirname(__DIR__);
$manoeuvres = require $root . '/content/manoeuvres.php';
$total = count($manoeuvres);
$e = fn (string $s) => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
// Espaces insécables avant la ponctuation haute et dans les guillemets français
$fr = fn (string $s) => preg_replace(['/ ([:;!?»%])/u', '/« /u', '/(\d) (h\b)/u'], ["\u{202F}$1", "«\u{202F}", "$1\u{00A0}$2"], $s);
$font = fn (int $w) => 'url(' . 'data:font/woff2;base64,' . base64_encode((string) file_get_contents($root . "/public/assets/fonts/nunito-latin-$w-normal.woff2")) . ') format("woff2")';
$logo = 'data:image/svg+xml;base64,' . base64_encode((string) file_get_contents($root . '/public/assets/img/cachet.svg'));
?><!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Satom · L’épreuve de conduite · Les manœuvres</title>
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
.tete { display: flex; align-items: center; gap: 4mm; margin-top: 8mm; }
.num { width: 9mm; height: 9mm; border-radius: 50%; background: #FF8672; color: #fff; font-weight: 900; font-size: 14pt; display: flex; align-items: center; justify-content: center; }
.kicker { font-weight: 800; font-size: 8pt; letter-spacing: .18em; text-transform: uppercase; }
h1 { font-size: 24pt; font-weight: 900; margin: 2mm 0 3mm; line-height: 1.1; }
.situation p { margin: 0 0 2mm; font-size: 10pt; line-height: 1.5; }
.consigne { background: #EEF0FB; border-radius: 5mm; padding: 4mm 5mm; margin-top: 3mm; }
.consigne p { margin: 0 0 3mm; font-weight: 800; font-size: 10pt; line-height: 1.4; }
.cases { display: grid; grid-template-columns: repeat(4, 1fr); gap: 3mm; }
.cases span { background: #fff; border: 1.5px solid #1226AA; border-radius: 3mm; padding: 2.5mm 3mm; font-weight: 800; font-size: 9pt; }
.decoupe { display: flex; align-items: center; gap: 3mm; margin: 5mm 0 0; font-size: 7.5pt; color: #6b77c9; }
.decoupe::before, .decoupe::after { content: ''; flex: 1; border-top: 1px dashed #9aa3dd; }
.reactions { flex: 1; display: grid; grid-template-columns: 1fr 1fr; grid-template-rows: 1fr 1fr; border-left: 1px dashed #9aa3dd; border-top: 1px dashed #9aa3dd; margin-top: 3mm; }
.reaction { border-right: 1px dashed #9aa3dd; border-bottom: 1px dashed #9aa3dd; padding: 5mm 5mm 4mm; display: flex; flex-direction: column; }
.lettre { width: 9mm; height: 9mm; border-radius: 50%; border: 1.8px solid #1226AA; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 13pt; margin-bottom: 3mm; }
.texte { font-size: 10pt; font-weight: 600; line-height: 1.5; flex: 1; }
.pied { display: flex; justify-content: space-between; align-items: center; font-size: 6.5pt; color: #6b77c9; margin-top: 2mm; }
.pied img { width: 5mm; height: 5mm; }
</style>
</head>
<body>
<?php foreach ($manoeuvres as $no => $m): ?>
<section class="page">
  <div class="deco"></div>
  <div class="bandeau"><span>L’épreuve de conduite · Les manœuvres</span><img src="<?= $logo ?>" alt=""></div>
  <div class="tete"><span class="num"><?= (int) $no ?></span><span class="kicker">Manœuvre <?= (int) $no ?> sur <?= $total ?></span></div>
  <h1><?= $e($m['titre']) ?></h1>
  <div class="situation">
    <?php foreach ($m['situation'] as $p): ?><p><?= $fr($e($p)) ?></p><?php endforeach; ?>
  </div>
  <div class="consigne">
    <p><?= $fr($e('Classez les 4 réactions du manager, de la plus aidante (1) à la moins aidante (4). Quand l’équipe est d’accord, le représentant saisit le classement dans l’application.')) ?></p>
    <div class="cases"><span>1 →</span><span>2 →</span><span>3 →</span><span>4 →</span></div>
  </div>
  <div class="decoupe">✂ Découper les 4 réactions</div>
  <div class="reactions">
    <?php foreach ($m['reactions'] as $l => $txt): ?>
      <div class="reaction">
        <span class="lettre"><?= $e($l) ?></span>
        <div class="texte"><?= $fr($e($txt)) ?></div>
        <div class="pied"><span>Manœuvre <?= (int) $no ?> · <?= $e($m['titre']) ?></span><img src="<?= $logo ?>" alt=""></div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endforeach; ?>
</body>
</html>
