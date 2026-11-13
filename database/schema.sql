-- Le permis de manager : schéma MySQL / MariaDB
-- Encodage : utf8mb4. À importer dans phpMyAdmin (Hostinger) ou en ligne de commande.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS teams (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(100) NOT NULL UNIQUE,      -- nom officiel (import CSV)
  nickname    VARCHAR(40) NULL UNIQUE,           -- nom choisi par l'équipe dans son espace
  nickname_by INT UNSIGNED NULL,
  nickname_at DATETIME NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS participants (
  id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email          VARCHAR(190) NOT NULL UNIQUE,
  first_name     VARCHAR(100) NOT NULL,
  last_name      VARCHAR(100) NOT NULL,
  team_id        INT UNSIGNED NULL,
  last_login_at  DATETIME NULL,
  created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_participant_team FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Résultat du questionnaire des drivers (une passation par participant)
CREATE TABLE IF NOT EXISTS questionnaire_results (
  participant_id  INT UNSIGNED PRIMARY KEY,
  answers         JSON NOT NULL,
  score_sp        TINYINT UNSIGNED NOT NULL,  -- Sois parfait
  score_fp        TINYINT UNSIGNED NOT NULL,  -- Fais plaisir
  score_sf        TINYINT UNSIGNED NOT NULL,  -- Sois fort
  score_fe        TINYINT UNSIGNED NOT NULL,  -- Fais des efforts
  score_dt        TINYINT UNSIGNED NOT NULL,  -- Dépêche-toi
  dominant        CHAR(2) NOT NULL,
  completed_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_result_participant FOREIGN KEY (participant_id) REFERENCES participants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Paramètres de l'événement (ouverture des séquences…)
CREATE TABLE IF NOT EXISTS settings (
  name   VARCHAR(60) PRIMARY KEY,
  value  VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO settings (name, value) VALUES
  ('questionnaire_open', '1'),
  ('code_open', '0'),
  ('manuel_open', '0'),
  ('cas_open', '0');

-- Séquence 3 : l'examen du code (une saisie par équipe, verrouillée)
CREATE TABLE IF NOT EXISTS code_submissions (
  team_id       INT UNSIGNED PRIMARY KEY,
  placements    JSON NOT NULL,
  score         TINYINT UNSIGNED NOT NULL,
  submitted_by  INT UNSIGNED NULL,
  submitted_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_code_team FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
  CONSTRAINT fk_code_by FOREIGN KEY (submitted_by) REFERENCES participants(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- Séquence 5 : l'épreuve de conduite (6 situations rédigées, évaluées par IA, + bonus driver identifié)
CREATE TABLE IF NOT EXISTS case_submissions (
  team_id         INT UNSIGNED NOT NULL,
  case_no         TINYINT UNSIGNED NOT NULL,
  answer          TEXT NOT NULL,
  ai_status       ENUM('pending','done','error') NOT NULL DEFAULT 'pending',
  ai_raw          TEXT NULL,
  driver_guess    CHAR(2) NULL,                 -- driver identifié par l'équipe (sp, fp, sf, fe, dt)
  driver_ok       TINYINT(1) NOT NULL DEFAULT 0, -- 1 si c'est le bon : point bonus
  crit_accueil    TINYINT UNSIGNED NULL,
  crit_valorise   TINYINT UNSIGNED NULL,
  crit_permission TINYINT UNSIGNED NULL,
  crit_cadre      TINYINT UNSIGNED NULL,
  crit_piege      TINYINT UNSIGNED NULL,
  ai_score        TINYINT UNSIGNED NULL,
  score_override  TINYINT UNSIGNED NULL,
  feedback_ok     VARCHAR(400) NULL,
  feedback_more   VARCHAR(400) NULL,
  alert           VARCHAR(30) NULL,
  projected       TINYINT(1) NOT NULL DEFAULT 0,
  submitted_by    INT UNSIGNED NULL,
  submitted_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (team_id, case_no),
  CONSTRAINT fk_case_team FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
  CONSTRAINT fk_case_by FOREIGN KEY (submitted_by) REFERENCES participants(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
