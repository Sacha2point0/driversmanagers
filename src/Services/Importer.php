<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Database as DB;
use App\Models\Participant;

/**
 * Import CSV des participants et des équipes.
 * Colonnes attendues (ligne d'en-tête obligatoire, ordre libre) : email ; prenom ; nom ; equipe
 * Séparateur ; ou , détecté automatiquement. Encodage UTF-8 (export Excel « CSV UTF-8 »).
 * Mise à jour par email : un participant déjà présent voit son nom et son équipe mis à jour.
 */
final class Importer
{
    public static function participants(string $path): array
    {
        $raw = (string) file_get_contents($path);
        $raw = preg_replace('/^\xEF\xBB\xBF/', '', $raw);            // BOM
        if (!mb_check_encoding($raw, 'UTF-8')) {
            $raw = mb_convert_encoding($raw, 'UTF-8', 'Windows-1252');  // CSV Excel « classique »
        }
        $lines = preg_split('/\r\n|\r|\n/', trim($raw));
        if (count($lines) < 2) {
            return ['created' => 0, 'updated' => 0, 'errors' => ['Le fichier est vide ou ne contient que l’en-tête.']];
        }
        $sep = substr_count($lines[0], ';') >= substr_count($lines[0], ',') ? ';' : ',';
        $header = array_map(fn ($h) => self::key($h), str_getcsv($lines[0], $sep));
        $idx = [];
        foreach (['email', 'prenom', 'nom', 'equipe'] as $col) {
            $pos = array_search($col, $header, true);
            if ($pos === false) {
                return ['created' => 0, 'updated' => 0, 'errors' => ['Colonne manquante : « ' . $col . ' ». En-tête attendu : email;prenom;nom;equipe']];
            }
            $idx[$col] = $pos;
        }

        $created = $updated = 0;
        $errors = [];
        $teams = [];
        $pdo = DB::pdo();
        $pdo->beginTransaction();
        try {
            foreach (array_slice($lines, 1) as $n => $line) {
                if (trim($line) === '') {
                    continue;
                }
                $cols = str_getcsv($line, $sep);
                $email = Participant::normalizeEmail($cols[$idx['email']] ?? '');
                $first = trim($cols[$idx['prenom']] ?? '');
                $last = trim($cols[$idx['nom']] ?? '');
                $teamName = trim($cols[$idx['equipe']] ?? '');
                if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $first === '' || $last === '') {
                    $errors[] = 'Ligne ' . ($n + 2) . ' ignorée (email, prénom ou nom invalide).';
                    continue;
                }
                if (self::upsert($email, $first, $last, $teamName, $teams) === 'created') {
                    $created++;
                } else {
                    $updated++;
                }
            }
            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            return ['created' => 0, 'updated' => 0, 'errors' => ['Import annulé : ' . $e->getMessage()]];
        }
        return ['created' => $created, 'updated' => $updated, 'errors' => $errors];
    }

    /**
     * Crée ou met à jour un participant (clé : email) et crée son équipe si besoin.
     * Utilisé par l'import CSV et par l'ajout manuel. L'email doit être normalisé et valide.
     * @param array<string,int> $teams cache nom d'équipe => id
     * @return 'created'|'updated'
     */
    public static function upsert(string $email, string $first, string $last, string $teamName, array &$teams = []): string
    {
        $teamId = null;
        if ($teamName !== '') {
            if (!isset($teams[$teamName])) {
                DB::run('INSERT IGNORE INTO teams (name) VALUES (?)', [$teamName]);
                $teams[$teamName] = (int) DB::value('SELECT id FROM teams WHERE name = ?', [$teamName]);
            }
            $teamId = $teams[$teamName];
        }
        $existing = DB::value('SELECT id FROM participants WHERE email = ?', [$email]);
        if ($existing) {
            DB::run('UPDATE participants SET first_name = ?, last_name = ?, team_id = ? WHERE id = ?', [$first, $last, $teamId, $existing]);
            return 'updated';
        }
        DB::run('INSERT INTO participants (email, first_name, last_name, team_id) VALUES (?, ?, ?, ?)', [$email, $first, $last, $teamId]);
        return 'created';
    }

    private static function key(string $h): string
    {
        $h = mb_strtolower(trim(preg_replace('/^\xEF\xBB\xBF/', '', $h)));
        $h = strtr($h, ['é' => 'e', 'è' => 'e', 'ê' => 'e', 'É' => 'e', 'ï' => 'i', '-' => '', ' ' => '', '_' => '']);
        return match ($h) {
            'mail', 'courriel', 'adresseemail', 'emailprofessionnel' => 'email',
            'firstname', 'prenoms' => 'prenom',
            'lastname', 'nomdefamille' => 'nom',
            'team', 'table', 'groupe' => 'equipe',
            default => $h,
        };
    }
}
