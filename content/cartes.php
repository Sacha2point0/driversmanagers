<?php
/**
 * L'examen du code : les 20 cartes du jeu imprimé.
 * 'accepte' = réponses qui rapportent le point (la première est la réponse principale).
 * 'nd' = « Pas forcément un driver » (zone du plateau).
 */
return [
    'options' => [
        'sp' => 'Sois parfait',
        'fp' => 'Fais plaisir',
        'sf' => 'Sois fort',
        'fe' => 'Fais des efforts',
        'dt' => 'Dépêche-toi',
        'nd' => 'Pas forcément un driver',
    ],
    'cartes' => [
        1  => ['texte' => 'Le conducteur de travaux retarde pour la 4e fois l’envoi du planning au client…', 'accepte' => ['sp']],
        2  => ['texte' => 'En réunion de lancement, la cheffe de projet coupe la parole…', 'accepte' => ['dt']],
        3  => ['texte' => 'Isolé sur un chantier enclavé, un chef de chantier répond « Tout va bien ici »…', 'accepte' => ['sf']],
        4  => ['texte' => 'Le responsable matériel prête la niveleuse « pour rendre service »…', 'accepte' => ['fp']],
        5  => ['texte' => '« J’ai passé tout le week-end sur ce dossier d’appel d’offres… »', 'accepte' => ['fe']],
        6  => ['texte' => 'La contrôleuse de gestion bloque la clôture pour un écart de 300 €…', 'accepte' => ['sp', 'nd']],
        7  => ['texte' => 'Au quart d’heure sécurité, le chef d’équipe expédie les consignes en 3 minutes…', 'accepte' => ['dt', 'sf']],
        8  => ['texte' => 'Après la chute d’un outil, le chef d’équipe ne déclare pas le presque-accident…', 'accepte' => ['sf', 'dt']],
        9  => ['texte' => 'Un chef de projet répond « Je vais essayer » et propose la solution la plus compliquée…', 'accepte' => ['fe']],
        10 => ['texte' => 'La cheffe de service logistique lance cinq chantiers d’amélioration en même temps…', 'accepte' => ['fe', 'dt']],
        11 => ['texte' => 'Le directeur de travaux envoie des mails « URGENT » à 23h…', 'accepte' => ['dt']],
        12 => ['texte' => 'Face au ministère, un chef de projet accepte une modification de tracé…', 'accepte' => ['fp', 'nd']],
        13 => ['texte' => 'Le responsable QHSE réécrit lui-même les procédures de son équipe…', 'accepte' => ['sp']],
        14 => ['texte' => 'Un chef de chantier nouvellement promu ne pose jamais de questions…', 'accepte' => ['sf', 'fe', 'nd']],
        15 => ['texte' => 'L’assistant logistique décrit les difficultés du dédouanement sans donner de date…', 'accepte' => ['fe']],
        16 => ['texte' => 'Un chef d’équipe ne contredit jamais le directeur devant tout le monde…', 'accepte' => ['fp', 'nd']],
        17 => ['texte' => 'Pendant la saison des pluies, le conducteur de travaux visite trois chantiers par jour…', 'accepte' => ['dt', 'nd']],
        18 => ['texte' => 'En pleine réorganisation, une DAF pays ne montre aucune émotion…', 'accepte' => ['sf']],
        19 => ['texte' => 'Un chef de chantier n’ose pas refuser les demandes de ses sous-traitants…', 'accepte' => ['fp']],
        20 => ['texte' => '« Ce béton est conforme, mais je préfère refaire une série d’essais… »', 'accepte' => ['sp', 'nd']],
    ],
];
