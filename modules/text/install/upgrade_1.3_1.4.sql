ALTER TABLE `{PREFIX}texts` 
ADD COLUMN `data` TEXT NULL DEFAULT NULL,
ADD INDEX `subkey` (`id_item` ASC, `subkey` ASC) ;