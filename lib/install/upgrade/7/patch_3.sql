/* create missing category columns */
DROP PROCEDURE IF EXISTS checkCatColumns;
/* procedure here, because if you can't create procedure, you must doing yourself */
CREATE PROCEDURE `checkCatColumns`()
BEGIN
   IF NOT EXISTS (SELECT NULL FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND COLUMN_NAME='label_sk' AND TABLE_NAME='{PREFIX}categories') THEN
      ALTER TABLE `{PREFIX}categories`
         ADD COLUMN `urlkey_sk` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_slovak_ci' NULL DEFAULT NULL  AFTER `alt_cs` ,
         ADD COLUMN `label_sk` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_slovak_ci' NULL DEFAULT NULL  AFTER `urlkey_sk` ,
         ADD COLUMN `alt_sk` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_slovak_ci' NULL DEFAULT NULL  AFTER `label_sk` ,
         ADD COLUMN `keywords_sk` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_slovak_ci' NULL DEFAULT NULL  AFTER `description_cs` ,
         ADD COLUMN `description_sk` VARCHAR(500) CHARACTER SET 'utf8' COLLATE 'utf8_slovak_ci' NULL DEFAULT NULL  AFTER `keywords_sk` ,
         CHANGE COLUMN `urlkey_cs` `urlkey_cs` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL,
         ADD FULLTEXT INDEX `label_sk` (`label_sk`), ADD FULLTEXT INDEX `description_sk` (`description_sk`) ;
   END IF;
END;

/*
DELIMITER $$
--
-- Procedury
--
CREATE DEFINER=`podaneruce`@`localhost` PROCEDURE `checkCatColumns`()
BEGIN
   IF NOT EXISTS (SELECT NULL FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND COLUMN_NAME='label_sk' AND TABLE_NAME='ies_categories') THEN
      ALTER TABLE `ies_categories`
         ADD COLUMN `urlkey_sk` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_slovak_ci' NULL DEFAULT NULL  AFTER `alt_cs` ,
         ADD COLUMN `label_sk` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_slovak_ci' NULL DEFAULT NULL  AFTER `urlkey_sk` ,
         ADD COLUMN `alt_sk` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_slovak_ci' NULL DEFAULT NULL  AFTER `label_sk` ,
         ADD COLUMN `keywords_sk` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_slovak_ci' NULL DEFAULT NULL  AFTER `description_cs` ,
         ADD COLUMN `description_sk` VARCHAR(500) CHARACTER SET 'utf8' COLLATE 'utf8_slovak_ci' NULL DEFAULT NULL  AFTER `keywords_sk` ,
         CHANGE COLUMN `urlkey_cs` `urlkey_cs` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL,
         ADD FULLTEXT INDEX `label_sk` (`label_sk`), ADD FULLTEXT INDEX `description_sk` (`description_sk`) ;
   END IF;
END$$
DELIMITER ;*/

CALL checkCatColumns();
/* DROP PROCEDURE IF EXISTS checkCatColumns;

/* callback function for change value */
ALTER TABLE `{PREFIX}config` ADD COLUMN `callback_func` VARCHAR(100) NULL DEFAULT NULL;

