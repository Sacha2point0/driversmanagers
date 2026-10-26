# Le permis de manager

Application du séminaire managers **Sogea-Satom** (WORKLAB, décembre 2026), sur le thème des drivers et du permis de conduire.

- **Avant l'événement** : chaque participant se connecte avec son email, remplit son *bilan de conduite* (questionnaire des drivers) et découvre son résultat, ses atouts, son voyant d'alerte, son feu vert et ses conseils. Il voit aussi son équipe.
- **Le jour J** : l'animateur ouvre les épreuves une à une.
  1. **L'examen du code** : un membre de chaque équipe saisit le placement des 20 cartes du jeu imprimé (1 point par carte).
  2. **La conduite accompagnée** : chacun enregistre ses rencontres avec des participants d'autres équipes, en notant les 4 réponses de « Mon tableau de bord » et en devinant le driver principal de la personne rencontrée. 1 point par rencontre (sans plafond) et 1 point bonus si le driver est bien deviné, au participant et à son équipe. En cas d'erreur, le vrai driver n'est pas révélé ; une bonne réponse le confirme, ce qu'il faut annoncer au briefing.
  3. **Les manœuvres** : l'équipe saisit son classement des 4 réactions pour chacune des 3 fiches (5 points par manœuvre).
  4. **La conduite** : l'équipe rédige sa réponse à chacun des 3 cas ; l'API Claude la note sur 10 et renvoie deux phrases de retour.
- **Écran plénière** (`/pleniere`) : classement des équipes en direct, actualisé toutes les 10 secondes.
- **Espace animateur** (`/admin`) : ouverture des séquences, import des participants, suivi du questionnaire, statistiques pour le débrief, correction des notes de l'IA, projection, exports CSV, suppression des données.

Pile : PHP 8.1 minimum (testé en 8.3), MySQL 5.7+ / MySQL 8 / MariaDB 10.4+, HTML/CSS/JS sans framework. **Aucune dépendance Composer** : la synchronisation GitHub suffit au déploiement.

---

## Arborescence

```
app-permis-de-manager/
├── public/              ← racine web (index.php, .htaccess, assets/)
├── src/                 ← code PHP (Core, Controllers, Models, Services)
├── views/               ← gabarits HTML
├── content/             ← CONTENUS MODIFIABLES SANS TOUCHER AU CODE
│   ├── questionnaire.php    questions et échelle (questionnaire des drivers WORKLAB)
│   ├── drivers.php          textes de résultat des 5 drivers
│   ├── cartes.php           20 cartes et réponses acceptées
│   ├── manoeuvres.php       3 manœuvres et ordre de référence
│   ├── cas.php              3 cas (titres, missions, longueurs)
│   ├── rencontres.php       4 questions de « Mon tableau de bord »
│   └── prompts/             prompt système de l'IA et référentiel de chaque cas
├── config/
│   └── config.example.php   à copier en config.php (exclu de Git)
├── database/schema.sql  ← création des tables
├── storage/logs/        ← journal d'erreurs PHP
├── tools/               ← données de démonstration, serveur local
└── .htaccess            ← protège le code si la racine web est le dépôt entier
```

---

## Installation sur Hostinger

1. **Base de données** : hPanel → *Bases de données MySQL* → créer une base et un utilisateur. Ouvrir phpMyAdmin et importer `database/schema.sql`. Si la base a été créée avec une version précédente, importer aussi les fichiers de `database/migrations/` qui ne l'ont pas encore été.
2. **Code** : hPanel → *Avancé → Git* → relier le dépôt GitHub et déployer.
   - **Idéal** : faire pointer le (sous-)domaine sur le dossier `public/` (hPanel → *Domaines* → racine du document).
   - **Sinon** : si la racine est le dossier du dépôt, le `.htaccess` racine redirige tout vers `public/` et bloque l'accès au code, à la configuration et aux contenus.
3. **Configuration** : avec le *Gestionnaire de fichiers*, copier `config/config.example.php` en `config/config.php` et renseigner :
   - `db` : hôte (`localhost`), nom de la base, utilisateur, mot de passe ;
   - `admin.password_hash` : générer le hash du mot de passe animateur, par exemple sur votre Mac :
     `php -r "echo password_hash('votre-mot-de-passe', PASSWORD_DEFAULT), PHP_EOL;"`
   - `ai.api_key` : clé de l'API Claude (console.anthropic.com) ; vérifier le nom du modèle (`ai.model`) dans la documentation Anthropic ;
   - `app.base_url` : laisser `''` si l'application est à la racine du sous-domaine.
   `config/config.php` n'est jamais écrasé par la synchronisation Git, puisqu'il n'est pas versionné.
