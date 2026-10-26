<?php
/**
 * L'épreuve de conduite : les 3 cas (fiches imprimées).
 * Le référentiel envoyé à l'IA est dans content/prompts/cas_N.txt,
 * le prompt système dans content/prompts/systeme.txt.
 */
return [
    'longueur_min' => 60,    // caractères
    'longueur_max' => 1200,
    'cas' => [
    1 => [
        'titre'   => 'Le tracé modifié',
        'driver'  => 'fp',
        'resume'  => 'Ibrahim a accepté devant le ministère une modification de tracé sans évaluer ni coût ni délai.',
        'mission' => 'Rédigez ce que vous dites à Ibrahim pour reprendre la main sur cette demande avec lui.',
    ],
    2 => [
        'titre'   => 'L’élingue',
        'driver'  => 'sf',
        'resume'  => 'Joseph n’a pas déclaré un presque-accident et refuse la relève qu’on lui propose.',
        'mission' => 'Rédigez ce que vous dites à Joseph pour que l’incident soit déclaré et que la situation change.',
    ],
    3 => [
        'titre'   => 'Le dossier sans fin',
        'driver'  => 'fe',
        'resume'  => 'Aminata décrit longuement les obstacles du dédouanement sans jamais donner de date.',
        'mission' => 'Rédigez ce que vous dites à Aminata pour faire avancer le dossier.',
    ],
    ],
];
