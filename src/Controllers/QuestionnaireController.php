<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Gate;
use App\Core\View;
use App\Services\Questionnaire;

final class QuestionnaireController
{
    public function form(): void
    {
        $me = Auth::requireParticipant();
        if ($me['dominant']) {
            redirect('/resultat');
        }
        Gate::requireOpen('questionnaire_open');
        View::render('questionnaire/form', [
            'title' => 'Mon bilan de conduite',
            'q'     => content('questionnaire'),
            'old'   => $_SESSION['q_old'] ?? [],
        ]);
        unset($_SESSION['q_old']);
    }

    public function submit(): void
    {
        csrf_check();
        $me = Auth::requireParticipant();
        if ($me['dominant']) {
            redirect('/resultat');
        }
        Gate::requireOpen('questionnaire_open');

        $result = Questionnaire::compute($_POST['r'] ?? []);
        if ($result['missing'] > 0) {
            $_SESSION['q_old'] = $result['answers'];
            flash('error', $result['missing'] === 1
                ? 'Il manque une réponse : complétez-la pour voir votre résultat.'
                : 'Il manque ' . $result['missing'] . ' réponses : complétez-les pour voir votre résultat.');
            redirect('/questionnaire');
        }
        try {
            Questionnaire::save((int) $me['id'], $result);
        } catch (\PDOException $e) {
            // Double envoi : le résultat existe déjà
        }
        redirect('/resultat');
    }

    public function result(): void
    {
        $me = Auth::requireParticipant();
        $res = Questionnaire::resultFor((int) $me['id']);
        if (!$res) {
            redirect('/questionnaire');
        }
        View::render('questionnaire/result', [
            'title'   => 'Mon bilan de conduite',
            'me'      => $me,
            'res'     => $res,
            'drivers' => content('drivers'),
            'max'     => Questionnaire::maxPerDriver(),
            'min'     => Questionnaire::minPerDriver(),
        ]);
    }
}
