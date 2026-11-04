<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database as DB;
use App\Core\Gate;
use App\Core\View;
use App\Services\Manuel;

/** Le manuel du conducteur : l'équipe rédige, pour chaque driver, ce qui le motive et ce qui le crispe. */
final class ManuelController
{
    public function index(): void
    {
        $me = Auth::requireParticipant();
        Gate::requireOpen('manuel_open');
        $teamId = Gate::requireTeam($me);
        View::render('epreuves/manuel', [
            'title'      => content('manuel')['titre'],
            'cfg'        => content('manuel'),
            'drivers'    => content('drivers'),
            'entries'    => Manuel::entries($teamId),
            'validation' => Manuel::validation($teamId),
            'old'        => $_SESSION['manuel_old'] ?? [],
        ]);
        unset($_SESSION['manuel_old']);
    }

    /** Enregistre la fiche d'un driver (chaque fiche a son propre formulaire : deux membres peuvent saisir en parallèle). */
    public function save(string $driver): void
    {
        csrf_check();
        $me = Auth::requireParticipant();
        Gate::requireOpen('manuel_open');
        $teamId = Gate::requireTeam($me);
        $drivers = content('drivers');
        if (!isset($drivers[$driver])) {
            redirect('/manuel');
        }
        if (Manuel::validation($teamId)) {
            flash('info', 'Votre équipe a déjà validé son manuel : il ne peut plus être modifié.');
            redirect('/manuel');
        }
        $cfg = content('manuel');
        $values = [];
        foreach (array_keys($cfg['rubriques']) as $key) {
            $values[$key] = mb_substr(trim(str_replace("\r\n", "\n", (string) ($_POST[$key] ?? ''))), 0, $cfg['max_caracteres']);
        }
        DB::run(
            'INSERT INTO manual_entries (team_id, driver, motive, crispe, updated_by) VALUES (?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE motive = VALUES(motive), crispe = VALUES(crispe), updated_by = VALUES(updated_by)',
            [$teamId, $driver, $values['motive'], $values['crispe'], $me['id']]
        );
        $nom = $drivers[$driver]['nom'];
        if (Manuel::isComplete($values)) {
            flash('success', 'Profil « ' . $nom . ' » renseigné : il compte pour votre équipe.');
        } else {
            flash('info', 'Profil « ' . $nom . ' » enregistré, mais pas encore complet : écrivez au moins '
                . $cfg['min_caracteres'] . ' caractères dans chaque rubrique pour qu’il compte.');
        }
        redirect('/manuel#d-' . $driver);
    }

    /** Valide le manuel de l'équipe : plus aucune modification possible. */
    public function validate(): void
    {
        csrf_check();
        $me = Auth::requireParticipant();
        Gate::requireOpen('manuel_open');
        $teamId = Gate::requireTeam($me);
        DB::run('INSERT IGNORE INTO manual_validations (team_id, validated_by) VALUES (?, ?)', [$teamId, $me['id']]);
        flash('success', 'Manuel validé pour toute l’équipe : ' . Manuel::completeCount($teamId) . ' profil(s) renseigné(s).');
        redirect('/manuel');
    }
}
