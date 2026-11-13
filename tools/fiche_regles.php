<?php
/**
 * Génère la fiche « Les règles du jeu » remise aux participants (HTML A4, 1 page par épreuve) :
 * L'examen du code, Le manuel du conducteur, L'épreuve de conduite.
 * Les chiffres (cartes, points, longueurs) viennent de content/ pour rester alignés avec l'appli.
 *
 *   php tools/fiche_regles.php > fiche-regles.html
 *   puis ouvrir dans Chrome et imprimer en PDF (A4, sans en-têtes ni pieds de page, graphiques d'arrière-plan activés).
 */
declare(strict_types=1);

$root = dirname(__DIR__);
require $root . '/src/Services/Signs.php';

use App\Services\Signs;

$cartes = require $root . '/content/cartes.php';
$manuel = require $root . '/content/manuel.php';
$cas = require $root . '/content/cas.php';
$drivers = require $root . '/content/drivers.php';

$nbCartes = count($cartes['cartes']);
$nbZones = count($cartes['options']);
$nbProfils = count($drivers);
$nbCas = count($cas['cas']);
$parCas = (int) $cas['max_redaction'] + (int) $cas['bonus_driver'];
$total = $nbCartes + $nbProfils + $nbCas * $parCas;

$e = fn (string $s) => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
// Espaces insécables avant la ponctuation haute et dans les guillemets français
$fr = fn (string $s) => preg_replace(['/ ([:;!?»%])/u', '/« /u'], ["\u{202F}$1", "«\u{202F}"], $s);
$t = fn (string $s) => $fr($e($s));
$font = fn (int $w) => 'url(data:font/woff2;base64,' . base64_encode((string) file_get_contents($root . "/public/assets/fonts/nunito-latin-$w-normal.woff2")) . ') format("woff2")';
$logo = 'data:image/svg+xml;base64,' . base64_encode((string) file_get_contents($root . '/public/assets/img/cachet.svg'));

