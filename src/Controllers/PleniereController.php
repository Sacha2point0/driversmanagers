<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\Database as DB;
use App\Core\View;
use App\Services\Scoring;

/** Écran projeté en plénière : classement des équipes, actualisé automatiquement. */
final class PleniereController
{
    public function index(): void
    {
        View::render('pleniere/index', [
            'title'   => 'Classement',
            'refresh' => (int) Config::get('pleniere_refresh', 10),
        ], 'pleniere/layout');
    }

    public function data(): void
    {
        json_response([
            'teams'      => array_map(fn ($r) => ['name' => $r['name'], 'total' => $r['total']], Scoring::leaderboard()),
            'encounters' => (int) DB::value('SELECT COUNT(*) FROM encounters'),
        ]);
    }
}
