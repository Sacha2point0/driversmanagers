-- Le permis de manager : schéma MySQL / MariaDB
-- Encodage : utf8mb4. À importer dans phpMyAdmin (Hostinger) ou en ligne de commande.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS teams (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(100) NOT NULL UNIQUE,
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
  ('rencontres_open', '0'),
  ('manoeuvres_open', '0'),
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

-- Séquence 4 : la conduite accompagnée (rencontres individuelles)
CREATE TABLE IF NOT EXISTS encounters (
  id                 INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  participant_id     INT UNSIGNED NOT NULL,
  met_participant_id INT UNSIGNED NOT NULL,
  answers            JSON NOT NULL,
  guess              CHAR(2) NULL,             -- driver principal deviné (sp, fp, sf, fe, dt)
  guess_ok           TINYINT(1) NULL,          -- 1 bien deviné, 0 raté, NULL si la personne n'a pas fait son bilan
  created_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_encounter (participant_id, met_participant_id),
  CONSTRAINT fk_enc_p FOREIGN KEY (participant_id) REFERENCES participants(id) ON DELETE CASCADE,
  CONSTRAINT fk_enc_m FOREIGN KEY (met_participant_id) REFERENCES participants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Séquence 5a : les manœuvres (classement de 4 réactions, une saisie par équipe et par manœuvre)
CREATE TABLE IF NOT EXISTS manoeuvre_submissions (
  team_id       INT UNSIGNED NOT NULL,
  manoeuvre_no  TINYINT UNSIGNED NOT NULL,
  ranking       CHAR(4) NOT NULL,
  score         TINYINT UNSIGNED NOT NULL,
  submitted_by  INT UNSIGNED NULL,
  submitted_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (team_id, manoeuvre_no),
  CONSTRAINT fk_man_team FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
  CONSTRAINT fk_man_by FOREIGN KEY (submitted_by) REFERENCES participants(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Séquence 5b : la conduite (cas rédigés, évalués par IA)
CREATE TABLE IF NOT EXISTS case_submissions (
  team_id         INT UNSIGNED NOT NULL,
  case_no         TINYINT UNSIGNED NOT NULL,
  answer          TEXT NOT NULL,
  ai_status       ENUM('pending','done','error') NOT NULL DEFAULT 'pending',
  ai_raw          TEXT NULL,
  crit_feu_vert   TINYINT UNSIGNED NULL,
  crit_stop       TINYINT UNSIGNED NULL,
  crit_demande    TINYINT UNSIGNED NULL,
  crit_ton        TINYINT UNSIGNED NULL,
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
