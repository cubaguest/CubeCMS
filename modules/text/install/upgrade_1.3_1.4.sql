ALTER TABLE `{PREFIX}texts` 
ADD COLUMN `data` TEXT NULL DEFAULT NULL  AFTER `text_clear_sk` 
, ADD INDEX `subkey` (`id_item` ASC, `subkey` ASC) ;