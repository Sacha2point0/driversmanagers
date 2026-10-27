<?php
/**
 * Copiez ce fichier en config/config.php et renseignez vos valeurs.
 * config/config.php est exclu de Git (.gitignore) : la clé d'API et les mots de passe n'y sont jamais versionnés.
 */
return [
    'app' => [
        'name'     => 'Le permis de manager',
        'client'   => 'Sogea-Satom',
        // URL de base, sans barre finale. Laisser '' si l'application est à la racine du (sous-)domaine.
        'base_url' => '',
        'debug'    => false,
        'timezone' => 'Europe/Paris',
    ],

    'db' => [
        'host'     => 'localhost',
        'port'     => 3306,
        'name'     => 'u000000000_permis',
        'user'     => 'u000000000_permis',
        'password' => 'CHANGER_MOI',
    ],

    // Espace animateur : générez le hash avec
    //   php -r "echo password_hash('votre-mot-de-passe', PASSWORD_DEFAULT), PHP_EOL;"
    'admin' => [
        'password_hash' => '',
    ],

    // API Claude (Anthropic) pour l'évaluation des cas
    'ai' => [
        'api_key'     => '',                    // sk-ant-...
        'model'       => 'claude-sonnet-5-5',   // vérifier le nom du modèle sur docs.claude.com
        'timeout'     => 60,                    // secondes
        'max_tokens'  => 4000,                  // minimum 4000 : le raisonnement du modèle compte dedans
        'effort'      => 'low',                 // effort de raisonnement : low, medium, high
    ],

    // Points de permis
    'points' => [
        'rencontre' => 1,   // par rencontre validée (au participant ET à son équipe)
        'devine'    => 1,   // bonus si le driver principal de la personne rencontrée est bien deviné
    ],

    // Rafraîchissement de l'écran plénière (secondes)
    'pleniere_refresh' => 10,
];
