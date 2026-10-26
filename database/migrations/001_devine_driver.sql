-- À exécuter UNIQUEMENT si la base a été créée avec une version antérieure de schema.sql
-- (ajout du driver deviné dans la séquence « La conduite accompagnée »).
ALTER TABLE encounters
  ADD COLUMN guess CHAR(2) NULL AFTER answers,
  ADD COLUMN guess_ok TINYINT(1) NULL AFTER guess;
