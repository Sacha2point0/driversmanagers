<?php
/**
 * L'épreuve de conduite : les manœuvres (fiches imprimées).
 * 'ordre' = classement de référence, de la plus aidante à la moins aidante.
 * Barème : 1 point par fiche au bon rang + 1 point si la première est juste (5 max).
 * 'situation' : texte complet de la fiche papier ; 'resume' : rappel court dans l'appli.
 * 'pourquoi' et 'a_retenir' : corrigé affiché à l'équipe après sa saisie.
 * Les fiches papier se régénèrent avec : php tools/fiches_manoeuvres.php > fiches.html (puis impression PDF).
 *
 * Version validée par Sacha le 02/11/2026 : 4 réactions de même longueur et de même forme ;
 * la meilleure accueille la réaction, valorise le driver, donne la permission et propose un cadre concret.
 * Rangs types : 1 accueil + valorisation + permission + cadre ; 2 accueil + permission sans rien de concret ;
 * 3 règle le problème sans accueil ; 4 valorisation qui nourrit le driver ou juge la personne (piège).
 * Doit rester aligné avec « Satom - épreuve de conduite - fiches manœuvres.pdf » et le référentiel animateur.
 */
return [
    1 => [
        'titre'  => 'Les commandes express',
        'driver' => 'dt',
        'resume' => 'Adja passe les commandes avant la vérification des métrés « pour gagner du temps » : deux commandes erronées ce mois-ci.',
        'situation' => [
            'Adja, cheffe de projet bâtiment, passe les commandes de matériaux avant que les métrés soient vérifiés, « pour gagner du temps ».',
            'Ce mois-ci, deux commandes étaient erronées : il a fallu tout refaire et attendre une nouvelle livraison.',
        ],
        'reactions' => [
            'A' => '« Adja, deux commandes à refaire en un mois, ce n’est plus possible. À partir de lundi, aucune commande ne part sans ma validation : tu m’envoies tes métrés le matin et je te réponds dans la journée. Le temps de remettre les choses à plat. »',
            'B' => '« Adja, je comprends que tu veuilles gagner du temps, et ton énergie fait vraiment avancer les chantiers. Ces erreurs arrivent quand on est sous pression. Prends le temps qu’il te faut à l’avenir, je te fais confiance pour trouver le bon rythme. »',
            'C' => '« Adja, bravo pour ta réactivité, c’est exactement ce qu’il nous faut en ce moment. Pour les deux commandes erronées, relance tout de suite le fournisseur et passe les nouvelles dès ce soir : on rattrapera le retard avant vendredi. »',
            'D' => '« Adja, je sais que tu commandes vite pour ne pas bloquer le chantier, et ta réactivité nous sauve souvent. Pour qu’elle ne nous coûte pas de nouvelles livraisons, on vérifie désormais les métrés à deux avant chaque commande : dix minutes, pas plus. »',
        ],
        'ordre' => 'DBAC',
        'pourquoi' => [
            'D' => 'Elle accueille sa logique (ne pas bloquer le chantier) et valorise sa réactivité, puis lui donne le droit de prendre le temps dans un cadre court qui respecte son besoin de vitesse.',
            'B' => 'L’accueil et la permission sont justes, mais il n’y a rien de concret : pour un Dépêche-toi, « prends le temps » reste flou et ne change rien.',
            'A' => 'Le problème est traité, mais sans accueil et en lui retirant son autonomie : le manager devient un goulot d’étranglement.',
            'C' => 'Le piège : la valorisation nourrit le driver. On lui demande d’aller encore plus vite, et la cause des erreurs reste intacte.',
        ],
        'a_retenir' => 'La réaction la plus aidante ne combat pas le Dépêche-toi : elle reconnaît ce que la réactivité apporte, puis donne la permission de prendre le temps dans un cadre court et concret.',
    ],
    2 => [
        'titre'  => 'Le planning de demain',
        'driver' => 'sp',
        'resume' => 'Fatou refuse pour la troisième fois d’envoyer un planning qu’elle juge « pas encore assez fiable » : remise au client demain à 9 h.',
        'situation' => [
            'Fatou, ingénieure méthodes, refuse pour la troisième fois d’envoyer le planning d’exécution au client. Elle le juge « pas encore assez fiable » : il reste deux incohérences mineures.',
            'La remise au client est prévue demain à 9 h.',
        ],
        'reactions' => [
            'A' => '« Fatou, ta rigueur fait la confiance du client, et je comprends que ces deux points te gênent. Ton planning est fiable à 90 %, c’est le niveau attendu : on note les deux réserves et tu me l’envoies à 18 h. »',
            'B' => '« Fatou, ta rigueur est vraiment précieuse et je comprends que tu ne veuilles rien laisser passer. Prends le temps de corriger ces deux incohérences, même tard ce soir : mieux vaut un planning sans aucune erreur, je préviendrai le client si besoin. »',
            'C' => '« Fatou, c’est la troisième fois que ce planning est repoussé et le client l’attend demain à 9 h. Envoie-le tel quel ce soir : les deux incohérences sont mineures et personne ne les remarquera. On en reparlera après la remise. »',
            'D' => '« Fatou, je vois que ces deux incohérences te préoccupent, et ton exigence est une vraie force pour l’équipe. Montre-les-moi maintenant : on les regarde ensemble, point par point, puis on décide ensemble si elles bloquent vraiment l’envoi au client. »',
        ],
        'ordre' => 'ADCB',
        'pourquoi' => [
            'A' => 'Elle valorise sa rigueur et accueille sa gêne, puis lui donne un critère explicite de « suffisamment bien », une façon de traiter le doute (les réserves) et une échéance.',
            'D' => 'Le manager accueille et accompagne, mais la séance reste centrée sur le détail : c’est encore le driver qui mène.',
            'C' => 'Le délai est tenu, mais ce qui compte pour elle est balayé (« personne ne les remarquera ») : elle risque de le vivre comme un manque de considération.',
            'B' => 'Le piège : la valorisation sert à renforcer le driver (« sans aucune erreur, même tard ce soir »).',
        ],
        'a_retenir' => 'La réaction la plus aidante ne combat pas le Sois parfait : elle reconnaît ce que la rigueur apporte, puis donne la permission du « suffisamment bien » avec un critère explicite et une échéance.',
    ],
    3 => [
        'titre'  => 'Les sous-traitants',
        'driver' => 'fp',
        'resume' => 'Paul n’ose pas refuser les demandes de ses sous-traitants : son équipe absorbe les retards en heures supplémentaires.',
        'situation' => [
            'Paul, chef de chantier, n’ose pas refuser les demandes de ses sous-traitants locaux, de peur de « casser la relation ».',
            'Ils accumulent les retards, et c’est son équipe qui les absorbe en heures supplémentaires.',
        ],
        'reactions' => [
            'A' => '« Paul, je sais que la relation avec nos sous-traitants compte beaucoup pour toi, et c’est une qualité. Tu as le droit de leur dire non quand une demande pénalise ton équipe : fais-toi confiance, tu sauras trouver les mots le moment venu. »',
            'B' => '« Paul, ton équipe fait trop d’heures supplémentaires à cause de ces retards. À partir de maintenant, c’est moi qui gère les échanges avec les sous-traitants : tu me fais remonter leurs demandes et je tranche. Tu pourras te concentrer sur ton chantier. »',
            'C' => '« Paul, ta relation avec nos sous-traitants est un vrai atout, et je comprends que tu tiennes à la préserver. Tu peux dire non sans la casser : propose-leur un point hebdomadaire avec planning partagé, et je viens au premier avec toi. »',
            'D' => '« Paul, ta gentillesse est appréciée de tous, mais là elle se retourne contre toi. Il faut que tu sois plus ferme avec eux : rappelle-leur les pénalités du contrat dès le prochain retard, et ne leur lâche plus rien. »',
        ],
        'ordre' => 'CABD',
        'pourquoi' => [
            'C' => 'Elle valide son besoin de relation, lui donne la permission de dire non, un outil concret et un soutien.',
            'A' => 'La permission est la bonne, mais il est laissé seul face au conflit qu’il redoute, sans outil.',
            'B' => 'Le symptôme est réglé en le dessaisissant, mais pas la cause : il n’apprend pas à dire non.',
            'D' => 'Le piège : le compliment devient un jugement sur la personne (« ta gentillesse se retourne contre toi »), puis il est poussé seul vers le conflit.',
        ],
        'a_retenir' => 'La réaction la plus aidante ne combat pas le Fais plaisir : elle reconnaît la valeur de la relation, puis donne la permission de dire non avec un outil concret et un soutien.',
    ],
];
