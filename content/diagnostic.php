<?php
/**
 * Diagnostic global de la page de résultat : blocs de texte assemblés selon le profil
 * (src/Services/Diagnostic.php). Textes à valider par Sacha.
 *
 * Variables remplacées : {P} driver(s) principal(aux) ; {sP} son score ; {S} autres drivers marqués avec leur score ;
 * {n} nombre de drivers au-dessus du seuil ; {seuil} seuil (20) ; {nb} nombre de drivers à égalité, en lettres.
 * Les accords (singulier / pluriel) sont gérés par des variantes de texte.
 */
return [
    // Sous le tableau de bord : ce qu'est (et n'est pas) un driver
    'definition' => [
        'Un driver est une injonction reçue tôt dans la vie, par l’éducation, l’école ou les premières expériences (« Sois parfait », « Fais plaisir », « Sois fort », « Fais des efforts », « Dépêche-toi »), puis intériorisée au point de devenir une façon spontanée d’aborder la réalité, le travail et les autres. Il nous a souvent été utile : c’est lui qui nous a appris la rigueur, l’attention aux autres, l’endurance, la persévérance ou la réactivité. Il devient un frein quand il s’impose à nous sans que nous l’ayons choisi, en particulier sous pression.',
        'Ces scores ne sont pas un verdict et ne disent pas qui vous êtes : nous sommes bien plus que nos drivers, et chacun peut apprendre à les reconnaître pour s’en libérer peu à peu. Prenez ce bilan comme une clé de lecture parmi d’autres, à confronter à ce que vous vivez et à ce que vous renvoient les autres.',
    ],

    // 1. Ouverture : la forme du profil
    'ouverture' => [
        'net' => 'Votre profil est nettement dominé par un driver : {P}, à {sP}/40, loin devant les quatre autres, qui restent à {seuil} ou moins. C’est donc lui qui colore le plus votre façon d’aborder le travail, les décisions et les relations, surtout quand la pression monte. Les autres drivers sont présents, comme chez chacun, mais pèsent peu dans vos réactions.',
        'combine_1' => 'Votre profil n’est pas porté par un seul driver mais par une combinaison : {P} arrive en tête ({sP}/40), avec {S}, qui se situe lui aussi au-dessus de {seuil}. Ces deux drivers ne s’additionnent pas simplement : ils se renforcent ou se contrarient selon les situations, et c’est leur association qui donne sa couleur à votre manière de manager.',
        'combine_2' => 'Votre profil n’est pas porté par un seul driver mais par une combinaison : {P} arrive en tête ({sP}/40), avec {S}, qui se situent eux aussi au-dessus de {seuil}. Ces drivers ne s’additionnent pas simplement : ils se renforcent ou se contrarient selon les situations, et c’est leur association qui donne sa couleur à votre manière de manager.',
        'egalite' => 'Votre profil repose sur {nb} drivers à égalité, {P}, avec {sP}/40 chacun. Aucun ne prend le pas sur l’autre : selon les situations, c’est tantôt l’un, tantôt l’autre qui prend le volant, et parfois les deux en même temps. Cette double influence est une richesse, à condition de savoir reconnaître lequel est à l’œuvre.',
        'egalite_n' => 'Votre profil repose sur {nb} drivers à égalité, {P}, avec {sP}/40 chacun. Aucun ne prend le pas sur les autres : selon les situations, c’est l’un ou l’autre qui prend le volant, et parfois plusieurs à la fois. Cette influence multiple est une richesse, à condition de savoir reconnaître lequel est à l’œuvre.',
        'egalite_plus_1' => '{S} complète ce tableau, avec un score lui aussi au-dessus de {seuil}.',
        'egalite_plus_2' => '{S} complètent ce tableau, avec des scores eux aussi au-dessus de {seuil}.',
        'charge' => 'Votre profil est très chargé : {n} drivers sur 5 dépassent {seuil}/40, avec {P} en tête ({sP}/40). Vous êtes donc sensible à plusieurs injonctions à la fois, parfois contradictoires, qui peuvent se relayer au cours d’une même journée. Ce type de profil appelle une attention particulière à la fatigue et au stress : quand tout semble obligatoire, il devient difficile de choisir ce qui compte vraiment.',
        'faible' => 'Aucun de vos drivers ne dépasse {seuil}/40 : aucun ne s’impose vraiment dans votre façon d’aborder la réalité. Votre tendance la plus présente est {P} ({sP}/40), mais elle reste modérée. Ce profil traduit une certaine liberté : vous êtes peu enfermé dans un mode de réaction automatique et vous pouvez sans doute adapter votre comportement aux situations. Sous forte pression, c’est néanmoins du côté de {P} qu’il faudra regarder en premier.',
        'faible_egalite' => 'Aucun de vos drivers ne dépasse {seuil}/40 : aucun ne s’impose vraiment dans votre façon d’aborder la réalité. Vos tendances les plus présentes sont {P}, à égalité ({sP}/40), mais elles restent modérées. Ce profil traduit une certaine liberté : vous êtes peu enfermé dans un mode de réaction automatique et vous pouvez sans doute adapter votre comportement aux situations. Sous forte pression, c’est néanmoins de ce côté qu’il faudra regarder en premier.',
    ],

    // 2. Chaque driver selon son rôle dans le profil
    'drivers' => [
        'sp' => [
            'principal' => 'Avec Sois parfait en tête, vous abordez le travail sous l’angle de la qualité et de la maîtrise. Vous voyez vite ce qui ne va pas, vous anticipez les erreurs et vous livrez un travail sur lequel on peut compter. Cette exigence est une vraie force de manager : elle rassure, elle structure et elle tire l’équipe vers le haut. Son revers apparaît quand le niveau visé devient sans limite : vous vérifiez encore, vous reprenez le travail des autres, vous différez une décision faute d’avoir toutes les informations. L’équipe peut alors se sentir contrôlée et perdre l’initiative.',
            'second'    => 'Sois parfait y ajoute une exigence de qualité : vous tenez à ce que les choses soient bien faites et vous repérez facilement les imperfections. Sous pression, ce driver peut vous pousser à vérifier ou reprendre plus que nécessaire, et à retarder une décision ou un envoi pour un détail.',
            'ressource' => 'Avec un Sois parfait peu présent, vous acceptez plus facilement le « suffisamment bien » et vous savez livrer sans tout vérifier ; veillez simplement à garder un niveau d’exigence suffisant sur les sujets qui le demandent.',
            'tendance'  => 'Sois parfait vous rend attentif à la qualité et aux détails. Modéré, il vous aide plus qu’il ne vous freine ; sous forte pression, il peut toutefois vous pousser à trop vérifier ou à différer un envoi.',
        ],
        'fp' => [
            'principal' => 'Avec Fais plaisir en tête, vous abordez le travail d’abord par la relation. Vous êtes attentif à l’ambiance, aux besoins de chacun et à la satisfaction de vos interlocuteurs, ce qui fait de vous un manager apprécié, à l’écoute, capable de créer de la cohésion et de la confiance. Son revers apparaît quand le besoin d’être apprécié prend le dessus : vous dites oui alors que vous pensez non, vous évitez un recadrage pour ne pas froisser, vous prenez sur vous les problèmes des autres. L’équipe peut alors manquer de cadre, et vous, de temps pour vos propres priorités.',
            'second'    => 'Fais plaisir y ajoute une forte sensibilité à la relation : vous tenez à ce que vos interlocuteurs soient satisfaits et à ce que le climat reste bon. Sous pression, il peut vous rendre difficile de refuser une demande ou d’assumer un désaccord.',
            'ressource' => 'Avec un Fais plaisir peu présent, vous savez dire non et assumer une décision impopulaire sans trop d’inconfort ; veillez à ce que cette liberté ne vous fasse pas négliger l’attention aux personnes et la reconnaissance.',
            'tendance'  => 'Fais plaisir vous rend attentif aux autres et à la qualité des relations. Modéré, c’est surtout une ressource ; sous forte pression, il peut toutefois vous faire accepter plus que vous ne le souhaitez.',
        ],
        'sf' => [
            'principal' => 'Avec Sois fort en tête, vous abordez la réalité en comptant d’abord sur vous-même. Vous gardez votre calme quand tout s’agite, vous encaissez beaucoup et vous tenez vos engagements : dans les moments difficiles, votre équipe sait qu’elle peut s’appuyer sur vous. Son revers apparaît quand l’autonomie devient isolement : vous ne demandez pas d’aide, vous minimisez les difficultés, vous montrez peu ce que vous ressentez. Vos collaborateurs peuvent alors hésiter à vous signaler un problème, et les alertes remonter trop tard.',
            'second'    => 'Sois fort y ajoute une forte autonomie : vous préférez régler les problèmes par vous-même et vous montrez peu vos émotions. Sous pression, il peut vous conduire à ne pas demander d’aide et à minimiser les difficultés, y compris auprès de votre hiérarchie.',
            'ressource' => 'Avec un Sois fort peu présent, vous demandez de l’aide sans difficulté et vous exprimez assez facilement ce que vous ressentez ; veillez à garder, dans les moments de tension, la stabilité dont votre équipe a besoin.',
            'tendance'  => 'Sois fort vous donne du sang-froid et de l’autonomie. Modéré, il vous sert plus qu’il ne vous isole ; sous forte pression, il peut toutefois vous faire garder les difficultés pour vous.',
        ],
        'fe' => [
            'principal' => 'Avec Fais des efforts en tête, vous abordez le travail comme un engagement à mener avec ténacité. Vous ne lâchez rien, vous aimez les défis et votre investissement inspire votre entourage. Son revers apparaît quand l’effort devient une fin en soi : vous valorisez la difficulté plus que le résultat, vous compliquez ce qui pourrait être simple, vous lancez de nouveaux sujets avant d’avoir terminé les précédents. L’équipe peut alors s’épuiser sans voir les résultats, ou croire qu’une réussite ne compte que si elle a coûté cher.',
            'second'    => 'Fais des efforts y ajoute de la ténacité : vous vous investissez pleinement et vous n’avez pas peur de ce qui est difficile. Sous pression, il peut vous faire préférer la voie la plus laborieuse, multiplier les chantiers et repousser le moment de conclure.',
            'ressource' => 'Avec un Fais des efforts peu présent, vous allez volontiers au plus simple et vous savez conclure ; veillez à garder de la persévérance face aux sujets longs et difficiles.',
            'tendance'  => 'Fais des efforts vous donne de la persévérance. Modéré, c’est surtout un atout ; sous forte pression, il peut toutefois vous faire compliquer les choses ou tarder à conclure.',
        ],
        'dt' => [
            'principal' => 'Avec Dépêche-toi en tête, vous abordez la réalité au rythme de l’action. Vous êtes réactif, vous décidez vite, vous faites avancer les sujets et vous tenez les délais serrés : votre énergie entraîne l’équipe. Son revers apparaît quand la vitesse devient la règle : vous coupez la parole, vous décidez avant d’avoir toutes les informations, vous menez plusieurs choses à la fois au risque d’oublier des étapes. Votre équipe peut alors se sentir bousculée et mal écoutée, et l’urgence permanente finit par fatiguer tout le monde.',
            'second'    => 'Dépêche-toi y ajoute de l’impatience et de la réactivité : vous aimez que les choses avancent vite. Sous pression, il peut vous faire écourter les échanges, décider trop tôt et accélérer au détriment de la qualité.',
            'ressource' => 'Avec un Dépêche-toi peu présent, vous savez prendre le temps d’écouter et de réfléchir avant d’agir ; veillez à garder de la réactivité quand la situation exige une décision rapide.',
            'tendance'  => 'Dépêche-toi vous donne de la réactivité. Modéré, c’est surtout une ressource ; sous forte pression, il peut toutefois vous faire accélérer plus que nécessaire.',
        ],
    ],

    // 3. Interactions entre deux drivers significatifs (clé dans l'ordre sp, fp, sf, fe, dt)
    'interactions' => [
        'sp_fp' => 'Associés, Sois parfait et Fais plaisir vous poussent à vouloir un travail irréprochable qui satisfasse tout le monde. Sous pression, vous risquez d’accepter des demandes que vous tiendrez ensuite à traiter parfaitement : la charge s’accumule et il devient difficile de poser des limites, aux autres comme à vous-même.',
        'sp_sf' => 'Associés, Sois parfait et Sois fort dessinent un manager exigeant et autonome, qui vise un travail parfait sans demander d’aide. Sous pression, vous risquez de vous isoler avec un travail que personne d’autre ne peut reprendre, et votre équipe peut découvrir les difficultés trop tard.',
        'sp_fe' => 'Associés, Sois parfait et Fais des efforts se renforcent : la barre est haute et le chemin pour l’atteindre doit être long. Sous pression, vous risquez d’avoir du mal à terminer, car rien ne vous paraît jamais assez bien ni assez travaillé.',
        'sp_dt' => 'Associés, Sois parfait et Dépêche-toi tirent en sens inverse : vous voulez aller vite et faire parfaitement. Sous pression, cette tension crée du stress : vous vous précipitez puis vous reprenez tout, ou vous oscillez entre urgence et vérifications. Savoir, pour chaque tâche, ce qui prime, du délai ou de la précision, devient essentiel.',
        'fp_sf' => 'Associés, Fais plaisir et Sois fort font de vous quelqu’un qui aide volontiers mais demande peu. Sous pression, vous risquez de porter les problèmes de chacun sans dire que vous saturez, disponible pour les autres et fermé sur vos propres besoins.',
        'fp_fe' => 'Associés, Fais plaisir et Fais des efforts vous conduisent à vous donner beaucoup de mal pour les autres. Sous pression, vous risquez d’accepter beaucoup et de transformer chaque demande en montagne : votre investissement devient épuisant, et pas toujours visible.',
        'fp_dt' => 'Associés, Fais plaisir et Dépêche-toi vous poussent à vouloir satisfaire tout le monde, et tout de suite. Sous pression, vous risquez de dire oui très vite à chaque demande et de courir de l’une à l’autre, sans prendre le temps de hiérarchiser.',
        'sf_fe' => 'Associés, Sois fort et Fais des efforts donnent une endurance remarquable : vous encaissez et vous redoublez d’efforts. Sous pression, vous risquez l’épuisement sans le dire, car demander de l’aide ou lever le pied peut vous sembler un échec.',
        'sf_dt' => 'Associés, Sois fort et Dépêche-toi dessinent un manager rapide et autonome, qui tranche vite et sans consulter. Sous pression, vous risquez d’avancer seul, sans partager l’information ni prendre le temps d’écouter les signaux que vous envoie votre équipe.',
        'fe_dt' => 'Associés, Fais des efforts et Dépêche-toi produisent beaucoup d’énergie : vous lancez vite et vous vous battez sur tous les fronts. Sous pression, vous risquez d’ouvrir de nouveaux sujets sans en terminer aucun, et d’entraîner l’équipe dans une course permanente.',
    ],

    // 4. Drivers marqués au-delà des trois décrits (profil chargé)
    'autres_actifs_1' => '{S} est également au-dessus de {seuil} : son influence s’ajoute à celles décrites ci-dessus et peut prendre le relais selon les situations.',
    'autres_actifs_2' => '{S} sont également au-dessus de {seuil} : leurs influences s’ajoutent à celles décrites ci-dessus et peuvent prendre le relais selon les situations.',

    // 5. Scores bas
    'ressources_intro' => 'À l’inverse, vos scores les plus bas sont aussi une information.',
    'ressources_intro_faible' => 'Vos autres drivers sont encore moins présents.',

    // 6. Conclusion
    'conclusion' => 'Ce bilan n’est pas un verdict. Il met en lumière des tendances, utiles pour comprendre certaines de vos réactions et celles que vous suscitez chez les autres. L’enjeu n’est pas de faire disparaître vos drivers, qui sont aussi à l’origine de vos atouts, mais de les reconnaître quand ils s’allument, pour choisir votre réponse plutôt que la subir. Les repères qui suivent vous y aideront.',
];
