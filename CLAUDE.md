# CLAUDE.md : Le permis de manager

Contexte de reprise pour Claude Code. Projet de Sacha Lopez (WORKLAB), séminaire managers Sogea-Satom, décembre 2026.

## Le projet
Application web du séminaire de ~200 managers de Sogea-Satom (filiale BTP de VINCI Construction en Afrique). L'après-midi porte sur les drivers (analyse transactionnelle, Kahler) dans l'univers du permis de conduire : « Le permis de manager ».

Déroulé de l'après-midi : mini-conférence (« La leçon de code »), icebreaker de positionnement dans l'espace (« Le rond-point », hors appli), puis trois épreuves gérées par l'appli. La séquence « La remise du permis » (clôture) n'est pas encore dans l'appli.

## Ce que fait l'appli (V1 livrée et testée)
- Connexion par email seul (liste importée en CSV : `email;prenom;nom;equipe`). Pas de code de vérification : choix assumé.
- Avant l'événement : questionnaire des drivers (« bilan de conduite »), puis résultat avec driver dominant, profil 5 drivers, atouts, voyant d'alerte, ce que vit l'équipe, feu vert (permission), 3 conseils. L'équipe est affichée.
- Jour J, séquences ouvertes une à une par l'animateur :
  1. **L'examen du code** : un membre de l'équipe saisit le placement des 20 cartes (jeu imprimé) ; 1 pt par carte, réponses ambiguës acceptées (`content/cartes.php`), zone « Pas forcément un driver ». Première saisie verrouillée pour l'équipe ; pas de corrigé affiché.
  2. **La conduite accompagnée** : chaque participant choisit dans la liste une personne d'une autre équipe, saisit ses 4 réponses (voyants, conduite sous pression, feu vert, panneau stop) et devine son driver principal. 1 pt par rencontre (sans plafond), +1 bonus si bien deviné (égalités acceptées), au participant et à son équipe. Pas de règle de délai entre deux rencontres. Validation sur la confiance. Le vrai driver n'est pas révélé en cas d'erreur.
  3. **Les manœuvres** : 3 fiches papier, l'équipe classe 4 réactions ; 1 pt par rang juste + 1 si le 1er est juste (5 max).
  4. **La conduite** : 3 cas papier, l'équipe rédige ; évaluation par l'API Claude (grille /10, JSON, température 0), retour en 2 phrases. La réponse est enregistrée avant l'appel ; en cas d'échec, l'animateur relance ou note à la main. La note animateur prime sur celle de l'IA.
- Écran plénière `/pleniere` : classement des équipes, rafraîchi toutes les 10 s.
- Espace animateur `/admin` : ouverture des séquences, import CSV, suivi et relance du questionnaire, répartition anonyme des drivers, stats de débrief par séquence (réussite par carte, devinettes par driver, 1er choix des manœuvres), projection anonyme des rencontres, sélection et projection des cas, exports CSV, purge.

## Décisions de Sacha à respecter
- Stack : PHP 8 natif MVC, PDO MySQL, HTML/CSS/JS sans framework, **aucune dépendance Composer** (déploiement par synchro GitHub sur Hostinger).
- Dédié à ce client (pas de généricité). Contenus dans `content/`, séparés du code.
- Français uniquement, charte WORKLAB (bleu #1226AA seul pour le texte, corail #FF8672, vert d'eau #3BD4AE, Nunito).
- Confidentialité : chacun ne voit que son résultat ; jamais le driver des autres dans les listes ; le client ne reçoit que des statistiques anonymes.

## Points ouverts
- **Questionnaire** : intégré le 26/10/2026 depuis « Questionnaire Les drivers - worklab.pdf » (50 items, 10 par driver, échelle 0 à 4, score 0 à 40, somme simple). Une réponse absente compte comme manquante (pas comme 0).
- Textes de résultat (`content/drivers.php`) à valider par Sacha.
- Égalité de scores : le 1er dans l'ordre `sp, fp, sf, fe, dt` est enregistré comme dominant (biais possible vers « Sois parfait » dans les stats).
- Nom du modèle IA (`ai.model`) à vérifier ; tester la vraie clé avant le séminaire.

## Déploiement (en cours)
- Dépôt GitHub : `Sacha2point0/driversmanagers`. Site Hostinger : `satom.worklab.fr`.
- Travailler dans un dossier local (pas dans Google Drive) pour le dépôt Git.
- Déploiement Git hPanel dans `public_html` (vide au 1er déploiement) ; le `.htaccess` racine redirige vers `public/` et bloque `config/`, `src/`, `content/`, etc.
- `config/config.php` est créé à la main sur le serveur, jamais versionné (voir `config/config.example.php`).
- Base : importer `database/schema.sql` ; `database/migrations/` seulement pour une base créée avec une version antérieure.

## Développement et tests
```bash
php -S localhost:8000 -t public tools/dev-router.php
```
Données de test : `tools/demo_participants.csv` (200 participants, 20 équipes). `ai.endpoint` (facultatif) permet de pointer vers un faux serveur d'API pour les tests.

## Supports papier associés (dans le dossier Drive S2 2026)
Jeu de 20 cartes à découper, fiches manœuvres, fiches cas, référentiel animateur et prompt IA, macro-déroulé PowerPoint. Les contenus de l'appli (`content/cartes.php`, `manoeuvres.php`, `cas.php`, `prompts/`) doivent rester alignés avec ces supports.
