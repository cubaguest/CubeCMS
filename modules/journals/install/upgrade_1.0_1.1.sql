ALTER TABLE `{PREFIX}journals` 
ADD COLUMN `journal_viewed` VARCHAR(45) NULL AFTER `journal_text` , 
CHANGE COLUMN `number` `journal_number` SMALLINT(6) NOT NULL  , 
CHANGE COLUMN `year` `journal_year` SMALLINT(6) NOT NULL  , 
CHANGE COLUMN `file` `journal_file` VARCHAR(100) CHARACTER SET 'utf8' NULL DEFAULT NULL  , 
CHANGE COLUMN `text` `journal_text` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL;

ALTER TABLE `{PREFIX}journals_labels` 
CHANGE COLUMN `label` `journal_label` VARCHAR(500) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NOT NULL  , 
CHANGE COLUMN `page` `journal_page` SMALLINT(6) NOT NULL  ;
