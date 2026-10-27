<?php
/**
 * Les 5 drivers, dans l'univers du permis.
 * Textes de la page de résultat (à faire valider par Sacha) :
 *  - nom, panneau, couleur : identité du driver ;
 *  - atouts, voyant : listes ; « voyant » suit « Quand la pression monte, je risque de… » ;
 *  - equipe : paragraphe quand le driver est principal ; equipe_court : phrase quand il est secondaire ;
 *  - feu_vert, feu_vert_texte : la permission qui contrebalance le driver ;
 *  - conseils : le 1er est repris quand le driver est secondaire.
 * Les textes du diagnostic global sont dans content/diagnostic.php.
 */
return [
    'sp' => [
        'nom'      => 'Sois parfait',
        'panneau'  => 'Le contrôle technique permanent',
        'couleur'  => '#3BD4AE',
        'atouts'   => [
            'La fiabilité : mes dossiers, mes plannings et mes chiffres sont solides.',
            'Le sens du détail, précieux sur les sujets à fort enjeu.',
            'Une exigence qui tire l’équipe vers le haut.',
        ],
        'voyant'   => [
            'reprendre le travail de mes collaborateurs au lieu de leur faire un retour',
            'retarder une décision ou un envoi pour un détail mineur',
            'faire des explications longues, pleines de précisions et de parenthèses',
        ],
        'equipe'   => 'Votre équipe peut se sentir contrôlée, ou n’ose plus livrer tant que ce n’est pas parfait. Les délais en pâtissent, et l’initiative aussi.',
        'equipe_court' => 'Avec Sois parfait, elle peut se sentir contrôlée et hésiter à livrer tant que ce n’est pas parfait.',
        'feu_vert' => 'Le droit à l’erreur et au « suffisamment bien »',
        'feu_vert_texte' => 'Un travail fiable à 90 % livré à temps vaut souvent mieux qu’un travail parfait livré en retard. Vous avez le droit de vous tromper, et votre équipe aussi.',
        'conseils' => [
            'Avant chaque tâche, fixez le niveau de qualité attendu et tenez-vous-y.',
            'Déléguez avec des critères clairs, puis faites un retour au lieu de refaire.',
            'Saluez ce qui est réussi avant de pointer ce qui manque.',
        ],
    ],
    'fp' => [
        'nom'      => 'Fais plaisir',
        'panneau'  => 'Cédez le passage',
        'couleur'  => '#FF8672',
        'atouts'   => [
            'L’écoute et l’empathie : mon équipe se sent considérée.',
            'Un vrai talent pour la relation avec les clients et les partenaires.',
            'La capacité à créer de la cohésion.',
        ],
        'voyant'   => [
            'dire oui alors que je pense non',
            'éviter un recadrage nécessaire pour ne pas froisser',
            'chercher l’approbation (« ça vous va ? », « si ça ne vous dérange pas… »)',
        ],
        'equipe'   => 'Votre équipe peut manquer de cadre et de décisions claires. Les engagements pris pour faire plaisir finissent par peser sur tout le monde.',
        'equipe_court' => 'Avec Fais plaisir, elle peut manquer de cadre et de décisions claires.',
        'feu_vert' => 'Le droit de dire non',
        'feu_vert_texte' => 'Dire non à une demande n’est pas dire non à la personne. Une relation solide supporte un désaccord exprimé avec respect.',
        'conseils' => [
            'Avant de répondre à une demande, prenez un temps : « Je regarde et je reviens vers vous demain. »',
            'Annoncez les décisions difficiles vous-même, clairement et sans vous excuser.',
            'Demandez-vous : qu’est-ce que je veux, moi, dans cette situation ?',
        ],
    ],
    'sf' => [
        'nom'      => 'Sois fort',
        'panneau'  => 'Pas de dépanneuse',
        'couleur'  => '#1226AA',
        'atouts'   => [
            'Le sang-froid dans les moments de crise.',
            'La fiabilité dans la durée : je tiens mes engagements.',
            'La capacité à rassurer mon équipe.',
        ],
        'voyant'   => [
            'répondre « tout va bien » alors que la situation se dégrade',
            'refuser l’aide ou le renfort qu’on me propose',
            'paraître distant, voire froid, et parler peu de moi',
        ],
        'equipe'   => 'Votre équipe peut hésiter à vous signaler un problème ou une difficulté, de peur de paraître faible. Les alertes remontent alors trop tard.',
        'equipe_court' => 'Avec Sois fort, elle peut hésiter à vous signaler une difficulté, de peur de paraître faible.',
        'feu_vert' => 'Le droit de demander de l’aide',
        'feu_vert_texte' => 'Demander de l’aide est une compétence de manager, pas une faiblesse. Dire ce que vous ressentez autorise aussi votre équipe à le faire.',
        'conseils' => [
            'Demandez de l’aide au moins une fois cette semaine, sur un sujet concret.',
            'Partagez un état réel, pas seulement « ça va », lors de vos points d’équipe.',
            'Remerciez explicitement ceux qui vous signalent un problème.',
        ],
    ],
    'fe' => [
        'nom'      => 'Fais des efforts',
        'panneau'  => 'Route en montée',
        'couleur'  => '#ECD925',
        'atouts'   => [
            'La ténacité face aux obstacles.',
            'Un engagement fort, qui inspire.',
            'Le goût des défis et des sujets complexes.',
        ],
        'voyant'   => [
            'dire « je vais essayer » plutôt que « je le fais »',
            'parler de la difficulté plutôt que du résultat',
            'lancer de nouveaux sujets avant d’avoir terminé les précédents',
        ],
        'equipe'   => 'Votre équipe peut s’épuiser à la tâche sans voir les résultats, ou penser que la réussite n’est reconnue que si elle a coûté cher.',
        'equipe_court' => 'Avec Fais des efforts, elle peut s’épuiser à la tâche sans voir les résultats.',
        'feu_vert' => 'Le droit de réussir simplement',
        'feu_vert_texte' => 'Une réussite facile reste une réussite. Ce qui compte, c’est le résultat obtenu, pas la peine qu’il a coûtée.',
        'conseils' => [
            'Fixez une seule priorité par semaine et menez-la jusqu’au bout.',
            'Remplacez « je vais essayer » par « je le fais d’ici… ».',
            'Valorisez les résultats de votre équipe, pas seulement son investissement.',
        ],
    ],
    'dt' => [
        'nom'      => 'Dépêche-toi',
        'panneau'  => 'Excès de vitesse',
        'couleur'  => '#FFBE9F',
        'atouts'   => [
            'La réactivité face aux imprévus.',
            'La capacité à tenir des délais serrés.',
            'Une énergie qui entraîne l’équipe.',
        ],
        'voyant'   => [
            'interrompre les autres ou finir leurs phrases',
            'écourter les temps d’échange et d’écoute',
            'faire plusieurs choses à la fois et oublier des étapes',
        ],
        'equipe'   => 'Votre équipe peut se sentir bousculée, mal écoutée, et commettre des erreurs par précipitation. L’urgence permanente fatigue.',
        'equipe_court' => 'Avec Dépêche-toi, elle peut se sentir bousculée et mal écoutée.',
        'feu_vert' => 'Le droit de prendre le temps',
        'feu_vert_texte' => 'Prendre cinq minutes pour vérifier ou écouter fait souvent gagner des heures. Le bon rythme n’est pas toujours le plus rapide.',
        'conseils' => [
            'Avant une décision importante, prenez dix minutes pour vérifier les informations clés.',
            'En réunion, laissez chacun finir sa phrase avant de répondre.',
            'Gardez dans votre agenda des temps d’échange avec votre équipe qui ne cèdent pas à l’urgence.',
        ],
    ],
];
