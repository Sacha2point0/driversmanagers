<?php
/**
 * L'épreuve de conduite : les manœuvres (fiches imprimées).
 * 'ordre' = classement de référence, de la plus aidante à la moins aidante.
 * Barème : 1 point par fiche au bon rang + 1 point si la première est juste (5 max).
 */
return [
    1 => [
        'titre'  => 'Les commandes express',
        'driver' => 'dt',
        'resume' => 'Adja passe les commandes avant la vérification des métrés.',
        'reactions' => [
            'A' => '« Désormais, c’est moi qui valide toutes les commandes avant envoi. »',
            'B' => '« Je sais que tu tiens à nos délais… 10 minutes de vérification à deux, chrono en main. »',
            'C' => '« Arrête de te précipiter, tu fais des erreurs. »',
            'D' => '« Prends tout ton temps, la qualité d’abord. »',
        ],
        'ordre' => 'BDAC',
    ],
    2 => [
        'titre'  => 'Le planning de demain',
        'driver' => 'sp',
        'resume' => 'Fatou refuse d’envoyer un planning qu’elle juge « pas encore assez fiable ».',
        'reactions' => [
            'A' => '« Envoie-le tel quel, ce n’est pas grave. »',
            'B' => '« Montre-moi les deux incohérences : on les regarde ensemble maintenant. »',
            'C' => '« Je veux un planning sans aucune erreur demain à 9h. »',
            'D' => '« Ton planning est fiable à 90 %… réserves… tu me l’envoies à 18h. »',
        ],
        'ordre' => 'DBAC',
    ],
    3 => [
        'titre'  => 'Les sous-traitants',
        'driver' => 'fp',
        'resume' => 'Paul n’ose pas refuser les demandes de ses sous-traitants.',
        'reactions' => [
            'A' => '« Tu es trop gentil avec eux, il faut que tu sois plus ferme. »',
            'B' => '« La relation compte… tu peux dire non sans la casser… je viens au premier point. »',
            'C' => '« À partir de maintenant, c’est moi qui gère les sous-traitants. »',
            'D' => '« Rappelle-leur les pénalités prévues au contrat. »',
        ],
        'ordre' => 'BDCA',
    ],
];
