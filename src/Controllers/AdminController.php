<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database as DB;
use App\Core\Settings;
use App\Core\View;
use App\Models\Participant;
use App\Services\AiEvaluator;
use App\Services\Importer;
use App\Services\Questionnaire;
use App\Services\Scoring;

final class AdminController
{
    /* ---------- Connexion ---------- */

    public function loginForm(): void
    {
        if (Auth::isAdmin()) {
            redirect('/admin');
        }
        View::render('admin/login', ['title' => 'Animateur', 'admin' => true]);
    }

    public function login(): void
    {
        csrf_check();
        usleep(300000); // ralentit les essais en série
        if (Auth::adminLogin((string) ($_POST['password'] ?? ''))) {
            redirect('/admin');
        }
        flash('error', 'Mot de passe incorrect.');
        redirect('/admin/login');
    }

    public function logout(): void
    {
        csrf_check();
        Auth::adminLogout();
        redirect('/admin/login');
    }

    /* ---------- Tableau de bord ---------- */

    public function dashboard(): void
    {
        Auth::requireAdmin();
        $nb = (int) DB::value('SELECT COUNT(*) FROM participants');
        $done = (int) DB::value('SELECT COUNT(*) FROM questionnaire_results');
        $this->render('admin/dashboard', 'Tableau de bord', [
            'settings'     => Settings::all(),
            'participants' => $nb,
            'done'         => $done,
            'teams'        => (int) DB::value('SELECT COUNT(*) FROM teams'),
            'noTeam'       => (int) DB::value('SELECT COUNT(*) FROM participants WHERE team_id IS NULL'),
            'distribution' => Questionnaire::distribution(),
            'encounters'   => (int) DB::value('SELECT COUNT(*) FROM encounters'),
            'codeCount'    => (int) DB::value('SELECT COUNT(*) FROM code_submissions'),
            'manCount'     => (int) DB::value('SELECT COUNT(*) FROM manoeuvre_submissions'),
            'casCount'     => (int) DB::value('SELECT COUNT(*) FROM case_submissions'),
            'casErrors'    => (int) DB::value("SELECT COUNT(*) FROM case_submissions WHERE ai_status <> 'done' AND score_override IS NULL"),
            'leaderboard'  => Scoring::leaderboard(),
        ]);
    }

    public function sequences(): void
    {
        Auth::requireAdmin();
        csrf_check();
        foreach (array_keys(Settings::SEQUENCES) as $key) {
            Settings::set($key, isset($_POST['seq'][$key]) ? '1' : '0');
        }
        flash('success', 'Ouverture des séquences mise à jour.');
        redirect('/admin');
    }

    /* ---------- Participants ---------- */

    public function participants(): void
    {
        Auth::requireAdmin();
        $filter = (string) ($_GET['filtre'] ?? '');
        $list = Participant::all();
        if ($filter === 'a-relancer') {
            $list = array_values(array_filter($list, fn ($p) => !$p['dominant']));
        }
        $this->render('admin/participants', 'Participants', [
            'list'    => $list,
            'filter'  => $filter,
            'drivers' => content('drivers'),
            'teams'   => DB::all('SELECT name FROM teams ORDER BY name'),
            'old'     => $_SESSION['add_old'] ?? [],
        ]);
        unset($_SESSION['add_old']);
    }

