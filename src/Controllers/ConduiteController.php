<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database as DB;
use App\Core\Gate;
use App\Core\View;
use App\Services\AiEvaluator;
use App\Services\Scoring;

/** L'épreuve de conduite : 6 situations rédigées en équipe, évaluées par l'IA, + bonus si le driver est identifié. */
final class ConduiteController
{
    public function index(): void
    {
        $me = Auth::requireParticipant();
        $teamId = Gate::requireTeam($me);
        Gate::requireOpen('cas_open');
        View::render('epreuves/epreuve', [
            'title'   => 'L’épreuve de conduite',
            'cas'     => content('cas')['cas'],
            'casDone' => $this->indexBy(DB::all('SELECT case_no AS no, ai_status, score_override, ' . Scoring::caseScoreSql() . ' AS score FROM case_submissions WHERE team_id = ?', [$teamId])),
        ]);
    }

    public function cas(string $no): void
    {
        $me = Auth::requireParticipant();
        Gate::requireOpen('cas_open');
        $teamId = Gate::requireTeam($me);
        $c = $this->casOr404((int) $no);
        View::render('epreuves/cas', [
            'title'      => $c['titre'],
            'no'         => (int) $no,
            'c'          => $c,
            'limits'     => content('cas'),
            'submission' => DB::one(
                'SELECT s.*, p.first_name, p.last_name, ' . Scoring::caseScoreSql() . ' AS final_score, ' . Scoring::redactionSql() . ' AS redaction
                 FROM case_submissions s LEFT JOIN participants p ON p.id = s.submitted_by WHERE s.team_id = ? AND s.case_no = ?',
                [$teamId, (int) $no]
            ),
        ]);
    }

    public function casSubmit(string $no): void
    {
        csrf_check();
        $me = Auth::requireParticipant();
        Gate::requireOpen('cas_open');
        $teamId = Gate::requireTeam($me);
        $n = (int) $no;
        $c = $this->casOr404($n);
        $limits = content('cas');

        $answer = trim(str_replace("\r\n", "\n", (string) ($_POST['answer'] ?? '')));
        $guess = (string) ($_POST['driver'] ?? '');
        if (!array_key_exists($guess, content('drivers'))) {
            $_SESSION['cas_old'] = $answer;
            flash('error', 'Indiquez le driver en jeu dans cette situation.');
            redirect('/cas/' . $n);
        }
        if (mb_strlen($answer) < $limits['longueur_min']) {
            $_SESSION['cas_old'] = $answer;
            $_SESSION['cas_old_driver'] = $guess;
            flash('error', 'Votre réponse est trop courte : rédigez le message du manager en quelques phrases.');
            redirect('/cas/' . $n);
        }
        $answer = mb_substr($answer, 0, $limits['longueur_max']);

        try {
            DB::run(
                'INSERT INTO case_submissions (team_id, case_no, answer, driver_guess, driver_ok, submitted_by) VALUES (?, ?, ?, ?, ?, ?)',
                [$teamId, $n, $answer, $guess, (int) ($guess === $c['driver']) * (int) $limits['bonus_driver'], $me['id']]
            );
        } catch (\PDOException $e) {
            flash('info', 'Votre équipe a déjà envoyé une réponse pour cette situation : c’est la première qui est retenue.');
            redirect('/cas/' . $n);
        }

        @set_time_limit(90);
        if (!AiEvaluator::evaluate($teamId, $n)) {
            flash('info', 'Votre réponse est bien enregistrée. L’examinateur la notera dans quelques minutes.');
        }
        redirect('/cas/' . $n);
    }

    private function casOr404(int $no): array
    {
        $all = content('cas')['cas'];
        if (!isset($all[$no])) {
            redirect('/epreuve');
        }
        return $all[$no];
    }

    private function indexBy(array $rows): array
    {
        $out = [];
        foreach ($rows as $r) {
            $out[(int) $r['no']] = $r;
        }
        return $out;
    }
}
