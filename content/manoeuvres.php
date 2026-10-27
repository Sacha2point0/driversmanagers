<?php
/**
 * L'épreuve de conduite : les manœuvres (fiches imprimées).
 * 'ordre' = classement de référence, de la plus aidante à la moins aidante.
 * Barème : 1 point par fiche au bon rang + 1 point si la première est juste (5 max).
 * 'pourquoi' et 'a_retenir' : corrigé affiché à l'équipe après sa saisie
 * (repris du référentiel animateur, sauf 'a_retenir', à valider par Sacha).
 */
return [
    1 => [
        'titre'  => 'Les commandes express',
        'driver' => 'dt',
        'resume' => 'Adja passe les commandes avant la vérification des métrés.',
        'reactions' => [
            'A' => '« Désormais, c’est moi qui valide toutes les commandes avant envoi. »',
            'B' => '« Je sais que tu tiens à nos délais, et c’est précieux. On met en place 10 minutes de vérification à deux avant chaque commande, chrono en main : tu gagneras du temps sur les corrections. »',
            'C' => '« Arrête de te précipiter, tu fais des erreurs. »',
            'D' => '« Prends tout ton temps, la qualité d’abord. »',
        ],
        'ordre' => 'BDAC',
        'pourquoi' => [
            'B' => 'Son besoin (tenir les délais) est reconnu, et elle reçoit un cadre court, assumé, qui lui fait gagner du temps.',
            'D' => 'C’est la bonne permission, mais sans cadre ni échéance ; pour un Dépêche-toi, ça reste flou.',
            'A' => 'Le problème est réglé, mais on lui retire son autonomie et le manager devient un goulot d’étranglement.',
            'C' => 'Un reproche qui renforce la pression et ne propose rien.',
        ],
        'a_retenir' => 'Le Dépêche-toi a besoin qu’on reconnaisse son souci des délais, et d’un cadre court qui lui fait gagner du temps plutôt que d’un simple « ralentis ».',
    ],
    2 => [
        'titre'  => 'Le planning de demain',
        'driver' => 'sp',
        'resume' => 'Fatou refuse d’envoyer un planning qu’elle juge « pas encore assez fiable ».',
        'reactions' => [
            'A' => '« Envoie-le tel quel, ce n’est pas grave. »',
            'B' => '« Montre-moi les deux incohérences : on les regarde ensemble maintenant et on décide si elles bloquent. »',
            'C' => '« Je veux un planning sans aucune erreur demain à 9h. »',
            'D' => '« Ton planning est fiable à 90 %, c’est le niveau attendu pour demain. On signale les deux points incertains en réserve et tu me l’envoies à 18h. »',
        ],
        'ordre' => 'DBAC',
        'pourquoi' => [
            'D' => 'Elle reçoit un critère explicite de « suffisamment bien », une façon de traiter le doute (les réserves) et une échéance.',
            'B' => 'Le manager l’accompagne et l’aide à trancher, mais la séance reste centrée sur le détail et lui prend du temps.',
            'A' => 'Le délai est tenu, mais ce qui compte pour elle est balayé ; elle risque de le vivre comme un manque de considération.',
            'C' => 'Renforce directement le driver.',
        ],
        'a_retenir' => 'Le Sois parfait a besoin d’un critère explicite de « suffisamment bien », d’une façon de traiter le doute et d’une échéance.',
    ],
    3 => [
        'titre'  => 'Les sous-traitants',
        'driver' => 'fp',
        'resume' => 'Paul n’ose pas refuser les demandes de ses sous-traitants.',
        'reactions' => [
            'A' => '« Tu es trop gentil avec eux, il faut que tu sois plus ferme. »',
            'B' => '« Je comprends que la relation compte pour toi, et elle compte aussi pour nous. Tu peux dire non sans la casser : propose-leur un point hebdomadaire avec un planning partagé, et je viens au premier pour poser le cadre avec toi. »',
            'C' => '« À partir de maintenant, c’est moi qui gère les sous-traitants. »',
            'D' => '« Rappelle-leur les pénalités prévues au contrat la prochaine fois qu’ils sont en retard. »',
        ],
        'ordre' => 'BDCA',
        'pourquoi' => [
            'B' => 'Son besoin de relation est validé, il reçoit la permission de dire non, un outil concret et un soutien.',
            'D' => 'L’outil est légitime, mais il est laissé seul face au conflit qu’il redoute.',
            'C' => 'On le dessaisit : ça règle le symptôme, pas la cause.',
            'A' => 'Un jugement sur la personne, qui risque de le faire culpabiliser.',
        ],
        'a_retenir' => 'Le Fais plaisir a besoin qu’on valide l’importance de la relation, puis de la permission de dire non, d’un outil concret et d’un soutien.',
    ],
];
