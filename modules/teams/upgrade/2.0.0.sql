ALTER TABLE `{PREFIX}teams`
CHANGE COLUMN `team_name` `team_name_cs` VARCHAR(100) NULL DEFAULT NULL  ,
ADD COLUMN `team_name_en` VARCHAR(100) NULL DEFAULT NULL  AFTER `team_name_cs` ,
ADD COLUMN `team_name_de` VARCHAR(100) NULL DEFAULT NULL  AFTER `team_name_en` ,
ADD COLUMN `team_name_sk` VARCHAR(100) NULL DEFAULT NULL  AFTER `team_name_de` ;

ALTER TABLE `{PREFIX}teams_persons`
CHANGE COLUMN `person_text` `person_text_cs` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL,
CHANGE COLUMN `person_text_clear` `person_text_clear_cs` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL,
ADD COLUMN `person_text_en` TEXT NULL DEFAULT NULL  AFTER `person_text_clear_cs`,
ADD COLUMN `person_text_clear_en` TEXT NULL DEFAULT NULL  AFTER `person_text_en`,
ADD COLUMN `person_text_de` TEXT NULL DEFAULT NULL  AFTER `person_text_clear_en`,
ADD COLUMN `person_text_clear_de` TEXT NULL DEFAULT NULL  AFTER `person_text_de`,
ADD COLUMN `person_text_sk` TEXT NULL DEFAULT NULL  AFTER `person_text_clear_de`,
ADD COLUMN `person_text_clear_sk` TEXT NULL DEFAULT NULL  AFTER `person_text_sk`,
DROP INDEX `text_clear`,
ADD FULLTEXT INDEX `text_clear_cs` (`person_text_clear_cs` ASC),
ADD FULLTEXT INDEX `text_clear_en` (`person_text_clear_en` ASC),
ADD FULLTEXT INDEX `text_clear_de` (`person_text_clear_de` ASC),
ADD FULLTEXT INDEX `text_clear_sk` (`person_text_clear_sk` ASC) ;
