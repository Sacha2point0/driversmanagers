<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Database as DB;

final class Questionnaire
{
    public const DRIVERS = ['sp', 'fp', 'sf', 'fe', 'dt'];

    /**
     * Valide les réponses et calcule les scores.
     * @return array{scores: array<string,int>, answers: array<string,int>, missing: int}
     */
    public static function compute(array $input): array
    {
        $q = content('questionnaire');
        $max = max(array_keys($q['echelle']));
        $min = min(array_keys($q['echelle']));
        $scores = array_fill_keys(self::DRIVERS, 0);
        $answers = [];
        $missing = 0;
        foreach ($q['questions'] as $question) {
            $raw = $input[$question['id']] ?? null;
            // L'échelle commence à 0 : une réponse absente ne doit pas compter comme un 0
            if (!is_scalar($raw) || !preg_match('/^\d+$/', (string) $raw)) {
                $missing++;
                continue;
            }
            $v = (int) $raw;
            if ($v < $min || $v > $max) {
                $missing++;
                continue;
            }
            $answers[$question['id']] = $v;
            $scores[$question['driver']] += $v;
        }
        return ['scores' => $scores, 'answers' => $answers, 'missing' => $missing];
    }

    /** Drivers dominants (plusieurs en cas d'égalité), dans l'ordre de référence. */
    public static function dominants(array $scores): array
    {
        $top = max($scores);
        return array_values(array_keys(array_filter($scores, fn ($s) => $s === $top)));
    }

    /** Niveau d'un score : faible (≤ seuil), marque, fort. */
    public static function level(int $score): string
    {
        $l = content('questionnaire')['lecture'];
        return $score <= $l['seuil'] ? 'faible' : ($score <= $l['tres_marque'] ? 'marque' : 'fort');
    }

    public const LEVEL_LABELS = ['faible' => 'peu déterminant', 'marque' => 'marqué', 'fort' => 'très marqué'];

    /**
     * Lecture d'ensemble du profil.
     * - principaux : drivers au score le plus élevé (plusieurs en cas d'égalité) ;
     * - secondaires : autres drivers marqués (au-dessus du seuil) ou proches du principal ;
     * - faible : le score le plus élevé ne dépasse pas le seuil (aucun driver déterminant) ;
     * - paires : combinaisons à commenter, entre les 3 drivers les plus marqués au-dessus du seuil.
     */
    public static function profile(array $scores): array
    {
        $l = content('questionnaire')['lecture'];
        $principaux = self::dominants($scores);
        $top = max($scores);
        $secondaires = [];
        foreach ($scores as $d => $v) {
            if (!in_array($d, $principaux, true) && ($v > $l['seuil'] || $top - $v <= $l['ecart_proche'])) {
                $secondaires[$d] = $v;
            }
        }
        arsort($secondaires);
        $secondaires = array_keys($secondaires);

        $actifs = array_values(array_filter(
            array_merge($principaux, $secondaires),
            fn ($d) => $scores[$d] > $l['seuil']
        ));
        $actifs = array_slice($actifs, 0, 3);
        $paires = [];
        for ($i = 0; $i < count($actifs); $i++) {
            for ($j = $i + 1; $j < count($actifs); $j++) {
                $paires[] = self::pairKey($actifs[$i], $actifs[$j]);
            }
        }

        return [
            'principaux'  => $principaux,
            'secondaires' => $secondaires,
            'egalite'     => count($principaux) > 1,
            'faible'      => $top <= $l['seuil'],
            'paires'      => $paires,
            'niveaux'     => array_map(fn ($v) => self::level((int) $v), $scores),
        ];
    }

    /** Clé d'une paire de drivers dans l'ordre de référence (ex. sp_dt). */
    public static function pairKey(string $a, string $b): string
    {
        $order = array_flip(self::DRIVERS);
        return $order[$a] < $order[$b] ? $a . '_' . $b : $b . '_' . $a;
    }

    /** Scores d'une ligne SQL contenant score_sp… score_dt, ou null si le questionnaire n'est pas fait. */
    public static function scoresFromRow(array $row): ?array
    {
        if (!isset($row['score_sp'])) {
            return null;
        }
        $out = [];
        foreach (self::DRIVERS as $d) {
            $out[$d] = (int) $row['score_' . $d];
        }
        return $out;
    }

    /** Libellé du ou des drivers principaux, égalités indiquées (« Sois parfait = Fais plaisir »). */
    public static function label(array $row): string
    {
        $scores = self::scoresFromRow($row);
        if (!$scores) {
            return '';
        }
        $drivers = content('drivers');
        return implode(' = ', array_map(fn ($d) => $drivers[$d]['nom'], self::dominants($scores)));
    }

    public static function save(int $participantId, array $result): void
    {
        $s = $result['scores'];
        $dominant = self::dominants($s)[0];
        DB::run(
            'INSERT INTO questionnaire_results (participant_id, answers, score_sp, score_fp, score_sf, score_fe, score_dt, dominant)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [$participantId, json_encode($result['answers']), $s['sp'], $s['fp'], $s['sf'], $s['fe'], $s['dt'], $dominant]
        );
    }

    public static function resultFor(int $participantId): ?array
    {
        $r = DB::one('SELECT * FROM questionnaire_results WHERE participant_id = ?', [$participantId]);
        if (!$r) {
            return null;
        }
        $scores = [];
        foreach (self::DRIVERS as $d) {
            $scores[$d] = (int) $r['score_' . $d];
        }
        return ['scores' => $scores, 'dominants' => self::dominants($scores), 'profil' => self::profile($scores), 'completed_at' => $r['completed_at']];
    }

    /** Score maximum possible par driver (pour les jauges). */
    public static function maxPerDriver(): int
    {
        $q = content('questionnaire');
        $count = array_count_values(array_column($q['questions'], 'driver'));
        return max($count) * max(array_keys($q['echelle']));
    }

    public static function minPerDriver(): int
    {
        $q = content('questionnaire');
        $count = array_count_values(array_column($q['questions'], 'driver'));
        return min($count) * min(array_keys($q['echelle']));
    }

    /** Répartition des drivers dominants (statistiques anonymes) ; les égalités sont comptées à part. */
    public static function distribution(): array
    {
        $out = array_fill_keys(self::DRIVERS, 0) + ['egalite' => 0];
        foreach (DB::all('SELECT score_sp, score_fp, score_sf, score_fe, score_dt FROM questionnaire_results') as $r) {
            $dom = self::dominants(self::scoresFromRow($r));
            $out[count($dom) > 1 ? 'egalite' : $dom[0]]++;
        }
        return $out;
    }
}