4. **Droits** : le dossier `storage/logs/` doit être accessible en écriture par PHP.
5. **HTTPS** : activer le certificat SSL du sous-domaine (le cookie de session passe alors en mode sécurisé).
6. **PHP** : version 8.1 ou plus, extensions `pdo_mysql`, `curl` et `mbstring` (actives par défaut sur Hostinger). Pour l'évaluation des cas, un délai d'exécution de 60 secondes est recommandé.

---

## Préparer l'événement

1. **Importer les participants** : `/admin` → *Participants* → importer un CSV UTF-8 avec la ligne d'en-tête `email;prenom;nom;equipe`. Les équipes sont créées à partir de la colonne `equipe`. Un réimport met à jour les noms et les équipes sans effacer les questionnaires.
2. **Diffuser le lien** de l'application aux participants (l'application n'envoie pas d'email). Suivre le taux de réponse dans le tableau de bord ; le filtre *Questionnaire à faire* donne la liste des emails à relancer.
3. **Le jour J** : ouvrir chaque séquence au bon moment depuis le tableau de bord, puis la refermer à la fin du temps imparti (plus aucune saisie possible).
4. **Débrief** :
   - *Examen du code* : tableau de réussite par carte, pour choisir les cartes à discuter.
   - *Rencontres* : part de bonnes devinettes par driver et confusions les plus fréquentes ; projection anonyme de 9 réponses tirées au hasard, par driver et par question.
   - *Manœuvres* : répartition des réactions classées en premier.
   - *Cas* : cocher « Projeter » sur les meilleures réponses, puis ouvrir *Projeter la sélection*.
5. **Après le séminaire** : exporter ce qui doit l'être, puis *Données → Supprimer → Tout*.

---

## Le questionnaire

Le questionnaire (`content/questionnaire.php`) est celui de WORKLAB, « Testez vos drivers » : 50 affirmations, 10 par driver, échelle de 0 (« Ce n’est pas moi ») à 4 (« C’est tout à fait moi »), score de 0 à 40 par driver. Pour le modifier, respecter le format décrit en tête du fichier :

- chaque question a un identifiant unique et un driver (`sp`, `fp`, `sf`, `fe`, `dt`) ;
- le score d'un driver est la somme des réponses à ses questions ;
- le driver dominant est le score le plus élevé ; en cas d'égalité, la page de résultat affiche les co-dominants, et le premier dans l'ordre `sp, fp, sf, fe, dt` est enregistré comme dominant.

Si le questionnaire existant utilise une autre méthode de calcul, adapter `src/Services/Questionnaire.php` (méthode `compute`).

Les textes de résultat (`content/drivers.php`) sont à valider avant l'ouverture.

---

## L'évaluation des cas par l'IA

- La réponse de l'équipe est **toujours enregistrée avant** l'appel à l'API.
- L'appel se fait au moment de l'envoi (température 0, réponse attendue en JSON). En cas d'échec (clé absente, API saturée, délai dépassé), la réponse passe en *échec IA* : l'animateur peut **relancer l'IA** ou **saisir une note** depuis *Cas (IA)*.
- Une note saisie par l'animateur remplace toujours celle de l'IA.
- Le prompt système et le référentiel de chaque cas sont dans `content/prompts/`. Tester une dizaine de réponses par cas avant l'événement et ajuster le référentiel si une note paraît injuste.

---

## Données personnelles

- Chaque participant ne voit que son propre résultat ; la liste de la séquence Rencontres n'affiche jamais le driver des autres.
- L'animateur voit les résultats individuels ; seules des statistiques anonymes (répartition des drivers, réponses anonymisées) sont destinées au client.
- La connexion se fait par email seul, sans code de vérification : choix assumé de simplicité pour un usage interne.
- Supprimer toutes les données après le séminaire (*Données → Supprimer → Tout*).

---

## Développement local et tests

```bash
cp config/config.example.php config/config.php   # puis renseigner une base locale
mysql -u root -e "CREATE DATABASE permis CHARACTER SET utf8mb4"
mysql -u root permis < database/schema.sql
php -S localhost:8000 -t public tools/dev-router.php
```

Données de test : `tools/demo_participants.csv` (200 faux participants, 20 équipes), à importer depuis l'espace animateur. Pour le régénérer : `php tools/generer_demo.php > tools/demo_participants.csv`.

Pour tester l'IA sans clé réelle, `ai.endpoint` peut pointer vers un serveur de test local (paramètre facultatif, absent en production).
