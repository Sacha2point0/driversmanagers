<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database as DB;

final class Participant
{
    private const SELECT = 'SELECT p.*, t.name AS team_name, r.dominant, r.score_sp, r.score_fp, r.score_sf, r.score_fe, r.score_dt, r.completed_at AS questionnaire_done_at
        FROM participants p
        LEFT JOIN teams t ON t.id = p.team_id
        LEFT JOIN questionnaire_results r ON r.participant_id = p.id';

    public static function find(int $id): ?array
    {
        return DB::one(self::SELECT . ' WHERE p.id = ?', [$id]);
    }

    public static function findByEmail(string $email): ?array
    {
        return DB::one(self::SELECT . ' WHERE p.email = ?', [self::normalizeEmail($email)]);
    }

    public static function normalizeEmail(string $email): string
    {
        return mb_strtolower(trim($email));
    }

    public static function all(): array
    {
        return DB::all(self::SELECT . ' ORDER BY p.last_name, p.first_name');
    }

    public static function teammates(int $teamId): array
    {
        return DB::all('SELECT id, first_name, last_name FROM participants WHERE team_id = ? ORDER BY first_name, last_name', [$teamId]);
    }

    public static function touchLogin(int $id): void
    {
        DB::run('UPDATE participants SET last_login_at = NOW() WHERE id = ?', [$id]);
    }

    /** Liste pour la recherche de la séquence Rencontres : autres équipes uniquement, sans le driver. */
    public static function directoryFor(array $me): array
    {
        return DB::all(
            'SELECT p.id, p.first_name, p.last_name, t.name AS team_name
             FROM participants p LEFT JOIN teams t ON t.id = p.team_id
             WHERE p.id <> ? AND (p.team_id IS NULL OR p.team_id <> ?)
             ORDER BY p.last_name, p.first_name',
            [$me['id'], (int) $me['team_id']]
        );
    }

    /** Points de permis individuels (rencontres). */
    public static function points(int $id): int
    {
        $r = DB::one('SELECT COUNT(*) AS n, COALESCE(SUM(guess_ok), 0) AS ok FROM encounters WHERE participant_id = ?', [$id]);
        return (int) $r['n'] * (int) \App\Core\Config::get('points.rencontre', 1)
            + (int) $r['ok'] * (int) \App\Core\Config::get('points.devine', 1);
    }
}
