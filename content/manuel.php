<?php
/**
 * Le manuel du conducteur (séquence d'équipe, remplace « La conduite accompagnée » depuis le 04/11/2026).
 * Pour chacun des 5 drivers, l'équipe renseigne 2 rubriques. Un profil est « renseigné » quand ses
 * 2 rubriques comptent au moins 'min_caracteres' caractères : il rapporte points.manuel point(s) à l'équipe.
 * Pas de corrigé affiché (décision de Sacha).
 */
return [
    'titre'    => 'Le manuel du conducteur',
    'consigne' => 'Ensemble, rédigez le mode d’emploi de chaque driver : ce qui le motive, et ce qui le crispe. Quelques mots ou une phrase suffisent. Chaque profil renseigné rapporte un point à votre équipe.',
    'rubriques' => [
        'motive' => ['icone' => '🟢', 'titre' => 'Ce qui le motive', 'aide' => 'Ce qu’il apprécie, ce qui le met en mouvement, ce qui le fait avancer.'],
        'crispe' => ['icone' => '⛔', 'titre' => 'Ce qui le crispe', 'aide' => 'Ce qui le braque instantanément : les mots, les attitudes, les situations à éviter.'],
    ],
    'min_caracteres' => 10,
    'max_caracteres' => 300,
];
