CREATE  TABLE IF NOT EXISTS `{PREFIX}mails_groups` (
  `id_group` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NOT NULL ,
  `note` VARCHAR(400) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL ,
  PRIMARY KEY (`id_group`) ) ENGINE = MyISAM DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;

INSERT INTO `{PREFIX}mails_groups` (`name`, `note`) VALUES ('Základní', 'Základní skupina');

ALTER TABLE `{PREFIX}mails_addressbook` ADD COLUMN `id_group` SMALLINT NULL DEFAULT 1  AFTER `id_mail` ;
ALTER TABLE `{PREFIX}mails_addressbook` CHARACTER SET = utf8 , COLLATE = utf8_general_ci , CHANGE COLUMN `id_group` `id_group` SMALLINT(6) NOT NULL DEFAULT '1', ADD INDEX `GROUP` (`id_group` ASC) ;

