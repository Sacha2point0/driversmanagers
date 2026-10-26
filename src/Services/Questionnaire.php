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
        return ['scores' => $scores, 'dominants' => self::dominants($scores), 'completed_at' => $r['completed_at']];
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

    /** Répartition des drivers dominants (statistiques anonymes). */
    public static function distribution(): array
    {
        $out = array_fill_keys(self::DRIVERS, 0);
        foreach (DB::all('SELECT dominant, COUNT(*) n FROM questionnaire_results GROUP BY dominant') as $r) {
            $out[$r['dominant']] = (int) $r['n'];
        }
        return $out;
    }
}
