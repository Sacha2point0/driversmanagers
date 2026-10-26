<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database as DB;
use App\Core\Settings;
use App\Core\View;
use App\Models\Participant;
use App\Services\Scoring;

final class EspaceController
{
    public function index(): void
    {
        $me = Auth::requireParticipant();
        $teamId = (int) ($me['team_id'] ?? 0);

        $code = $teamId ? DB::one(
            'SELECT c.score, c.submitted_at, p.first_name FROM code_submissions c LEFT JOIN participants p ON p.id = c.submitted_by WHERE c.team_id = ?',
            [$teamId]
        ) : null;
        $manDone = $teamId ? (int) DB::value('SELECT COUNT(*) FROM manoeuvre_submissions WHERE team_id = ?', [$teamId]) : 0;
        $casDone = $teamId ? (int) DB::value('SELECT COUNT(*) FROM case_submissions WHERE team_id = ?', [$teamId]) : 0;

        $anyEpreuveOpen = Settings::isOpen('code_open') || Settings::isOpen('rencontres_open')
            || Settings::isOpen('manoeuvres_open') || Settings::isOpen('cas_open');

        View::render('espace/index', [
            'title'      => 'Mon espace',
            'me'         => $me,
            'teammates'  => $teamId ? Participant::teammates($teamId) : [],
            'code'       => $code,
            'rencontres' => (int) DB::value('SELECT COUNT(*) FROM encounters WHERE participant_id = ?', [$me['id']]),
            'points'     => Participant::points((int) $me['id']),
            'manDone'    => $manDone,
            'casDone'    => $casDone,
            'team'       => ($teamId && $anyEpreuveOpen) ? Scoring::teamTotal($teamId) : null,
            'open'       => array_map(fn ($k) => Settings::isOpen($k), array_combine(array_keys(Settings::SEQUENCES), array_keys(Settings::SEQUENCES))),
        ]);
    }
}