    public function addParticipant(): void
    {
        Auth::requireAdmin();
        csrf_check();
        $email = Participant::normalizeEmail((string) ($_POST['email'] ?? ''));
        $first = trim((string) ($_POST['prenom'] ?? ''));
        $last = trim((string) ($_POST['nom'] ?? ''));
        $team = trim((string) ($_POST['equipe'] ?? ''));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $first === '' || $last === '') {
            $_SESSION['add_old'] = ['email' => $email, 'prenom' => $first, 'nom' => $last, 'equipe' => $team];
            flash('error', 'Renseignez un email valide, un prénom et un nom.');
            redirect('/admin/participants');
        }
        $r = Importer::upsert($email, $first, $last, $team);
        flash('success', $r === 'created'
            ? $first . ' ' . $last . ' a été ajouté(e).'
            : 'Cet email existait déjà : ' . $first . ' ' . $last . ' a été mis(e) à jour, sans toucher à son questionnaire.');
        redirect('/admin/participants');
    }

    public function import(): void
    {
        Auth::requireAdmin();
        csrf_check();
        $file = $_FILES['csv'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK || $file['size'] > 2_000_000) {
            flash('error', 'Fichier manquant ou trop volumineux.');
            redirect('/admin/participants');
        }
        $r = Importer::participants($file['tmp_name']);
        flash($r['created'] + $r['updated'] > 0 ? 'success' : 'error',
            $r['created'] . ' participant(s) créé(s), ' . $r['updated'] . ' mis à jour.' . ($r['errors'] ? ' ' . implode(' ', array_slice($r['errors'], 0, 5)) : ''));
        redirect('/admin/participants');
    }

    public function editParticipant(string $id): void
    {
        Auth::requireAdmin();
        $p = Participant::find((int) $id);
        if (!$p) {
            flash('error', 'Ce participant n’existe plus.');
            redirect('/admin/participants');
        }
        $old = $_SESSION['edit_old'] ?? [];
        unset($_SESSION['edit_old']);
        $this->render('admin/participant_edit', 'Modifier un participant', [
            'nav'      => 'admin/participants',
            'p'        => $p,
            'old'      => $old,
            'teams'    => DB::all('SELECT name FROM teams ORDER BY name'),
            'drivers'  => content('drivers'),
            'made'     => (int) DB::value('SELECT COUNT(*) FROM encounters WHERE participant_id = ?', [$p['id']]),
            'received' => (int) DB::value('SELECT COUNT(*) FROM encounters WHERE met_participant_id = ?', [$p['id']]),
        ]);
    }

    public function updateParticipant(string $id): void
    {
        Auth::requireAdmin();
        csrf_check();
        $p = Participant::find((int) $id);
        if (!$p) {
            flash('error', 'Ce participant n’existe plus.');
            redirect('/admin/participants');
        }
        $email = Participant::normalizeEmail((string) ($_POST['email'] ?? ''));
        $first = trim((string) ($_POST['prenom'] ?? ''));
        $last = trim((string) ($_POST['nom'] ?? ''));
        $team = trim((string) ($_POST['equipe'] ?? ''));
        $back = '/admin/participants/' . (int) $p['id'];
        $_SESSION['edit_old'] = ['email' => $email, 'prenom' => $first, 'nom' => $last, 'equipe' => $team];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $first === '' || $last === '') {
            flash('error', 'Renseignez un email valide, un prénom et un nom.');
            redirect($back);
        }
        if (DB::value('SELECT id FROM participants WHERE email = ? AND id <> ?', [$email, $p['id']])) {
            flash('error', 'Cet email est déjà utilisé par un autre participant.');
            redirect($back);
        }
        $teamId = null;
        if ($team !== '') {
            DB::run('INSERT IGNORE INTO teams (name) VALUES (?)', [$team]);
            $teamId = (int) DB::value('SELECT id FROM teams WHERE name = ?', [$team]);
        }
        DB::run('UPDATE participants SET email = ?, first_name = ?, last_name = ?, team_id = ? WHERE id = ?', [$email, $first, $last, $teamId, $p['id']]);
        unset($_SESSION['edit_old']);
        flash('success', $first . ' ' . $last . ' a été mis(e) à jour.');
        redirect('/admin/participants');
    }

    public function deleteParticipant(string $id): void
    {
        Auth::requireAdmin();
        csrf_check();
        $p = Participant::find((int) $id);
        if ($p) {
            // Le questionnaire et les rencontres (faites et reçues) sont supprimés en cascade
            DB::run('DELETE FROM participants WHERE id = ?', [$p['id']]);
            flash('success', $p['first_name'] . ' ' . $p['last_name'] . ' a été supprimé(e).');
        }
        redirect('/admin/participants');
    }

    public function resetQuestionnaire(string $id): void
    {
        Auth::requireAdmin();
        csrf_check();
        DB::run('DELETE FROM questionnaire_results WHERE participant_id = ?', [(int) $id]);
        flash('success', 'Questionnaire remis à zéro : le participant peut le refaire.');
        redirect('/admin/participants');
    }

    /* ---------- Séquences ---------- */

    public function code(): void
    {
        Auth::requireAdmin();
        $cfg = content('cartes');
        $subs = DB::all('SELECT c.*, t.name AS team_name FROM code_submissions c JOIN teams t ON t.id = c.team_id ORDER BY c.score DESC, t.name');
        // Statistiques par carte : zone choisie par les équipes
        $stats = [];
        foreach ($cfg['cartes'] as $no => $carte) {
            $stats[$no] = ['ok' => 0, 'zones' => array_fill_keys(array_keys($cfg['options']), 0)];
        }
        foreach ($subs as $s) {
            foreach (json_decode($s['placements'], true) as $no => $zone) {
                if (isset($stats[$no]['zones'][$zone])) {
                    $stats[$no]['zones'][$zone]++;
                    if (in_array($zone, $cfg['cartes'][$no]['accepte'], true)) {
                        $stats[$no]['ok']++;
                    }
                }
            }
        }
        $this->render('admin/code', 'L’examen du code', ['subs' => $subs, 'stats' => $stats, 'cfg' => $cfg]);
    }

    public function rencontres(): void
    {
        Auth::requireAdmin();
        $byTeam = DB::all(
            'SELECT t.name, COUNT(e.id) AS n, COUNT(DISTINCT e.participant_id) AS actifs,
               (SELECT COUNT(*) FROM participants p2 WHERE p2.team_id = t.id) AS membres
             FROM teams t LEFT JOIN participants p ON p.team_id = t.id LEFT JOIN encounters e ON e.participant_id = p.id
             GROUP BY t.id ORDER BY n DESC, t.name'
        );
        // Devinettes : pour chaque driver réel, part de bonnes réponses et confusion la plus fréquente
        $guesses = [];
        foreach (array_keys(content('drivers')) as $d) {
            $guesses[$d] = ['n' => 0, 'ok' => 0, 'wrong' => []];
        }
        $rows = DB::all(
            'SELECT r.dominant, e.guess, e.guess_ok FROM encounters e
             JOIN questionnaire_results r ON r.participant_id = e.met_participant_id
             WHERE e.guess IS NOT NULL AND e.guess_ok IS NOT NULL'
        );
        foreach ($rows as $r) {
            $g = &$guesses[$r['dominant']];
            $g['n']++;
            if ((int) $r['guess_ok'] === 1) {
                $g['ok']++;
            } else {
                $g['wrong'][$r['guess']] = ($g['wrong'][$r['guess']] ?? 0) + 1;
            }
            unset($g);
        }
        $this->render('admin/rencontres', 'La conduite accompagnée', [
            'guesses' => $guesses,
            'byTeam'  => $byTeam,
            'total'   => (int) DB::value('SELECT COUNT(*) FROM encounters'),
            'drivers' => content('drivers'),
            'questions' => content('rencontres')['questions'],
        ]);
    }

    public function manoeuvres(): void
    {
        Auth::requireAdmin();
        $cfg = content('manoeuvres');
        $subs = DB::all('SELECT s.*, t.name AS team_name FROM manoeuvre_submissions s JOIN teams t ON t.id = s.team_id ORDER BY s.manoeuvre_no, s.score DESC, t.name');
        $first = [];
        foreach ($cfg as $no => $m) {
            $first[$no] = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0];
        }
        foreach ($subs as $s) {
            $first[(int) $s['manoeuvre_no']][$s['ranking'][0]]++;
        }
        $this->render('admin/manoeuvres', 'Les manœuvres', ['cfg' => $cfg, 'subs' => $subs, 'first' => $first]);
    }

    public function cas(): void
    {
        Auth::requireAdmin();
        $no = (int) ($_GET['cas'] ?? 0);
        $sql = 'SELECT s.*, t.name AS team_name, ' . Scoring::caseScoreSql() . ' AS final_score FROM case_submissions s JOIN teams t ON t.id = s.team_id';
        $subs = $no ? DB::all($sql . ' WHERE s.case_no = ? ORDER BY final_score DESC', [$no]) : DB::all($sql . ' ORDER BY s.case_no, final_score DESC');
        $this->render('admin/cas', 'La conduite (cas)', ['subs' => $subs, 'cfg' => content('cas')['cas'], 'filter' => $no]);
    }

    public function casOverride(string $team, string $no): void
    {
        Auth::requireAdmin();
        csrf_check();
        $v = trim((string) ($_POST['note'] ?? ''));
        $note = $v === '' ? null : max(0, min(10, (int) $v));
        DB::run('UPDATE case_submissions SET score_override = ? WHERE team_id = ? AND case_no = ?', [$note, (int) $team, (int) $no]);
        flash('success', $note === null ? 'Note de l’animateur retirée : la note de l’IA s’applique.' : 'Note corrigée.');
        redirect('/admin/cas?cas=' . (int) ($_POST['filtre'] ?? 0));
    }

    public function casRetry(string $team, string $no): void
    {
        Auth::requireAdmin();
        csrf_check();
        @set_time_limit(90);
        $ok = AiEvaluator::evaluate((int) $team, (int) $no);
        flash($ok ? 'success' : 'error', $ok ? 'Réponse évaluée par l’IA.' : 'L’appel à l’IA a encore échoué : notez la réponse à la main.');
        redirect('/admin/cas?cas=' . (int) ($_POST['filtre'] ?? 0));
    }

    public function casProject(string $team, string $no): void
    {
        Auth::requireAdmin();
        csrf_check();
        DB::run('UPDATE case_submissions SET projected = 1 - projected WHERE team_id = ? AND case_no = ?', [(int) $team, (int) $no]);
        redirect('/admin/cas?cas=' . (int) ($_POST['filtre'] ?? 0));
    }

    /* ---------- Projections (plein écran) ---------- */

    public function projectionCas(): void
    {
        Auth::requireAdmin();
        $rows = DB::all(
            'SELECT s.*, t.name AS team_name, ' . Scoring::caseScoreSql() . ' AS final_score
             FROM case_submissions s JOIN teams t ON t.id = s.team_id WHERE s.projected = 1 ORDER BY s.case_no, final_score DESC'
        );
        View::render('pleniere/cas', ['title' => 'Projection', 'rows' => $rows, 'cfg' => content('cas')['cas']], 'pleniere/layout');
    }

    /** Réponses de la séquence Rencontres regroupées par driver de la personne rencontrée (anonyme). */
    public function projectionRencontres(): void
    {
        Auth::requireAdmin();
        $drivers = content('drivers');
        $questions = content('rencontres')['questions'];
        $d = (string) ($_GET['driver'] ?? 'sp');
        $q = (string) ($_GET['q'] ?? 'voyants');
        if (!isset($drivers[$d]) || !isset($questions[$q])) {
            redirect('/admin/rencontres');
        }
        $rows = DB::all(
            'SELECT e.answers FROM encounters e JOIN questionnaire_results r ON r.participant_id = e.met_participant_id
             WHERE r.dominant = ? ORDER BY RAND() LIMIT 9',
            [$d]
        );
        $quotes = [];
        foreach ($rows as $r) {
            $a = json_decode($r['answers'], true);
            if (!empty($a[$q])) {
                $quotes[] = $a[$q];
            }
        }
        View::render('pleniere/rencontres', [
            'title' => 'Projection', 'driver' => $d, 'drivers' => $drivers, 'q' => $q, 'questions' => $questions,
            'quotes' => $quotes,
            'count' => (int) DB::value('SELECT COUNT(*) FROM encounters e JOIN questionnaire_results r ON r.participant_id = e.met_participant_id WHERE r.dominant = ?', [$d]),
        ], 'pleniere/layout');
    }

    /* ---------- Exports et données ---------- */

    public function export(string $type): void
    {
        Auth::requireAdmin();
        $drivers = content('drivers');
        switch ($type) {
            case 'participants.csv':
                $head = ['email', 'prenom', 'nom', 'equipe', 'driver_dominant', 'questionnaire_le', 'derniere_connexion'];
                $rows = array_map(fn ($p) => [$p['email'], $p['first_name'], $p['last_name'], $p['team_name'],
                    $p['dominant'] ? Questionnaire::label($p) : '', $p['questionnaire_done_at'], $p['last_login_at']], Participant::all());
                break;
            case 'scores-questionnaire.csv':
                $head = ['email', 'prenom', 'nom', 'equipe', 'sois_parfait', 'fais_plaisir', 'sois_fort', 'fais_des_efforts', 'depeche_toi', 'driver_principal', 'lecture'];
                $rows = array_map(function ($r) {
                    $prof = Questionnaire::profile(Questionnaire::scoresFromRow($r));
                    $lecture = $prof['faible'] ? 'aucun driver déterminant' : ($prof['egalite'] ? 'égalité' : ($prof['secondaires'] ? 'profil combiné' : 'driver net'));
                    return [$r['email'], $r['first_name'], $r['last_name'], $r['name'], $r['score_sp'], $r['score_fp'], $r['score_sf'], $r['score_fe'], $r['score_dt'],
                        Questionnaire::label($r), $lecture];
                }, DB::all(
                    'SELECT p.email, p.first_name, p.last_name, t.name, r.score_sp, r.score_fp, r.score_sf, r.score_fe, r.score_dt
                     FROM questionnaire_results r JOIN participants p ON p.id = r.participant_id LEFT JOIN teams t ON t.id = p.team_id ORDER BY p.last_name'
                ));
                break;
            case 'classement.csv':
                $head = ['rang', 'equipe', 'examen_du_code', 'rencontres', 'manoeuvres', 'cas', 'total'];
                $rows = [];
                foreach (Scoring::leaderboard() as $i => $r) {
                    $rows[] = [$i + 1, $r['name'], $r['code'], $r['rencontres'], $r['manoeuvres'], $r['cas'], $r['total']];
                }
                break;
            case 'cas.csv':
                $head = ['cas', 'equipe', 'reponse', 'note_ia', 'note_animateur', 'note_finale', 'ce_qui_fonctionne', 'pour_aller_plus_loin', 'statut_ia'];
                $rows = array_map(fn ($r) => array_values($r), DB::all(
                    'SELECT s.case_no, t.name, s.answer, s.ai_score, s.score_override, ' . Scoring::caseScoreSql() . ', s.feedback_ok, s.feedback_more, s.ai_status
                     FROM case_submissions s JOIN teams t ON t.id = s.team_id ORDER BY s.case_no, t.name'
                ));
                break;
            case 'rencontres-anonymes.csv':
                // Pas de noms : uniquement le driver de la personne rencontrée et ce qu'elle a dit
                $head = ['driver_personne_rencontree', 'driver_devine', 'bien_devine', 'voyants', 'conduite', 'feu_vert', 'stop'];
                $rows = [];
                foreach (DB::all('SELECT r.dominant, e.answers, e.guess, e.guess_ok FROM encounters e LEFT JOIN questionnaire_results r ON r.participant_id = e.met_participant_id') as $r) {
                    $a = json_decode($r['answers'], true);
                    $rows[] = [$r['dominant'] ? $drivers[$r['dominant']]['nom'] : 'inconnu', $r['guess'] ? $drivers[$r['guess']]['nom'] : '',
                        $r['guess_ok'] === null ? '' : ((int) $r['guess_ok'] ? 'oui' : 'non'),
                        $a['voyants'] ?? '', $a['conduite'] ?? '', $a['feu_vert'] ?? '', $a['stop'] ?? ''];
                }
                break;
            default:
                http_response_code(404);
                exit('Export inconnu');
        }
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="permis-' . $type . '"');
        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF"); // BOM pour Excel
        fputcsv($out, $head, ';', '"', '');
        foreach ($rows as $row) {
            fputcsv($out, $row, ';', '"', '');
        }
        fclose($out);
        exit;
    }

    public function dataPage(): void
    {
        Auth::requireAdmin();
        $this->render('admin/donnees', 'Données', []);
    }

    public function purge(): void
    {
        Auth::requireAdmin();
        csrf_check();
        if (trim((string) ($_POST['confirm'] ?? '')) !== 'SUPPRIMER') {
            flash('error', 'Tapez SUPPRIMER en majuscules pour confirmer.');
            redirect('/admin/donnees');
        }
        $scope = (string) ($_POST['scope'] ?? 'epreuves');
        $pdo = DB::pdo();
        $pdo->exec('DELETE FROM case_submissions');
        $pdo->exec('DELETE FROM manoeuvre_submissions');
        $pdo->exec('DELETE FROM code_submissions');
        $pdo->exec('DELETE FROM encounters');
        if ($scope === 'tout') {
            $pdo->exec('DELETE FROM questionnaire_results');
            $pdo->exec('DELETE FROM participants');
            $pdo->exec('DELETE FROM teams');
        }
        flash('success', $scope === 'tout' ? 'Toutes les données ont été supprimées.' : 'Les données des épreuves ont été supprimées (questionnaires et participants conservés).');
        redirect('/admin/donnees');
    }

    private function render(string $view, string $title, array $data): void
    {
        View::render($view, $data + ['title' => $title, 'admin' => true, 'wide' => true, 'nav' => $view]);
    }
}