/* UPDATE_MAIN_SITE */
/* new table with global configurations */
CREATE TABLE IF NOT EXISTS `cubecms_global_config` (
  `id_config` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL,
  `label` varchar(1000) DEFAULT NULL,
  `value` text,
  `values` varchar(200) DEFAULT NULL,
  `protected` tinyint(1) NOT NULL DEFAULT '0',
  `type` enum('string','number','bool','list','listmulti','ser_object') NOT NULL DEFAULT 'string',
  `id_group` smallint(6) NOT NULL DEFAULT '0',
  `callback_func` varchar(100) NULL DEFAULT NULL,
  PRIMARY KEY (`id_config`),
  UNIQUE KEY `key` (`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/* movel global settings to global table */
INSERT INTO `cubecms_global_config` SELECT * FROM `{PREFIX}config` WHERE
`key` != 'USE_SUBDOMAIN_HTACCESS_WORKAROUND' AND
`key` != 'VERSION' AND
`key` != 'RELEASE';


/* tabulka s podweby */
CREATE TABLE IF NOT EXISTS `{PREFIX}sites` (
  `id_site` smallint(6) NOT NULL AUTO_INCREMENT,
  `domain` varchar(20) NOT NULL,
  `dir` varchar(20) NOT NULL,
  `table_prefix` varchar(20) NOT NULL,
  `is_main` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_site`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
/* main site */
INSERT INTO `{PREFIX}sites` (`domain`, `is_main`) VALUES ('www', 1);

CREATE TABLE IF NOT EXISTS `{PREFIX}sites_groups` (
  `id_site` smallint(6) NOT NULL,
  `id_group` int(11) NOT NULL,
  KEY `id_site` (`id_site`,`id_group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='tabulka propojení webů se skupinami adminů';


/* END_UPDATE */

/* delete old non global settings (same as insert in main) */
DELETE FROM `{PREFIX}config` WHERE
`key` != 'APP_LANGS' AND
`key` != 'CATEGORIES_STRUCTURE' AND
`key` != 'ADMIN_MENU_STRUCTURE' AND
`key` != 'WEB_NAME' AND
`key` != 'DEFAULT_APP_LANG' AND
`key` != 'USE_SUBDOMAIN_HTACCESS_WORKAROUND' AND
`key` != 'VERSION' AND
`key` != 'RELEASE';

/* new columns for users contain last login time and created time */
ALTER TABLE `{PREFIX}users` ADD COLUMN `created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP  AFTER `deleted` , ADD COLUMN `last_login` DATETIME NULL DEFAULT NULL  AFTER `created` ;



/* increase labels sizes */
ALTER TABLE `{PREFIX}categories` CHANGE `label_cs` `label_cs` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL ,
CHANGE `label_en` `label_en` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
CHANGE `label_de` `label_de` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
CHANGE `label_sk` `label_sk` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_slovak_ci NULL DEFAULT NULL;

/* add admin flags to groups and subsites */
ALTER TABLE `{PREFIX}groups`
ADD `admin` BOOLEAN NOT NULL DEFAULT '0';
-- ,ADD `admin_subsites` VARCHAR( 200 ) NULL DEFAULT NULL
UPDATE `{PREFIX}groups` SET `admin` = '1' WHERE `id_group` = 1;



/* UPDATE_SUB_SITE */
SELECT @domain:= (SELECT `value` FROM `{PREFIX}config` WHERE `key` = 'USE_SUBDOMAIN_HTACCESS_WORKAROUND');
/* new settings values */
INSERT INTO `{PREFIX}config` (`key`, `label`, `value`, `protected`, `type`, `id_group`) VALUES
('SUB_SITE_DOMAIN', 'Doména podstránek', @domain, false, 'string', 1),
('SUB_SITE_DIR', 'Adresár s podstránkami', @domain, false, 'string', 1),
('SUB_SITE_USE_HTACCESS', 'Jestli je pro subdomény použit htaccess', false, false, 'bool', 1),
('MAIN_SITE_TABLE_PREFIX', 'Prefix tabulek hlavních stránek (některé moduly využívají globální tabulky)', NULL, false, 'string', 1);
/* END_UPDATE */

/* remove old settings */
DELETE FROM `{PREFIX}config` WHERE `key` = 'USE_SUBDOMAIN_HTACCESS_WORKAROUND' OR `key` = 'SMTP_SERVER_ENCRYPT' OR `key` = 'USE_IMAGEMAGICK';

/* UPDATE_MAIN_SITE */
DELETE FROM `cubecms_global_config` WHERE `key` = 'USE_IMAGEMAGICK';
INSERT INTO `cubecms_global_config` (`key`, `label`, `value`, `protected`, `type`, `id_group`) VALUES
('FTP_HOST', 'Adresa ftp serveru, kde jsou stránky nahrány', 'localhost', false, 'string', 3),
('FTP_PORT', 'Port ftp serveru, kde jsou stránky nahrány', 22, false, 'number', 3),
('FTP_USER', 'Uživatel ftp serveru, kde jsou stránky nahrány', 'user', false, 'string', 3),
('FTP_PASSOWRD', 'Heslo uživatele ftp serveru, kde jsou stránky nahrány', NULL, false, 'string', 3),
('USE_IMAGEMAGICK', 'Jeslti se má používat knihovna Imagick pro práci s obrázky', false, false, 'bool', 3),
('SUB_SITE_DOMAIN', 'Doména podstránek', NULL, false, 'string', 1),
('SUB_SITE_DIR', 'Adresár s podstránkami', NULL, false, 'string', 1),
('SUB_SITE_USE_HTACCESS', 'Jestli je pro subdomény použit htaccess', false, false, 'bool', 1),
('MAIN_SITE_TABLE_PREFIX', 'Prefix tabulek hlavních stránek (některé moduly využívají globální tabulky)', NULL, false, 'string', 1),
('SMTP_SERVER_ENCRYPT', 'Šifrování spojení k SMTP serveru (tls, ssl)', NULL, false, 'string', 6);
/* END_UPDATE */

/* indexy pro urlkey */
ALTER TABLE `{PREFIX}categories`
-- DROP INDEX `urlkey_cs`,
-- DROP INDEX `urlkey_sk`,
-- DROP INDEX `urlkey_en`,
-- DROP INDEX `urlkey_de`,
ADD INDEX `urlkey_cs` (`urlkey_cs` ASC),
ADD INDEX `urlkey_sk` (`urlkey_sk` ASC),
ADD INDEX `urlkey_en` (`urlkey_en` ASC),
ADD INDEX `urlkey_de` (`urlkey_de` ASC) ;


