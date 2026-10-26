<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Config;
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

    /** Manœuvres : 1 point par fiche au bon rang + 1 point si la première est juste. */
    public static function manoeuvre(int $no, string $ranking): int
    {
        $ref = content('manoeuvres')[$no]['ordre'];
        $score = 0;
        for ($i = 0; $i < 4; $i++) {
            if (($ranking[$i] ?? '') === $ref[$i]) {
                $score++;
            }
        }
        if (($ranking[0] ?? '') === $ref[0]) {
            $score++;
        }
        return $score;
    }

    /** Score final d'un cas : la note corrigée par l'animateur prime sur celle de l'IA. */
    public static function caseScoreSql(): string
    {
        return 'COALESCE(score_override, ai_score, 0)';
    }

    /** Classement des équipes, toutes séquences confondues. */
    public static function leaderboard(): array
    {
        $pts = (int) Config::get('points.rencontre', 1);
        $rows = DB::all(
            'SELECT t.id, t.name,
                COALESCE((SELECT score FROM code_submissions c WHERE c.team_id = t.id), 0) AS code,
                (SELECT COUNT(*) * ? + COALESCE(SUM(e.guess_ok), 0) * ? FROM encounters e JOIN participants p ON p.id = e.participant_id WHERE p.team_id = t.id) AS rencontres,
                COALESCE((SELECT SUM(score) FROM manoeuvre_submissions m WHERE m.team_id = t.id), 0) AS manoeuvres,
                COALESCE((SELECT SUM(' . self::caseScoreSql() . ') FROM case_submissions s WHERE s.team_id = t.id), 0) AS cas
             FROM teams t',
            [$pts, (int) Config::get('points.devine', 1)]
        );
        foreach ($rows as &$r) {
            foreach (['code', 'rencontres', 'manoeuvres', 'cas'] as $k) {
                $r[$k] = (int) $r[$k];
            }
            $r['total'] = $r['code'] + $r['rencontres'] + $r['manoeuvres'] + $r['cas'];
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
