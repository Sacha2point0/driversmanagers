-- Nom d'équipe choisi par les participants dans leur espace (13/11/2026).
-- Non destructif : à exécuter dans phpMyAdmin AVANT de pousser le code.
ALTER TABLE teams
  ADD COLUMN nickname    VARCHAR(40) NULL AFTER name,
  ADD COLUMN nickname_by INT UNSIGNED NULL AFTER nickname,
  ADD COLUMN nickname_at DATETIME NULL AFTER nickname_by,
  ADD UNIQUE KEY uniq_team_nickname (nickname);
