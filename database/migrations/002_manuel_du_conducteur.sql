-- À exécuter UNE FOIS dans phpMyAdmin sur une base créée avant le 04/11/2026, AVANT de déployer la version
-- « Le manuel du conducteur » (qui remplace « La conduite accompagnée »).
-- Sans risque pour la version en ligne : ce script ne fait qu'ajouter deux tables et un paramètre.
-- L'ancienne table des rencontres (encounters) n'est plus utilisée ; elle peut être supprimée plus tard
-- dans phpMyAdmin (DROP TABLE encounters;), après le déploiement.

SET NAMES utf8mb4;

-- Séquence 4 : le manuel du conducteur (une fiche par équipe et par driver, modifiable jusqu'à validation)
CREATE TABLE IF NOT EXISTS manual_entries (
  team_id     INT UNSIGNED NOT NULL,
  driver      CHAR(2) NOT NULL,              -- sp, fp, sf, fe, dt
  motive      VARCHAR(400) NOT NULL DEFAULT '',
  crispe      VARCHAR(400) NOT NULL DEFAULT '',
  updated_by  INT UNSIGNED NULL,
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (team_id, driver),
  CONSTRAINT fk_manual_team FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
  CONSTRAINT fk_manual_by FOREIGN KEY (updated_by) REFERENCES participants(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Validation du manuel par l'équipe (verrouille la saisie)
CREATE TABLE IF NOT EXISTS manual_validations (
  team_id       INT UNSIGNED PRIMARY KEY,
  validated_by  INT UNSIGNED NULL,
  validated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_manval_team FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
  CONSTRAINT fk_manval_by FOREIGN KEY (validated_by) REFERENCES participants(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO settings (name, value) VALUES ('manuel_open', '0');
