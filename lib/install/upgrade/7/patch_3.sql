-- rozšíření uživalskyých informací o datum vytvoření účtu a datum posledního přihlášení
ALTER TABLE `{PREFIX}users` ADD COLUMN `created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP  AFTER `deleted` , ADD COLUMN `last_login` DATETIME NULL DEFAULT NULL  AFTER `created` ;

-- callback function for change value
ALTER TABLE `{PREFIX}config` ADD COLUMN `callback_func` VARCHAR(100) NULL DEFAULT NULL;

-- zvětšení velikosti labelů
ALTER TABLE `{PREFIX}categories` CHANGE `label_cs` `label_cs` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL ,
CHANGE `label_en` `label_en` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
CHANGE `label_de` `label_de` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
CHANGE `label_sk` `label_sk` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_slovak_ci NULL DEFAULT NULL;

-- změna administrace
ALTER TABLE `{PREFIX}groups` ADD `admin` BOOLEAN NOT NULL DEFAULT '0';
UPDATE `{PREFIX}groups` SET `admin` = '1' WHERE `id_group` = 1;

-- projít a vymazat všechny kategorie s adminu a přiřazených práv

-- nová tabulka s konfiguracemi - globální
CREATE TABLE IF NOT EXISTS `cubecms_global_config` (
  `id_config` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL,
  `label` varchar(1000) DEFAULT NULL,
  `value` text,
  `values` varchar(200) DEFAULT NULL,
  `protected` tinyint(1) NOT NULL DEFAULT '0',
  `type` enum('string','number','bool','list','listmulti','ser_object') NOT NULL DEFAULT 'string',
  `id_group` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_config`),
  UNIQUE KEY `key` (`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- nove hodnoty pro nastavení
INSERT INTO `{PREFIX}config` (`key`, `label`, `value`, `protected`, `type`, `id_group`) VALUES
('SUB_SITE_DOMAIN', 'Doména podstránek', NULL, false, 'string', 1),
('SUB_SITE_DIR', 'Adresár s podstránkami', NULL, false, 'string', 1),
('SUB_SITE_USE_HTACCESS', 'Jestli je pro subdomény použit htaccess', false, false, 'bool', 1),

('FTP_HOST', 'Adresa ftp serveru, kde jsou stránky nahrány', 'localhost', false, 'string', 3),
('FTP_PORT', 'Port ftp serveru, kde jsou stránky nahrány', 22, false, 'number', 3),
('FTP_USER', 'Uživatel ftp serveru, kde jsou stránky nahrány', 'user', false, 'string', 3),
('FTP_PASSOWRD', 'Heslo uživatele ftp serveru, kde jsou stránky nahrány', NULL, false, 'string', 3)

;

-- odstranění starých proměných
DELETE FROM `{PREFIX}config` WHERE `key` = 'USE_SUBDOMAIN_HTACCESS_WORKAROUND';
-- přesun do globalního configu
INSERT INTO `cubecms_global_config` SELECT * FROM `{PREFIX}config` WHERE
`key` = 'SESSION_NAME' OR `key` = 'PDF_PAGE_FORMAT' OR `key` = 'PDF_PAGE_ORIENTATION' OR
`key` = 'PDF_CREATOR' OR `key` = 'PDF_AUTHOR' OR `key` = 'PDF_HEADER_LOGO' OR
`key` = 'PDF_HEADER_LOGO_WIDTH' OR `key` = 'PDF_UNIT' OR `key` = 'PDF_MARGIN_HEADER' OR
`key` = 'PDF_MARGIN_FOOTER' OR `key` = 'PDF_MARGIN_TOP' OR `key` = 'PDF_MARGIN_BOTTOM' OR
`key` = 'PDF_MARGIN_LEFT' OR `key` = 'PDF_MARGIN_RIGHT' OR `key` = 'PDF_FONT_NAME_MAIN' OR
`key` = 'PDF_FONT_SIZE_MAIN' OR `key` = 'PDF_FONT_NAME_DATA' OR `key` = 'PDF_FONT_SIZE_DATA' OR
`key` = 'PDF_FONT_MONOSPACED' OR `key` = 'PDF_IMAGE_SCALE_RATIO' OR `key` = 'HEAD_MAGNIFICATION' OR
`key` = 'FEED_NUM' OR `key` = 'FEED_TTL' OR `key` = 'WEB_COPYRIGHT' OR
`key` = 'LOGIN_TIME' OR `key` = 'IMAGE_THUMB_W' OR `key` = 'IMAGE_THUMB_H' OR
`key` = 'SMTP_SERVER' OR `key` = 'SMTP_SERVER_PORT' OR `key` = 'SMTP_SERVER_USERNAME' OR
`key` = 'SMTP_SERVER_PASSWORD' OR `key` = 'SHORT_TEXT_TAGS' OR `key` = 'DEFAULT_PHOTO_W' OR
`key` = 'DEFAULT_PHOTO_H' OR `key` = 'STORE_ORIGINAl_FILES' OR `key` = 'JQUERY_THEME' OR
`key` = 'IMAGE_THUMB_CROP' OR `key` = 'PIROBOX_THEME' OR
`key` = 'FTP_HOST' OR `key` = 'FTP_PORT' OR `key` = 'FTP_USER' OR `key` = 'FTP_PASSOWRD';

DELETE FROM `{PREFIX}config` WHERE
`key` = 'SESSION_NAME' OR `key` = 'PDF_PAGE_FORMAT' OR `key` = 'PDF_PAGE_ORIENTATION' OR
`key` = 'PDF_CREATOR' OR `key` = 'PDF_AUTHOR' OR `key` = 'PDF_HEADER_LOGO' OR
`key` = 'PDF_HEADER_LOGO_WIDTH' OR `key` = 'PDF_UNIT' OR `key` = 'PDF_MARGIN_HEADER' OR
`key` = 'PDF_MARGIN_FOOTER' OR `key` = 'PDF_MARGIN_TOP' OR `key` = 'PDF_MARGIN_BOTTOM' OR
`key` = 'PDF_MARGIN_LEFT' OR `key` = 'PDF_MARGIN_RIGHT' OR `key` = 'PDF_FONT_NAME_MAIN' OR
`key` = 'PDF_FONT_SIZE_MAIN' OR `key` = 'PDF_FONT_NAME_DATA' OR `key` = 'PDF_FONT_SIZE_DATA' OR
`key` = 'PDF_FONT_MONOSPACED' OR `key` = 'PDF_IMAGE_SCALE_RATIO' OR `key` = 'HEAD_MAGNIFICATION' OR
`key` = 'FEED_NUM' OR `key` = 'FEED_TTL' OR `key` = 'WEB_COPYRIGHT' OR
`key` = 'LOGIN_TIME' OR `key` = 'IMAGE_THUMB_W' OR `key` = 'IMAGE_THUMB_H' OR
`key` = 'SMTP_SERVER' OR `key` = 'SMTP_SERVER_PORT' OR `key` = 'SMTP_SERVER_USERNAME' OR
`key` = 'SMTP_SERVER_PASSWORD' OR `key` = 'SHORT_TEXT_TAGS' OR `key` = 'DEFAULT_PHOTO_W' OR
`key` = 'DEFAULT_PHOTO_H' OR `key` = 'STORE_ORIGINAl_FILES' OR `key` = 'JQUERY_THEME' OR
`key` = 'IMAGE_THUMB_CROP' OR `key` = 'PIROBOX_THEME' OR
`key` = 'FTP_HOST' OR `key` = 'FTP_PORT' OR `key` = 'FTP_USER' OR `key` = 'FTP_PASSOWRD';


