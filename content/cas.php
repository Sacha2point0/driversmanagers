<?php
/**
 * L'épreuve de conduite : 6 situations (fiches imprimées), rédigées en équipe et évaluées par l'IA.
 * Les situations 4 à 6 reprennent les anciennes manœuvres (fusion validée par Sacha le 13/11/2026).
 *
 * Barème par situation (6 points) :
 *  - rédaction notée par l'IA sur 5 : accueil, valorisation du driver, permission, cadre concret, sans piège (1 point chacun) ;
 *  - 1 point bonus si l'équipe identifie le bon driver (calculé par l'appli, pas par l'IA).
 *
 * 'contexte' et 'situation' : texte des fiches papier (php tools/fiches_conduite.php) ; 'resume' : rappel court dans l'appli.
 * 'reference' et 'a_retenir' : corrigé montré à l'équipe après l'évaluation.
 * Le référentiel envoyé à l'IA est dans content/prompts/cas_N.txt, le prompt système dans content/prompts/systeme.txt.
 */
return [
    'longueur_min'  => 60,    // caractères
    'longueur_max'  => 1200,
    'max_redaction' => 5,     // note de l'IA (ou de l'animateur)
    'bonus_driver'  => 1,     // driver identifié
    'cas' => [
    1 => [
        'titre'    => 'Le tracé modifié',
        'driver'   => 'fp',
        'resume'   => 'Ibrahim a accepté devant le ministère une modification de tracé sans évaluer ni coût ni délai.',
        'contexte' => 'Chantier de réhabilitation d’une route nationale. Ibrahim est chef de projet dans votre équipe depuis deux ans. Il est apprécié du client et de ses équipes.',
        'situation' => [
            'En réunion de chantier, le représentant du ministère (maître d’ouvrage) demande de décaler le tracé sur 300 mètres pour contourner un marché.',
            'Ibrahim répond aussitôt : « Bien sûr, nous allons nous adapter. » Il ne dit rien du coût ni du délai.',
            'En sortant, il vous confie : « Je ne pouvais pas dire non devant eux, ils l’auraient mal pris. » Le bureau d’études estime que la modification ajoute plusieurs semaines au planning.',
        ],
        'mission'  => 'Rédigez ce que vous dites à Ibrahim pour reprendre la main sur cette demande avec lui.',
        'reference' => '« Ibrahim, je sais combien la relation avec le ministère compte pour toi, et c’est un vrai atout pour nous. On peut accueillir leur demande sans s’engager sur tout : chiffrons ensemble l’impact coût et délai d’ici jeudi, puis tu leur présenteras deux options. Tu peux leur dire : “Nous étudions votre demande et revenons vers vous avec les conditions.” Si tu veux, je t’accompagne au rendez-vous. »',
        'a_retenir' => 'La réponse la plus aidante ne désavoue pas Ibrahim : elle reconnaît la valeur de la relation client, puis lui donne la permission, et les mots, pour revenir vers le client avec des conditions chiffrées et datées.',
    ],
    2 => [
        'titre'    => 'L’élingue',
        'driver'   => 'sf',
        'resume'   => 'Joseph n’a pas déclaré un presque-accident et refuse la relève qu’on lui propose.',
        'contexte' => 'Construction d’une station de pompage. Joseph est chef d’équipe, réputé solide et fiable. Depuis six semaines, il ne prend aucun jour de repos pour tenir le planning.',
        'situation' => [
            'Lors d’un levage, une élingue a lâché. La charge est tombée à deux mètres d’un ouvrier. Personne n’a été blessé.',
            'Joseph n’a rien déclaré : vous l’apprenez par un membre de son équipe.',
            'Quand vous lui en parlez, il hausse les épaules : « Ce n’est rien, j’ai géré. On ne va pas arrêter le chantier pour ça. » La semaine dernière, il a refusé la relève que vous lui proposiez.',
        ],
        'mission'  => 'Rédigez ce que vous dites à Joseph pour que l’incident soit déclaré et que la situation change.',
        'reference' => '« Joseph, je vois tout ce que tu portes sur ce chantier depuis six semaines. Ce qui s’est passé avec l’élingue n’est pas une faute de ta part : c’est une information précieuse pour la sécurité de tous. On la déclare ensemble aujourd’hui, avant 17 h. Et je ne veux pas que tu tiennes seul : à partir de lundi, quelqu’un te relaie deux jours par semaine. Dis-moi ce dont tu as besoin d’autre. »',
        'a_retenir' => 'La réponse la plus aidante ne combat pas le Sois fort : elle reconnaît son engagement, présente la déclaration comme une information utile et non comme une faute, et l’autorise à ne pas tout porter seul, avec une aide datée.',
    ],
    3 => [
        'titre'    => 'Le dossier sans fin',
        'driver'   => 'fe',
        'resume'   => 'Aminata décrit longuement les obstacles du dédouanement sans jamais donner de date.',
        'contexte' => 'Direction logistique pays. Aminata pilote depuis deux mois l’amélioration des délais de dédouanement. Elle multiplie les réunions, les notes et les tableaux de suivi.',
        'situation' => [
            'Les matériaux d’un chantier prioritaire sont bloqués au port depuis trois semaines.',
            'Quand vous lui demandez où en est le dossier, Aminata décrit longuement tous les obstacles rencontrés, mais ne donne aucune date.',
            'Elle conclut : « C’est un vrai combat, mais on avance. » Le conducteur de travaux vous relance chaque jour.',
        ],
        'mission'  => 'Rédigez ce que vous dites à Aminata pour faire avancer le dossier.',
        'reference' => '« Aminata, ton travail sur le dédouanement va nous servir longtemps. Aujourd’hui, j’ai besoin d’une seule chose : les matériaux du chantier sur site. D’ici demain midi, dis-moi quelle est la prochaine étape qui bloque, qui peut la débloquer et à quelle date les matériaux peuvent sortir du port. Si un appel de ma part peut aider, je le passe dans la journée. »',
        'a_retenir' => 'La réponse la plus aidante ne félicite pas l’effort : elle valorise le travail accompli, recentre sur un seul résultat et demande une prochaine étape précise, avec une échéance et un appui.',
    ],
    4 => [
        'titre'    => 'Les commandes express',
        'driver'   => 'dt',
        'resume'   => 'Adja passe les commandes avant la vérification des métrés « pour gagner du temps » : deux commandes erronées ce mois-ci.',
        'contexte' => 'Construction d’un bâtiment administratif. Adja est cheffe de projet bâtiment dans votre équipe. Elle est connue pour sa réactivité.',
        'situation' => [
            'Adja passe les commandes de matériaux avant que les métrés soient vérifiés, « pour gagner du temps ».',
            'Ce mois-ci, deux commandes étaient erronées : il a fallu tout refaire et attendre une nouvelle livraison.',
        ],
        'mission'  => 'Rédigez ce que vous dites à Adja pour que ses commandes soient justes, sans éteindre sa réactivité.',
        'reference' => '« Adja, je sais que tu commandes vite pour ne pas bloquer le chantier, et ta réactivité nous sauve souvent. Pour qu’elle ne nous coûte pas de nouvelles livraisons, on vérifie désormais les métrés à deux avant chaque commande : dix minutes, pas plus. »',
        'a_retenir' => 'La réponse la plus aidante ne combat pas le Dépêche-toi : elle reconnaît ce que la réactivité apporte, puis donne la permission de prendre le temps dans un cadre court et concret.',
    ],
    5 => [
        'titre'    => 'Le planning de demain',
        'driver'   => 'sp',
        'resume'   => 'Fatou refuse pour la troisième fois d’envoyer un planning qu’elle juge « pas encore assez fiable » : remise au client demain à 9 h.',
        'contexte' => 'Fatou est ingénieure méthodes dans votre équipe. Ses plannings sont réputés solides.',
        'situation' => [
            'Fatou refuse pour la troisième fois d’envoyer le planning d’exécution au client. Elle le juge « pas encore assez fiable » : il reste deux incohérences mineures.',
            'La remise au client est prévue demain à 9 h.',
        ],
        'mission'  => 'Rédigez ce que vous dites à Fatou pour que le planning parte à temps.',
        'reference' => '« Fatou, ta rigueur fait la confiance du client, et je comprends que ces deux points te gênent. Ton planning est fiable à 90 %, c’est le niveau attendu : on note les deux réserves et tu me l’envoies à 18 h. »',
        'a_retenir' => 'La réponse la plus aidante ne combat pas le Sois parfait : elle reconnaît ce que la rigueur apporte, puis donne la permission du « suffisamment bien » avec un critère explicite et une échéance.',
    ],
    6 => [
        'titre'    => 'Les sous-traitants',
        'driver'   => 'fp',
        'resume'   => 'Paul n’ose pas refuser les demandes de ses sous-traitants : son équipe absorbe les retards en heures supplémentaires.',
        'contexte' => 'Paul est chef de chantier dans votre équipe. Il travaille avec plusieurs sous-traitants locaux.',
        'situation' => [
            'Paul n’ose pas refuser les demandes de ses sous-traitants, de peur de « casser la relation ».',
            'Ils accumulent les retards, et c’est son équipe qui les absorbe en heures supplémentaires.',
        ],
        'mission'  => 'Rédigez ce que vous dites à Paul pour qu’il reprenne la main face à ses sous-traitants.',
        'reference' => '« Paul, ta relation avec nos sous-traitants est un vrai atout, et je comprends que tu tiennes à la préserver. Tu peux dire non sans la casser : propose-leur un point hebdomadaire avec planning partagé, et je viens au premier avec toi. »',
        'a_retenir' => 'La réponse la plus aidante ne combat pas le Fais plaisir : elle reconnaît la valeur de la relation, puis donne la permission de dire non avec un outil concret et un soutien.',
    ],
    ],
];
