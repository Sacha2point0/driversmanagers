<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Diagnostic global du bilan de conduite : assemble les blocs de content/diagnostic.php
 * selon la forme du profil (net, combiné, égalité, chargé, faible).
 *
 * Drivers significatifs : le ou les principaux, puis les drivers au-dessus du seuil, 3 au plus
 * (seulement le ou les principaux quand aucun driver ne dépasse le seuil). Ce sont eux qui
 * alimentent aussi les atouts, le voyant d'alerte, l'équipe, le feu vert et les conseils.
 */
final class Diagnostic
{
    public static function build(array $scores): array
    {
        $c = content('diagnostic');
        $drv = content('drivers');
        $seuil = (int) content('questionnaire')['lecture']['seuil'];

        $order = array_flip(Questionnaire::DRIVERS);
        $codes = array_keys($scores);
        usort($codes, fn ($a, $b) => [$scores[$b], $order[$a]] <=> [$scores[$a], $order[$b]]);

        $principaux = Questionnaire::dominants($scores);
        $top = max($scores);
        $faible = $top <= $seuil;
        $egalite = count($principaux) > 1;
        $actifs = array_values(array_filter($codes, fn ($d) => $scores[$d] > $seuil));

        $significatifs = $principaux;
        if (!$faible) {
            foreach ($actifs as $d) {
                if (count($significatifs) < 3 && !in_array($d, $significatifs, true)) {
                    $significatifs[] = $d;
                }
            }
        }
        $secondaires = array_values(array_diff($significatifs, $principaux));

        if ($faible) {
            $forme = 'faible';
        } elseif ($egalite) {
            $forme = 'egalite';
        } elseif (count($actifs) >= 4) {
            $forme = 'charge';
        } elseif (count($actifs) >= 2) {
            $forme = 'combine';
        } else {
            $forme = 'net';
        }

        $nom = fn (string $d) => $drv[$d]['nom'];
        $avecScore = fn (string $d) => $drv[$d]['nom'] . ' (' . $scores[$d] . '/40)';
        $vars = [
            '{P}'     => self::liste(array_map($nom, $principaux)),
            '{sP}'    => (string) $top,
            '{S}'     => self::liste(array_map($avecScore, $secondaires)),
            '{n}'     => (string) count($actifs),
            '{seuil}' => (string) $seuil,
            '{nb}'    => [2 => 'deux', 3 => 'trois', 4 => 'quatre', 5 => 'cinq'][count($principaux)] ?? '',
        ];
        $t = fn (string $tpl, array $extra = []) => strtr($tpl, $extra + $vars);
        $accord = fn (string $base, int $n) => $c[$base . ($n > 1 ? '_2' : '_1')];

        $paragraphes = [];

        // 1. Ouverture
        switch ($forme) {
            case 'faible':
                $paragraphes[] = $t($c['ouverture'][$egalite ? 'faible_egalite' : 'faible']);
                break;
            case 'egalite':
                $p = $t($c['ouverture'][count($principaux) > 2 ? 'egalite_n' : 'egalite']);
                if ($secondaires) {
                    $p .= ' ' . $t($c['ouverture']['egalite_plus' . (count($secondaires) > 1 ? '_2' : '_1')]);
                }
                $paragraphes[] = $p;
                break;
            case 'combine':
                $paragraphes[] = $t($c['ouverture']['combine' . (count($secondaires) > 1 ? '_2' : '_1')]);
                break;
            default:
                $paragraphes[] = $t($c['ouverture'][$forme]);
        }

        // 2. Le ou les drivers principaux
        foreach ($principaux as $d) {
            $paragraphes[] = $c['drivers'][$d][$faible ? 'tendance' : 'principal'];
        }

        // 3. Les autres drivers significatifs, et ceux qui dépassent encore le seuil (profil chargé)
        if ($secondaires) {
            $p = implode(' ', array_map(fn ($d) => $c['drivers'][$d]['second'], $secondaires));
            $autres = array_values(array_diff($actifs, $significatifs));
            if ($autres) {
                $p .= ' ' . $t($accord('autres_actifs', count($autres)), ['{S}' => self::liste(array_map($avecScore, $autres))]);
            }
            $paragraphes[] = $p;
        }

        // 4. Interactions entre drivers significatifs (2 au plus)
        if (!$faible && count($significatifs) >= 2) {
            $paires = [[$significatifs[0], $significatifs[1]]];
            if (count($significatifs) >= 3) {
                $paires[] = [$significatifs[0], $significatifs[2]];
            }
            foreach ($paires as [$a, $b]) {
                $paragraphes[] = $c['interactions'][Questionnaire::pairKey($a, $b)];
            }
        }

        // 5. Ce que disent les scores bas
        $bas = array_values(array_filter(array_reverse($codes), fn ($d) => $scores[$d] <= $seuil && !in_array($d, $significatifs, true)));
        if ($bas) {
            $paragraphes[] = $c[$faible ? 'ressources_intro_faible' : 'ressources_intro'] . ' '
                . implode(' ', array_map(fn ($d) => $c['drivers'][$d]['ressource'], $bas));
        }

        // 6. Conclusion
        $paragraphes[] = $c['conclusion'];

        return ['forme' => $forme, 'significatifs' => $significatifs, 'principaux' => $principaux, 'paragraphes' => $paragraphes];
    }

    /**
     * Éléments d'une liste de chaque driver significatif : tout pour un driver seul,
     * sinon 2 pour un principal et 1 pour un secondaire.
     */
    public static function pick(array $significatifs, array $principaux, string $field): array
    {
        $drv = content('drivers');
        $out = [];
        foreach ($significatifs as $d) {
            $n = count($significatifs) === 1 ? PHP_INT_MAX : (in_array($d, $principaux, true) ? 2 : 1);
            foreach (array_slice($drv[$d][$field], 0, $n) as $item) {
                $out[] = $item;
            }
        }
        return $out;
    }

    /** « A », « A et B », « A, B et C ». */
    public static function liste(array $items): string
    {
        if (count($items) <= 1) {
            return (string) ($items[0] ?? '');
        }
        $last = array_pop($items);
        return implode(', ', $items) . ' et ' . $last;
    }
}
