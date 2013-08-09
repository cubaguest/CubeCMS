-- vlastnik kategorie
ALTER TABLE `{PREFIX}categories` ADD COLUMN `id_owner_user` SMALLINT NULL DEFAULT 0  AFTER `background` ;

