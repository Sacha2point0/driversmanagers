<?php
declare(strict_types=1);

namespace App\Core;

/** Paramètres de l'événement stockés en base (ouverture des séquences). */
final class Settings
{
    public const SEQUENCES = [
        'questionnaire_open' => 'Le bilan de conduite (questionnaire)',
        'code_open'          => 'L’examen du code (jeu de cartes)',
        'rencontres_open'    => 'La conduite accompagnée (rencontres)',
        'manoeuvres_open'    => 'L’épreuve de conduite : les manœuvres',
        'cas_open'           => 'L’épreuve de conduite : la conduite (cas)',
    ];

    private static ?array $cache = null;

    public static function all(): array
    {
        if (self::$cache === null) {
            self::$cache = [];
            foreach (Database::all('SELECT name, value FROM settings') as $r) {
                self::$cache[$r['name']] = $r['value'];
            }
        }
        return self::$cache;
    }

    public static function get(string $name, string $default = ''): string
    {
        return self::all()[$name] ?? $default;
    }

    public static function isOpen(string $name): bool
    {
        return self::get($name) === '1';
    }

    public static function set(string $name, string $value): void
    {
        Database::run(
            'INSERT INTO settings (name, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)',
            [$name, $value]
        );
        self::$cache = null;
    }
}
