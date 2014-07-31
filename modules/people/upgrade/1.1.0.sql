-- remove deleted records
DELETE FROM `{PREFIX}people` WHERE `deleted` = 1;
-- update table
ALTER TABLE `{PREFIX}people` 
DROP COLUMN `deleted`,
CHANGE COLUMN `id_person` `id_person` INT NOT NULL AUTO_INCREMENT ,
CHANGE COLUMN `id_category` `id_category` INT NOT NULL ,
CHANGE COLUMN `name` `person_name` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NOT NULL ,
CHANGE COLUMN `surname` `person_surname` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NOT NULL ,
CHANGE COLUMN `degree` `person_degree` VARCHAR(10) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL ,
CHANGE COLUMN `degree_after` `person_degree_after` VARCHAR(10) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL ,
CHANGE COLUMN `text` `person_text` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL ,
CHANGE COLUMN `text_clear` `person_text_clear` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL ,
CHANGE COLUMN `order` `person_order` SMALLINT(6) NULL DEFAULT '0' ,
CHANGE COLUMN `image` `person_image` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL ,
ADD COLUMN `person_age` INT NULL DEFAULT NULL AFTER `person_image`,
ADD COLUMN `person_label` VARCHAR(100) NULL AFTER `person_age`;
