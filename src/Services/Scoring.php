<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Database as DB;

final class Scoring
{
    /** Examen du code : 1 point par carte placée dans une zone acceptée. */
    public static function code(array $placements): int
    {
        $score = 0;
        foreach (content('cartes')['cartes'] as $no => $carte) {
            if (isset($placements[$no]) && in_array($placements[$no], $carte['accepte'], true)) {
                $score++;
            }
        }
        return $score;
    }

    /** Note de la rédaction d'une situation (sur 5) : la note de l'animateur prime sur celle de l'IA. */
    public static function redactionSql(): string
    {
        return 'COALESCE(score_override, ai_score, 0)';
    }

    /** Score final d'une situation (sur 6) : rédaction + 1 point si le driver est identifié. */
    public static function caseScoreSql(): string
    {
        return '(COALESCE(score_override, ai_score, 0) + driver_ok)';
    }

    /** Classement des équipes, toutes séquences confondues. */
    public static function leaderboard(): array
    {
        $rows = DB::all(
            'SELECT t.id, t.name,
                COALESCE((SELECT score FROM code_submissions c WHERE c.team_id = t.id), 0) AS code,
                ' . Manuel::scoreSql() . ' AS manuel,
                COALESCE((SELECT SUM(' . self::caseScoreSql() . ') FROM case_submissions s WHERE s.team_id = t.id), 0) AS conduite
             FROM teams t'
        );
        foreach ($rows as &$r) {
            foreach (['code', 'manuel', 'conduite'] as $k) {
                $r[$k] = (int) $r[$k];
            }
            $r['total'] = $r['code'] + $r['manuel'] + $r['conduite'];
        }
        unset($r);
        usort($rows, fn ($a, $b) => [$b['total'], $a['name']] <=> [$a['total'], $b['name']]);
        return $rows;
    }

    public static function teamTotal(int $teamId): ?array
    {
        foreach (self::leaderboard() as $rank => $row) {
            if ((int) $row['id'] === $teamId) {
                return $row + ['rank' => $rank + 1];
            }
        }
        return null;
    }
}
