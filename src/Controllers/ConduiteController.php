<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database as DB;
use App\Core\Gate;
use App\Core\Settings;
use App\Core\View;
use App\Services\AiEvaluator;
use App\Services\Scoring;

/** L'épreuve de conduite : les manœuvres (classement) puis la conduite (cas évalués par IA). */
final class ConduiteController
{
    public function index(): void
    {
        $me = Auth::requireParticipant();
        $teamId = Gate::requireTeam($me);
        if (!Settings::isOpen('manoeuvres_open') && !Settings::isOpen('cas_open')) {
            Gate::requireOpen('manoeuvres_open');
        }
        View::render('epreuves/epreuve', [
            'title'      => 'L’épreuve de conduite',
            'manoeuvres' => content('manoeuvres'),
            'cas'        => content('cas')['cas'],
            'manDone'    => $this->indexBy(DB::all('SELECT manoeuvre_no AS no, score FROM manoeuvre_submissions WHERE team_id = ?', [$teamId])),
            'casDone'    => $this->indexBy(DB::all('SELECT case_no AS no, ai_status, ' . Scoring::caseScoreSql() . ' AS score FROM case_submissions WHERE team_id = ?', [$teamId])),
            'manOpen'    => Settings::isOpen('manoeuvres_open'),
            'casOpen'    => Settings::isOpen('cas_open'),
        ]);
    }

    public function manoeuvre(string $no): void
    {
        $me = Auth::requireParticipant();
        Gate::requireOpen('manoeuvres_open');
        $teamId = Gate::requireTeam($me);
        $m = $this->manoeuvreOr404((int) $no);
        View::render('epreuves/manoeuvre', [
            'title'      => $m['titre'],
            'no'         => (int) $no,
            'm'          => $m,
            'submission' => DB::one(
                'SELECT s.*, p.first_name, p.last_name FROM manoeuvre_submissions s LEFT JOIN participants p ON p.id = s.submitted_by WHERE s.team_id = ? AND s.manoeuvre_no = ?',
                [$teamId, (int) $no]
            ),
        ]);
    }

    public function manoeuvreSubmit(string $no): void
    {
        csrf_check();
        $me = Auth::requireParticipant();
        Gate::requireOpen('manoeuvres_open');
        $teamId = Gate::requireTeam($me);
        $n = (int) $no;
        $this->manoeuvreOr404($n);

        $ranking = '';
        for ($i = 1; $i <= 4; $i++) {
            $ranking .= strtoupper(substr((string) ($_POST['rang'][$i] ?? ''), 0, 1));
        }
        $letters = str_split($ranking);
        sort($letters);
        if (strlen($ranking) !== 4 || implode('', $letters) !== 'ABCD') {
            flash('error', 'Chaque fiche (A, B, C, D) doit apparaître une seule fois dans votre classement.');
            redirect('/manoeuvres/' . $n);
        }
        try {
            DB::run(
                'INSERT INTO manoeuvre_submissions (team_id, manoeuvre_no, ranking, score, submitted_by) VALUES (?, ?, ?, ?, ?)',
                [$teamId, $n, $ranking, Scoring::manoeuvre($n, $ranking), $me['id']]
            );
            flash('success', 'Classement enregistré pour toute l’équipe.');
        } catch (\PDOException $e) {
            flash('info', 'Votre équipe a déjà saisi cette manœuvre : c’est la première saisie qui est retenue.');
        }
        redirect('/manoeuvres/' . $n);
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
                'SELECT s.*, p.first_name, p.last_name, ' . Scoring::caseScoreSql() . ' AS final_score
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
        $this->casOr404($n);
        $limits = content('cas');

        $answer = trim(str_replace("\r\n", "\n", (string) ($_POST['answer'] ?? '')));
        if (mb_strlen($answer) < $limits['longueur_min']) {
            $_SESSION['cas_old'] = $answer;
            flash('error', 'Votre réponse est trop courte : rédigez le message du manager en quelques phrases.');
            redirect('/cas/' . $n);
        }
        $answer = mb_substr($answer, 0, $limits['longueur_max']);

        try {
            DB::run(
                'INSERT INTO case_submissions (team_id, case_no, answer, submitted_by) VALUES (?, ?, ?, ?)',
                [$teamId, $n, $answer, $me['id']]
            );
        } catch (\PDOException $e) {
            flash('info', 'Votre équipe a déjà envoyé une réponse pour ce cas : c’est la première qui est retenue.');
            redirect('/cas/' . $n);
        }

        @set_time_limit(90);
        if (!AiEvaluator::evaluate($teamId, $n)) {
            flash('info', 'Votre réponse est bien enregistrée. L’examinateur la notera dans quelques minutes.');
        }
        redirect('/cas/' . $n);
    }

    private function manoeuvreOr404(int $no): array
    {
        $all = content('manoeuvres');
        if (!isset($all[$no])) {
            redirect('/epreuve');
        }
        return $all[$no];
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
