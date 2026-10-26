<?php
declare(strict_types=1);

namespace App\Core;

use App\Models\Participant;

final class Auth
{
    public static function participant(): ?array
    {
        static $cached = false;
        if ($cached !== false) {
            return $cached;
        }
        $id = $_SESSION['participant_id'] ?? null;
        $cached = $id ? Participant::find((int) $id) : null;
        if ($id && !$cached) {
            unset($_SESSION['participant_id']);
        }
        return $cached;
    }

    /** Exige un participant connecté, sinon renvoie vers la page de connexion. */
    public static function requireParticipant(): array
    {
        $p = self::participant();
        if (!$p) {
            redirect('/');
        }
        return $p;
    }

    public static function login(int $participantId): void
    {
        session_regenerate_id(true);
        $_SESSION['participant_id'] = $participantId;
    }

    public static function logout(): void
    {
        unset($_SESSION['participant_id']);
        session_regenerate_id(true);
    }

    public static function isAdmin(): bool
    {
        return !empty($_SESSION['is_admin']);
    }

    public static function requireAdmin(): void
    {
        if (!self::isAdmin()) {
            redirect('/admin/login');
        }
    }

    public static function adminLogin(string $password): bool
    {
        $hash = (string) Config::get('admin.password_hash', '');
        if ($hash !== '' && password_verify($password, $hash)) {
            session_regenerate_id(true);
            $_SESSION['is_admin'] = true;
            return true;
        }
        return false;
    }

    public static function adminLogout(): void
    {
        unset($_SESSION['is_admin']);
    }
}
