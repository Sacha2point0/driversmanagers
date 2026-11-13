-- À exécuter UNE FOIS dans phpMyAdmin sur une base créée avant le 13/11/2026, AVANT de déployer la version
-- « L'épreuve de conduite en 6 situations » (les manœuvres deviennent des situations rédigées, notées par l'IA).
-- Sans risque pour la version en ligne : ce script ne fait qu'ajouter des colonnes.
-- Après le déploiement, on peut faire le ménage dans phpMyAdmin (facultatif) :
--   DROP TABLE manoeuvre_submissions;
--   DELETE FROM settings WHERE name = 'manoeuvres_open';
--   ALTER TABLE case_submissions DROP COLUMN crit_feu_vert, DROP COLUMN crit_stop, DROP COLUMN crit_demande, DROP COLUMN crit_ton;
-- Pensez aussi à purger les épreuves de test (Admin → Données → « Épreuves seulement ») : les anciennes notes étaient sur 10.

SET NAMES utf8mb4;

ALTER TABLE case_submissions
  ADD COLUMN driver_guess    CHAR(2) NULL AFTER ai_raw,
  ADD COLUMN driver_ok       TINYINT(1) NOT NULL DEFAULT 0 AFTER driver_guess,
  ADD COLUMN crit_accueil    TINYINT UNSIGNED NULL AFTER driver_ok,
  ADD COLUMN crit_valorise   TINYINT UNSIGNED NULL AFTER crit_accueil,
  ADD COLUMN crit_permission TINYINT UNSIGNED NULL AFTER crit_valorise,
  ADD COLUMN crit_cadre      TINYINT UNSIGNED NULL AFTER crit_permission,
  ADD COLUMN crit_piege      TINYINT UNSIGNED NULL AFTER crit_cadre;
