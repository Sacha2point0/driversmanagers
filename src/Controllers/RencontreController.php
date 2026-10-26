<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Config;
use App\Core\Database as DB;
use App\Core\Gate;
use App\Core\View;
use App\Models\Participant;
use App\Services\Questionnaire;

/** La conduite accompagnée : chaque participant enregistre ses rencontres (validation sur la confiance). */
final class RencontreController
{
    public function index(): void
    {
        $me = Auth::requireParticipant();
        Gate::requireOpen('rencontres_open');
        Gate::requireTeam($me);

        $mine = DB::all(
            'SELECT e.created_at, e.guess_ok, p.id, p.first_name, p.last_name, t.name AS team_name
             FROM encounters e JOIN participants p ON p.id = e.met_participant_id LEFT JOIN teams t ON t.id = p.team_id
             WHERE e.participant_id = ? ORDER BY e.created_at DESC',
            [$me['id']]
        );
        View::render('epreuves/rencontres', [
            'title'     => 'La conduite accompagnée',
            'mine'      => $mine,
            'metIds'    => array_map('intval', array_column($mine, 'id')),
            'directory' => Participant::directoryFor($me),
            'points'    => Participant::points((int) $me['id']),
            'teamCount' => (int) DB::value(
                'SELECT COUNT(*) FROM encounters e JOIN participants p ON p.id = e.participant_id WHERE p.team_id = ?',
                [$me['team_id']]
            ),
        ]);
    }

    public function form(string $id): void
    {
        $me = Auth::requireParticipant();
        Gate::requireOpen('rencontres_open');
        Gate::requireTeam($me);
        $other = $this->checkOther($me, (int) $id);
        View::render('epreuves/rencontre_form', [
            'title' => 'Ma rencontre',
            'other' => $other,
            'q'       => content('rencontres'),
            'drivers' => content('drivers'),
            'old'     => $_SESSION['enc_old'] ?? [],
            'oldGuess'=> $_SESSION['enc_guess'] ?? '',
        ]);
        unset($_SESSION['enc_old'], $_SESSION['enc_guess']);
    }

    public function submit(string $id): void
    {
        csrf_check();
        $me = Auth::requireParticipant();
        Gate::requireOpen('rencontres_open');
        Gate::requireTeam($me);
        $other = $this->checkOther($me, (int) $id);

        $cfg = content('rencontres');
        $answers = [];
        foreach (array_keys($cfg['questions']) as $key) {
            $v = trim((string) ($_POST['a'][$key] ?? ''));
            if (mb_strlen($v) < $cfg['min_caracteres']) {
                $_SESSION['enc_old'] = $_POST['a'] ?? [];
                $_SESSION['enc_guess'] = (string) ($_POST['guess'] ?? '');
                flash('error', 'Complétez les 4 questions en quelques mots (au moins ' . $cfg['min_caracteres'] . ' caractères chacune).');
                redirect('/rencontres/avec/' . $other['id']);
            }
            $answers[$key] = mb_substr($v, 0, 600);
        }

        // Le driver principal deviné (obligatoire)
        $drivers = content('drivers');
        $guess = (string) ($_POST['guess'] ?? '');
        if (!isset($drivers[$guess])) {
            $_SESSION['enc_old'] = $_POST['a'] ?? [];
            flash('error', 'Dites-nous quel est, selon vous, le driver principal de ' . $other['first_name'] . '.');
            redirect('/rencontres/avec/' . $other['id']);
        }
        // Bien deviné si le driver choisi fait partie de ses drivers dominants (égalités comprises).
        // NULL si la personne n'a pas encore fait son bilan : pas de bonus possible.
        $result = Questionnaire::resultFor((int) $other['id']);
        $guessOk = $result ? (int) in_array($guess, $result['dominants'], true) : null;
        try {
            DB::run(
                'INSERT INTO encounters (participant_id, met_participant_id, answers, guess, guess_ok) VALUES (?, ?, ?, ?, ?)',
                [$me['id'], $other['id'], json_encode($answers, JSON_UNESCAPED_UNICODE), $guess, $guessOk]
            );
            $pts = (int) Config::get('points.rencontre', 1);
            $bonus = (int) Config::get('points.devine', 1);
            $msg = 'Rencontre avec ' . $other['first_name'] . ' enregistrée : +' . $pts . ' point' . ($pts > 1 ? 's' : '') . ' pour vous et votre équipe. ';
            if ($guessOk === 1) {
                $msg .= '🎯 Bien vu, c’est bien son driver principal : +' . $bonus . ' point' . ($bonus > 1 ? 's' : '') . ' de bonus !';
            } elseif ($guessOk === 0) {
                $msg .= 'Pour le driver, ce n’est pas son principal : demandez-lui, ou gardez la question pour le débrief.';
            } else {
                $msg .= $other['first_name'] . ' n’a pas encore fait son bilan de conduite : pas de bonus possible.';
            }
            flash($guessOk === 0 ? 'info' : 'success', $msg);
        } catch (\PDOException $e) {
            flash('info', 'Vous avez déjà enregistré cette rencontre.');
        }
        redirect('/rencontres');
    }

    /** La personne rencontrée doit exister, être d'une autre équipe, et ne pas avoir déjà été rencontrée. */
    private function checkOther(array $me, int $otherId): array
    {
        $other = Participant::find($otherId);
        if (!$other || (int) $other['id'] === (int) $me['id']) {
            flash('error', 'Participant introuvable.');
            redirect('/rencontres');
        }
        if ((int) $other['team_id'] === (int) $me['team_id']) {
            flash('error', $other['first_name'] . ' est dans votre équipe : allez à la rencontre d’une autre équipe.');
            redirect('/rencontres');
        }
        $already = DB::value('SELECT 1 FROM encounters WHERE participant_id = ? AND met_participant_id = ?', [$me['id'], $otherId]);
        if ($already) {
            flash('info', 'Vous avez déjà enregistré votre rencontre avec ' . $other['first_name'] . '.');
            redirect('/rencontres');
        }
        return $other;
    }
}
