<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database as DB;

/**
 * Équipes. 'name' est le nom officiel (clé de l'import CSV, jamais modifié par les participants) ;
 * 'nickname' est le nom choisi par l'équipe dans son espace, affiché partout s'il existe.
 */
final class Team
{
    public const MIN = 2;
    public const MAX = 40;

    /** Nom affiché aux participants et en plénière. */
    public static function labelSql(string $a = 't'): string
    {
        return "COALESCE($a.nickname, $a.name)";
    }

    /** Nom affiché à l'animateur : « Nom choisi (Équipe 3) ». */
    public static function adminLabelSql(string $a = 't'): string
    {
        return "IF($a.nickname IS NULL, $a.name, CONCAT($a.nickname, ' (', $a.name, ')'))";
    }

    public static function normalize(string $s): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', $s));
    }

    /**
     * Enregistre le nom choisi (null ou '' pour revenir au nom officiel). Renvoie un message d'erreur, ou null si c'est fait.
     */
    public static function rename(int $teamId, ?string $nickname, ?int $by): ?string
    {
        $nickname = self::normalize((string) $nickname);
        if ($nickname === '') {
            DB::run('UPDATE teams SET nickname = NULL, nickname_by = NULL, nickname_at = NULL WHERE id = ?', [$teamId]);
            return null;
        }
        $len = mb_strlen($nickname);
        if ($len < self::MIN || $len > self::MAX) {
            return 'Le nom de l’équipe doit compter entre ' . self::MIN . ' et ' . self::MAX . ' caractères.';
        }
        if (DB::value('SELECT id FROM teams WHERE id <> ? AND (nickname = ? OR name = ?) LIMIT 1', [$teamId, $nickname, $nickname])) {
            return 'Ce nom est déjà pris par une autre équipe : choisissez-en un autre.';
        }
        DB::run('UPDATE teams SET nickname = ?, nickname_by = ?, nickname_at = NOW() WHERE id = ?', [$nickname, $by, $teamId]);
        return null;
    }
}
