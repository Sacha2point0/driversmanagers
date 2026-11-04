<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Config;
use App\Core\Database as DB;

/**
 * Le manuel du conducteur : pour chaque driver, l'équipe renseigne « ce qui le motive » et « ce qui le crispe ».
 * Un profil est renseigné quand ses deux rubriques atteignent le minimum de caractères ; il rapporte points.manuel.
 */
final class Manuel
{
    /** Fiches de l'équipe, indexées par driver (avec le prénom de la dernière personne qui a enregistré). */
    public static function entries(int $teamId): array
    {
        $out = [];
        foreach (DB::all(
            'SELECT m.*, p.first_name, p.last_name FROM manual_entries m LEFT JOIN participants p ON p.id = m.updated_by WHERE m.team_id = ?',
            [$teamId]
        ) as $r) {
            $out[$r['driver']] = $r;
        }
        return $out;
    }

    public static function isComplete(?array $entry): bool
    {
        $min = (int) content('manuel')['min_caracteres'];
        return $entry !== null
            && mb_strlen(trim((string) $entry['motive'])) >= $min
            && mb_strlen(trim((string) $entry['crispe'])) >= $min;
    }

    public static function completeCount(int $teamId): int
    {
        return count(array_filter(self::entries($teamId), fn ($e) => self::isComplete($e)));
    }

    public static function validation(int $teamId): ?array
    {
        return DB::one(
            'SELECT v.validated_at, p.first_name, p.last_name FROM manual_validations v LEFT JOIN participants p ON p.id = v.validated_by WHERE v.team_id = ?',
            [$teamId]
        );
    }

    /** Nombre total de profils renseignés, toutes équipes confondues. */
    public static function totalComplete(): int
    {
        $min = (int) content('manuel')['min_caracteres'];
        return (int) DB::value(
            'SELECT COUNT(*) FROM manual_entries WHERE CHAR_LENGTH(TRIM(motive)) >= ? AND CHAR_LENGTH(TRIM(crispe)) >= ?',
            [$min, $min]
        );
    }

    /** Points du manuel pour le classement (sous-requête SQL sur l'équipe t). */
    public static function scoreSql(): string
    {
        $min = (int) content('manuel')['min_caracteres'];
        $pts = (int) Config::get('points.manuel', 1);
        return "(SELECT COUNT(*) * $pts FROM manual_entries m WHERE m.team_id = t.id
                 AND CHAR_LENGTH(TRIM(m.motive)) >= $min AND CHAR_LENGTH(TRIM(m.crispe)) >= $min)";
    }
}
