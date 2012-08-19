ALTER TABLE `{PREFIX}mails_addressbook` 
CHANGE COLUMN `id_mail` `id_addressbook_mail` SMALLINT(6) NOT NULL AUTO_INCREMENT  , 
CHANGE COLUMN `id_group` `id_addressbook_group` SMALLINT(6) NOT NULL DEFAULT '1'  , 
CHANGE COLUMN `name` `addressbook_name` VARCHAR(30) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL  , 
CHANGE COLUMN `surname` `addressbook_surname` VARCHAR(30) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL  , 
CHANGE COLUMN `mail` `addressbook_mail` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NOT NULL  , 
CHANGE COLUMN `note` `addressbook_note` VARCHAR(400) NULL DEFAULT NULL  ,
ADD COLUMN `addressbook_valid` SMALLINT NULL DEFAULT 0  AFTER `addressbook_note`  ;

ALTER TABLE `{PREFIX}mails_groups` 
CHANGE COLUMN `id_group` `id_addressbook_group` INT(11) NOT NULL AUTO_INCREMENT  , 
CHANGE COLUMN `name` `addressbook_group_name` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NOT NULL  , 
CHANGE COLUMN `note` `addressbook_group_note` VARCHAR(400) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL  , 
RENAME TO  `{PREFIX}mails_addressbook_groups`  ;

