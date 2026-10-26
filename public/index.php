<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

use App\Controllers as C;
use App\Core\Router;

$r = new Router();

// Connexion participant
$r->get('/', [C\AuthController::class, 'form']);
$r->post('/login', [C\AuthController::class, 'login']);
$r->post('/logout', [C\AuthController::class, 'logout']);

// Espace participant
$r->get('/espace', [C\EspaceController::class, 'index']);

// Le bilan de conduite (questionnaire)
$r->get('/questionnaire', [C\QuestionnaireController::class, 'form']);
$r->post('/questionnaire', [C\QuestionnaireController::class, 'submit']);
$r->get('/resultat', [C\QuestionnaireController::class, 'result']);

// L'examen du code (jeu de cartes, saisie par équipe)
$r->get('/code', [C\CodeController::class, 'form']);
$r->post('/code', [C\CodeController::class, 'submit']);

// La conduite accompagnée (rencontres)
$r->get('/rencontres', [C\RencontreController::class, 'index']);
$r->get('/rencontres/avec/{id}', [C\RencontreController::class, 'form']);
$r->post('/rencontres/avec/{id}', [C\RencontreController::class, 'submit']);

// L'épreuve de conduite
$r->get('/epreuve', [C\ConduiteController::class, 'index']);
$r->get('/manoeuvres/{no}', [C\ConduiteController::class, 'manoeuvre']);
$r->post('/manoeuvres/{no}', [C\ConduiteController::class, 'manoeuvreSubmit']);
$r->get('/cas/{no}', [C\ConduiteController::class, 'cas']);
$r->post('/cas/{no}', [C\ConduiteController::class, 'casSubmit']);

// Écran plénière (projection publique, lecture seule)
$r->get('/pleniere', [C\PleniereController::class, 'index']);
$r->get('/api/pleniere', [C\PleniereController::class, 'data']);

// Espace animateur
$r->get('/admin/login', [C\AdminController::class, 'loginForm']);
$r->post('/admin/login', [C\AdminController::class, 'login']);
$r->post('/admin/logout', [C\AdminController::class, 'logout']);
$r->get('/admin', [C\AdminController::class, 'dashboard']);
$r->post('/admin/sequences', [C\AdminController::class, 'sequences']);
$r->get('/admin/participants', [C\AdminController::class, 'participants']);
$r->post('/admin/import', [C\AdminController::class, 'import']);
$r->post('/admin/participants/ajouter', [C\AdminController::class, 'addParticipant']);
$r->get('/admin/participants/{id}', [C\AdminController::class, 'editParticipant']);
$r->post('/admin/participants/{id}', [C\AdminController::class, 'updateParticipant']);
$r->post('/admin/participants/{id}/supprimer', [C\AdminController::class, 'deleteParticipant']);
$r->post('/admin/participants/{id}/reset', [C\AdminController::class, 'resetQuestionnaire']);
$r->get('/admin/code', [C\AdminController::class, 'code']);
$r->get('/admin/rencontres', [C\AdminController::class, 'rencontres']);
$r->get('/admin/manoeuvres', [C\AdminController::class, 'manoeuvres']);
$r->get('/admin/cas', [C\AdminController::class, 'cas']);
$r->post('/admin/cas/{team}/{no}/note', [C\AdminController::class, 'casOverride']);
$r->post('/admin/cas/{team}/{no}/relancer', [C\AdminController::class, 'casRetry']);
$r->post('/admin/cas/{team}/{no}/projeter', [C\AdminController::class, 'casProject']);
$r->get('/admin/projection/cas', [C\AdminController::class, 'projectionCas']);
$r->get('/admin/projection/rencontres', [C\AdminController::class, 'projectionRencontres']);
$r->get('/admin/export/{type}', [C\AdminController::class, 'export']);
$r->get('/admin/donnees', [C\AdminController::class, 'dataPage']);
$r->post('/admin/purge', [C\AdminController::class, 'purge']);

try {
    $r->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
} catch (\Throwable $e) {
    error_log((string) $e);
    http_response_code(500);
    if (\App\Core\Config::get('app.debug')) {
        echo '<pre>' . e((string) $e) . '</pre>';
    } else {
        echo 'Une erreur est survenue. Réessayez dans un instant.';
    }
}
