<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\View;
use App\Models\Participant;

final class AuthController
{
    public function form(): void
    {
        if (Auth::participant()) {
            redirect('/espace');
        }
        View::render('auth/login', ['title' => 'Connexion']);
    }

    public function login(): void
    {
        csrf_check();
        $email = (string) ($_POST['email'] ?? '');
        $p = filter_var(trim($email), FILTER_VALIDATE_EMAIL) ? Participant::findByEmail($email) : null;
        if (!$p) {
            flash('error', 'Cette adresse email ne figure pas dans la liste des participants. Vérifiez-la, ou contactez l’équipe d’animation.');
            $_SESSION['last_email'] = $email;
            redirect('/');
        }
        Auth::login((int) $p['id']);
        Participant::touchLogin((int) $p['id']);
        redirect('/espace');
    }

    public function logout(): void
    {
        csrf_check();
        Auth::logout();
        redirect('/');
    }
}
