<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database as DB;
use App\Core\Gate;
use App\Core\View;
use App\Services\Scoring;

/** L'examen du code : un membre de l'équipe saisit le placement des 20 cartes. */
final class CodeController
{
    public function form(): void
    {
        $me = Auth::requireParticipant();
        Gate::requireOpen('code_open');
        $teamId = Gate::requireTeam($me);
        View::render('epreuves/code', [
            'title'      => 'L’examen du code',
            'jeu'        => content('cartes'),
            'submission' => $this->submission($teamId),
        ]);
    }

    public function submit(): void
    {
        csrf_check();
        $me = Auth::requireParticipant();
        Gate::requireOpen('code_open');
        $teamId = Gate::requireTeam($me);

        if ($this->submission($teamId)) {
            flash('info', 'Votre équipe a déjà saisi l’examen du code.');
            redirect('/code');
        }

        $c = content('cartes');
        $placements = [];
        foreach (array_keys($c['cartes']) as $no) {
            $v = (string) ($_POST['c'][$no] ?? '');
            if (!array_key_exists($v, $c['options'])) {
                flash('error', 'Placez toutes les cartes avant de valider (carte ' . $no . ' manquante).');
                $_SESSION['code_old'] = $_POST['c'] ?? [];
                redirect('/code');
            }
            $placements[$no] = $v;
        }

        try {
            DB::run(
                'INSERT INTO code_submissions (team_id, placements, score, submitted_by) VALUES (?, ?, ?, ?)',
                [$teamId, json_encode($placements), Scoring::code($placements), $me['id']]
            );
            flash('success', 'C’est enregistré pour toute l’équipe.');
        } catch (\PDOException $e) {
            flash('info', 'Un autre membre de votre équipe vient de saisir l’examen : c’est sa saisie qui est retenue.');
        }
        redirect('/code');
    }

    private function submission(int $teamId): ?array
    {
        return DB::one(
            'SELECT c.*, p.first_name, p.last_name FROM code_submissions c LEFT JOIN participants p ON p.id = c.submitted_by WHERE c.team_id = ?',
            [$teamId]
        );
    }
}
