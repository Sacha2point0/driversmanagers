<?php
/**
 * Questionnaire des drivers WORKLAB (« Testez vos drivers », 50 affirmations, échelle de 0 à 4).
 * Source : Questionnaire Les drivers - worklab.pdf
 *
 * Format :
 *  - 'echelle'   : libellés des réponses (valeurs 0 à 4)
 *  - 'questions' : liste ['id' => identifiant unique, 'driver' => sp|fp|sf|fe|dt, 'texte' => ...]
 * Le score d'un driver = somme des réponses à ses 10 questions (0 à 40).
 * Grille de correction : n° 1, 6, 11… Dépêche-toi ; 2, 7, 12… Fais des efforts ; 3, 8, 13… Sois fort ;
 * 4, 9, 14… Sois parfait ; 5, 10, 15… Fais plaisir.
 */
return [
    'intro' => 'Pour chaque affirmation, indiquez à quel point elle vous ressemble. Il n’y a pas de bonne ou de mauvaise réponse : répondez spontanément. Comptez environ 10 minutes.',
    // Lecture du profil (scores de 0 à 40 par driver)
    'lecture' => [
        'seuil'        => 20,  // jusqu'à 20 : driver peu déterminant
        'tres_marque'  => 30,  // de 21 à 30 : marqué ; au-delà : très marqué
        'ecart_proche' => 4,   // écart avec le driver principal jusqu'auquel un driver est jugé « proche »
    ],
    'echelle' => [
        0 => 'Ce n’est pas moi',
        1 => 'C’est rarement moi',
        2 => 'C’est quelques fois moi',
        3 => 'C’est souvent moi',
        4 => 'C’est tout à fait moi',
    ],
    'questions' => [
        ['id' => 'q01', 'driver' => 'dt', 'texte' => 'J’ai toujours l’impression de me battre contre la montre.'],
        ['id' => 'q02', 'driver' => 'fe', 'texte' => 'Je pense que l’effort est plus important que le résultat.'],
        ['id' => 'q03', 'driver' => 'sf', 'texte' => 'Je pense que dans la vie je dois me débrouiller seul(e).'],
        ['id' => 'q04', 'driver' => 'sp', 'texte' => 'Pour commencer une tâche, il me faut « toutes » les informations.'],
        ['id' => 'q05', 'driver' => 'fp', 'texte' => 'Il est important que les autres soient bien disposés à mon égard.'],
        ['id' => 'q06', 'driver' => 'dt', 'texte' => 'Le stress me dope.'],
        ['id' => 'q07', 'driver' => 'fe', 'texte' => 'J’ai souvent peur de ne pas être à la hauteur.'],
        ['id' => 'q08', 'driver' => 'sf', 'texte' => 'Je suis très exigeant(e) envers moi-même et les autres.'],
        ['id' => 'q09', 'driver' => 'sp', 'texte' => 'Pour être satisfait(e), je dois exceller.'],
        ['id' => 'q10', 'driver' => 'fp', 'texte' => 'Pour être aimé(e), je rends service plus que la moyenne.'],
        ['id' => 'q11', 'driver' => 'dt', 'texte' => 'Je ne peux pas déléguer, car les autres sont trop lents.'],
        ['id' => 'q12', 'driver' => 'fe', 'texte' => 'Je dois dépenser beaucoup d’énergie pour faire les choses.'],
        ['id' => 'q13', 'driver' => 'sf', 'texte' => 'Je ne suis pas proche de mes émotions et de leurs expressions.'],
        ['id' => 'q14', 'driver' => 'sp', 'texte' => 'Pour être efficace, ma tâche doit être parfaite.'],
        ['id' => 'q15', 'driver' => 'fp', 'texte' => 'Pour mon patron, je suis disponible, même chez moi.'],
        ['id' => 'q16', 'driver' => 'dt', 'texte' => 'Je parle vite.'],
        ['id' => 'q17', 'driver' => 'fe', 'texte' => 'J’ai un sentiment d’oppression et la peur d’exploser si je me laisse aller.'],
        ['id' => 'q18', 'driver' => 'sf', 'texte' => 'J’aime accomplir de nobles tâches.'],
        ['id' => 'q19', 'driver' => 'sp', 'texte' => 'Les faits, les chiffres, la logique : voilà les vraies valeurs.'],
        ['id' => 'q20', 'driver' => 'fp', 'texte' => 'Il faut dire aux gens ce qu’ils ont envie d’entendre.'],
        ['id' => 'q21', 'driver' => 'dt', 'texte' => 'J’ai envie d’interrompre les gens pour finir leur phrase à leur place.'],
        ['id' => 'q22', 'driver' => 'fe', 'texte' => 'J’ai le sentiment depuis longtemps d’être responsable de ce qui arrive aux autres.'],
        ['id' => 'q23', 'driver' => 'sf', 'texte' => 'L’échange intellectuel est le domaine où je suis à l’aise.'],
        ['id' => 'q24', 'driver' => 'sp', 'texte' => 'L’exactitude à tout prix : voilà le prix de mon image.'],
        ['id' => 'q25', 'driver' => 'fp', 'texte' => 'J’aime savoir qu’un collègue a besoin de moi.'],
        ['id' => 'q26', 'driver' => 'dt', 'texte' => 'Je dis souvent « Pressons… Oui… oui, et alors ? ».'],
        ['id' => 'q27', 'driver' => 'fe', 'texte' => 'Les autres disent de moi que je me plains souvent.'],
        ['id' => 'q28', 'driver' => 'sf', 'texte' => 'J’aime ce qui fait appel à la logique.'],
        ['id' => 'q29', 'driver' => 'sp', 'texte' => 'Je dois laisser croire à mon client que je sais tout.'],
        ['id' => 'q30', 'driver' => 'fp', 'texte' => 'Il faut savoir se mettre en quatre.'],
        ['id' => 'q31', 'driver' => 'dt', 'texte' => 'J’ai coutume de faire les cent pas lorsque je suis en attente de quelque chose.'],
        ['id' => 'q32', 'driver' => 'fe', 'texte' => 'Les gens viennent à moi sans que je le leur demande, car on me trouve gentil(le).'],
        ['id' => 'q33', 'driver' => 'sf', 'texte' => 'J’ai du mal à faire confiance et à m’abandonner.'],
        ['id' => 'q34', 'driver' => 'sp', 'texte' => 'Pour moi, une objection est le signe de mon incompétence ; aussi, je dois tout réussir pour éviter de me faire critiquer.'],
        ['id' => 'q35', 'driver' => 'fp', 'texte' => 'J’aime aider les autres.'],
        ['id' => 'q36', 'driver' => 'dt', 'texte' => 'Je tapote souvent avec mes doigts ou mes pieds.'],
        ['id' => 'q37', 'driver' => 'fe', 'texte' => 'Je crée des situations confuses où je suis stupide et impuissant(e).'],
        ['id' => 'q38', 'driver' => 'sf', 'texte' => 'La manière dont les autres me jugent sur ce que je fais m’importe beaucoup.'],
        ['id' => 'q39', 'driver' => 'sp', 'texte' => 'Je suis convaincu(e) que je suis le meilleur.'],
        ['id' => 'q40', 'driver' => 'fp', 'texte' => 'Je ne sais pas dire non.'],
        ['id' => 'q41', 'driver' => 'dt', 'texte' => 'Je vais trop vite, ce qui me fait faire des fautes d’inattention.'],
        ['id' => 'q42', 'driver' => 'fe', 'texte' => 'Que d’efforts pour répondre à toutes ces questions !'],
        ['id' => 'q43', 'driver' => 'sf', 'texte' => 'Je me sens en sécurité si je ne m’implique pas émotionnellement.'],
        ['id' => 'q44', 'driver' => 'sp', 'texte' => 'Tant qu’une tâche n’est pas réalisée comme je l’imaginais, je recommence.'],
        ['id' => 'q45', 'driver' => 'fp', 'texte' => 'J’aime jouer le rôle de confident(e).'],
        ['id' => 'q46', 'driver' => 'dt', 'texte' => 'Je ne peux pas rester inactif(ve) et, au besoin, je fais plusieurs choses en même temps.'],
        ['id' => 'q47', 'driver' => 'fe', 'texte' => 'Ma mère me disait souvent : « Avec un peu plus d’efforts… »'],
        ['id' => 'q48', 'driver' => 'sf', 'texte' => 'Dans une tâche à plusieurs, je n’aime pas que l’on ne respecte pas le timing et l’objectif, ni que l’on fasse des digressions.'],
        ['id' => 'q49', 'driver' => 'sp', 'texte' => 'J’attends de mes collaborateurs qu’ils fassent exactement ce que je leur dis.'],
        ['id' => 'q50', 'driver' => 'fp', 'texte' => 'En répondant à ces questions, je me demande si les réponses correspondent bien à ce que l’on attend de moi.'],
    ],
];
