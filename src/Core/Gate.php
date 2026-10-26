<?php
declare(strict_types=1);

namespace App\Core;

/** Contrôles d'accès communs aux épreuves. */
final class Gate
{
    public static function requireOpen(string $setting): void
    {
        if (!Settings::isOpen($setting)) {
            View::render('layout/error', [
                'title'   => 'Pas encore ouvert',
                'message' => 'Cette étape s’ouvrira pendant le séminaire, au signal de l’animateur.',
            ]);
            exit;
        }
    }

    public static function requireTeam(array $me): int
    {
        if (empty($me['team_id'])) {
            View::render('layout/error', [
                'title'   => 'Équipe non attribuée',
                'message' => 'Vous n’êtes rattaché à aucune équipe. Signalez-le à l’équipe d’animation.',
            ]);
            exit;
        }
        return (int) $me['team_id'];
    }

    public static function requireQuestionnaireDone(array $me): void
    {
        if (empty($me['dominant'])) {
            flash('info', 'Commencez par votre bilan de conduite : il ne prend qu’une dizaine de minutes.');
            redirect('/questionnaire');
        }
    }
}