$epreuves = [
    [
        'titre'  => 'L’examen du code',
        'but'    => 'Reconnaître les drivers derrière des comportements observés au travail.',
        'materiel' => "Un jeu de $nbCartes cartes « situation » et $nbZones zones : les $nbProfils drivers et « Pas forcément un driver ».",
        'etapes' => [
            'Lisez les cartes une à une, à voix haute.',
            'Cherchez les indices : les mots employés, ce qui se répète, ce que le comportement coûte à la personne ou à l’équipe.',
            'Mettez-vous d’accord sur une seule zone par carte. Si le comportement s’explique par le métier ou le contexte, choisissez « Pas forcément un driver ».',
            "Quand les $nbCartes cartes sont placées, un représentant saisit le placement dans l’application et valide.",
            'L’équipe découvre ensuite le corrigé, carte par carte.',
        ],
        'bareme' => [
            ['1 point', 'par carte bien placée'],
            ["$nbCartes points", 'au maximum'],
        ],
        'bareme_note' => 'Certaines cartes acceptent plusieurs réponses : le contexte compte.',
        'astuces' => [
            'Une seule saisie par équipe : la première validée compte et ne se modifie plus. Vérifiez avant de valider.',
            'Un comportement isolé ne suffit pas à faire un driver : c’est la répétition et l’excès qui le révèlent.',
            'Pas d’accord ? Prenez le temps d’en débattre : c’est là que l’on apprend le plus.',
        ],
    ],
    [
        'titre'  => 'Le manuel du conducteur',
        'but'    => 'Écrire, pour chaque driver, le mode d’emploi pour bien travailler avec lui.',
        'materiel' => 'La fiche d’équipe : une ligne par driver, deux rubriques, « ' . $manuel['rubriques']['motive']['titre'] . ' » et « ' . $manuel['rubriques']['crispe']['titre'] . ' ».',
        'etapes' => [
            'Pour chaque driver, rédigez ensemble ce qui le motive : ce qu’il apprécie, ce qui le met en mouvement.',
            'Puis ce qui le crispe : les mots, les attitudes, les situations qui le braquent.',
            'Quelques mots ou une phrase suffisent. Pensez à ce que vous voyez et entendez vraiment sur le terrain.',
            'Saisissez chaque profil dans l’application. Plusieurs membres peuvent saisir en même temps : chaque fiche s’enregistre séparément et reste modifiable.',
            "Quand les $nbProfils profils sont prêts, validez le manuel : il est alors verrouillé.",
        ],
        'bareme' => [
            ['1 point', 'par profil renseigné'],
            ["$nbProfils points", 'au maximum'],
        ],
        'bareme_note' => 'Un profil est renseigné quand ses deux rubriques comptent au moins ' . (int) $manuel['min_caracteres'] . ' caractères.',
        'astuces' => [
            'Il n’y a pas de bonne ou de mauvaise réponse, et pas de corrigé : c’est votre manuel.',
            'Des réponses peuvent être projetées en plénière, sans le nom de l’équipe.',
            'Ne validez qu’à la fin : après validation, plus aucune fiche ne se modifie.',
        ],
    ],
    [
        'titre'  => 'L’épreuve de conduite',
        'but'    => 'Trouver les mots justes face à un collaborateur pris dans son driver.',
        'materiel' => "$nbCas fiches « situation » : le contexte, la situation et votre mission. Le même texte figure dans l’application.",
        'etapes' => [
            'Lisez la situation ensemble.',
            'Identifiez le driver en jeu : cochez-en un seul.',
            'Rédigez ce que vous dites à la personne, en 5 à 8 lignes, avec vos propres mots, comme si vous lui parliez.',
            'Le représentant saisit le driver et la réponse dans l’application, puis l’envoie.',
            'Vous recevez votre note, un retour en deux phrases, une réponse possible et le point à retenir.',
        ],
        'bareme' => [
            ['+ 1 point', 'si le driver est le bon'],
            [(int) $cas['max_redaction'] . ' points', 'pour la réponse'],
            ["$parCas points", "par situation, " . ($nbCas * $parCas) . ' au total'],
        ],
        'bareme_note' => 'La réponse est notée par l’IA, 1 point par critère ci-dessous. L’animateur peut revoir une note.',
        'criteres' => [
            ['Accueil', 'vous montrez que vous comprenez ce que vit la personne, avant de lui demander quelque chose.'],
            ['Valorisation', 'vous reconnaissez la qualité qui se cache derrière son driver, sans l’encourager à en faire encore plus.'],
            ['Permission', 'vous lui donnez le feu vert qui répond à son driver.'],
            ['Cadre concret', 'une action précise, qui la fait, et une échéance ou une limite.'],
            ['Sans piège', 'ni jugement, ni menace, ni culpabilisation ; vous ne banalisez pas le problème et ne laissez pas la personne seule.'],
        ],
        'astuces' => [
            'Une réponse par situation : une fois envoyée, elle ne se modifie plus.',
            'Parlez à la personne, pas d’elle. L’orthographe et le style ne sont pas notés.',
        ],
    ],
];
?><!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Satom · Le permis de manager · Les règles du jeu</title>
<style>
<?php foreach ([400, 600, 700, 800, 900] as $w): ?>
@font-face { font-family: Nunito; font-weight: <?= $w ?>; src: <?= $font($w) ?>; }
<?php endforeach; ?>
@page { size: A4; margin: 0; }
* { box-sizing: border-box; }
body { margin: 0; font-family: Nunito, sans-serif; color: #1226AA; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
.page { width: 210mm; height: 297mm; padding: 12mm 14mm 10mm; position: relative; overflow: hidden; page-break-after: always; display: flex; flex-direction: column; }
.page:last-child { page-break-after: auto; }
.deco { position: absolute; top: -38mm; right: -38mm; width: 80mm; height: 80mm; border-radius: 50%; background: #FFE6E1; z-index: 0; }
.bandeau { position: relative; z-index: 1; display: flex; align-items: center; justify-content: space-between; background: #3BD4AE; border-radius: 10mm; padding: 3mm 3mm 3mm 7mm; font-weight: 800; font-size: 11pt; }
.bandeau img { width: 9mm; height: 9mm; background: #fff; border-radius: 50%; }
.kicker { font-weight: 800; font-size: 8pt; letter-spacing: .18em; text-transform: uppercase; margin-top: 8mm; }
h1 { font-size: 26pt; font-weight: 900; margin: 1.5mm 0 3mm; line-height: 1.1; }
h2 { font-weight: 800; font-size: 8pt; letter-spacing: .18em; text-transform: uppercase; margin: 6mm 0 2.5mm; }
.but { font-size: 13pt; font-weight: 800; line-height: 1.4; margin: 0; }
.materiel { font-size: 10pt; line-height: 1.5; margin: 2mm 0 0; }
.etapes { list-style: none; margin: 0; padding: 0; counter-reset: n; }
.etapes li { counter-increment: n; position: relative; padding-left: 10mm; margin-bottom: 2.6mm; font-size: 10.5pt; line-height: 1.45; min-height: 7mm; }
.etapes li::before { content: counter(n); position: absolute; left: 0; top: -.3mm; width: 6.5mm; height: 6.5mm; border-radius: 50%; background: #FF8672; color: #fff; font-weight: 900; font-size: 9.5pt; display: flex; align-items: center; justify-content: center; }
.bareme { background: #EEF0FB; border-radius: 5mm; padding: 4mm 5mm; }
.bareme .points { display: flex; gap: 4mm; }
.bareme .points div { flex: 1; background: #fff; border-radius: 3mm; padding: 2.5mm 3mm; font-size: 9pt; font-weight: 600; line-height: 1.3; }
.bareme .points strong { display: block; font-size: 15pt; font-weight: 900; }
.bareme p { margin: 3mm 0 0; font-size: 9pt; font-weight: 600; line-height: 1.4; }
.criteres { list-style: none; margin: 3mm 0 0; padding: 0; display: grid; gap: 1.6mm; }
.criteres li { font-size: 9.5pt; line-height: 1.4; background: #fff; border-radius: 2.5mm; padding: 1.8mm 3mm; }
.astuces { list-style: none; margin: 0; padding: 0; }
.astuces li { position: relative; padding-left: 6mm; margin-bottom: 2mm; font-size: 10pt; line-height: 1.45; }
.astuces li::before { content: ""; position: absolute; left: 0; top: 1.6mm; width: 2.6mm; height: 2.6mm; border-radius: 50%; background: #3BD4AE; }
.drivers { display: flex; justify-content: space-between; margin-top: 4mm; }
.drivers div { display: flex; align-items: center; gap: 1.5mm; font-weight: 800; font-size: 8.5pt; }
.espace { flex: 1; }
.pied { border-top: 1.5px solid #1226AA; padding-top: 3mm; font-size: 8.5pt; text-align: center; font-weight: 700; line-height: 1.4; }
</style>
</head>
<body>
<?php foreach ($epreuves as $i => $ep): ?>
<section class="page">
  <div class="deco"></div>
  <div class="bandeau"><span>Le permis de manager · Les règles du jeu</span><img src="<?= $logo ?>" alt=""></div>
  <div class="kicker">Épreuve <?= $i + 1 ?> sur <?= count($epreuves) ?> · En équipe</div>
  <h1><?= $e($ep['titre']) ?></h1>
  <p class="but"><?= $t($ep['but']) ?></p>
  <p class="materiel"><strong>Le matériel :</strong> <?= $t($ep['materiel']) ?></p>

  <h2>Comment ça se passe</h2>
  <ol class="etapes">
    <?php foreach ($ep['etapes'] as $s): ?><li><?= $t($s) ?></li><?php endforeach; ?>
  </ol>

  <h2>Les points</h2>
  <div class="bareme">
    <div class="points">
      <?php foreach ($ep['bareme'] as [$fort, $texte]): ?><div><strong><?= $t($fort) ?></strong><?= $t($texte) ?></div><?php endforeach; ?>
    </div>
    <p><?= $t($ep['bareme_note']) ?></p>
    <?php if (!empty($ep['criteres'])): ?>
      <ul class="criteres">
        <?php foreach ($ep['criteres'] as [$nom, $def]): ?><li><strong><?= $e($nom) ?></strong> : <?= $t($def) ?></li><?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>

  <h2>Bon à savoir</h2>
  <ul class="astuces">
    <?php foreach ($ep['astuces'] as $s): ?><li><?= $t($s) ?></li><?php endforeach; ?>
  </ul>

  <div class="drivers">
    <?php foreach ($drivers as $code => $d): ?><div><?= Signs::svg($code, 22) ?><?= $e($d['nom']) ?></div><?php endforeach; ?>
  </div>
  <div class="espace"></div>
  <p class="pied"><?= $t("Trois épreuves, $total points de permis au total. Le classement des équipes s’affiche en direct sur l’écran de la plénière.") ?></p>
</section>
<?php endforeach; ?>
</body>
</html>
