-- DELIMITER $$

DROP PROCEDURE IF EXISTS upgrade_7;

CREATE PROCEDURE `upgrade_7` ()
BEGIN
	-- Release var NEW in 7.0
	INSERT INTO `{PREFIX}config` (`key`, `label`, `value`, `protected`, `type`) VALUES
	('RELEASE', 'verze release', 0, 1, 'number');

	-- 6.1 to 6.2
	IF EXISTS(SELECT * FROM `{PREFIX}config` WHERE `key` = 'VERSION' AND `value` = '6.1') THEN
	INSERT INTO `{PREFIX}config` (`key`, `label`, `value`, `protected`, `type`) VALUES
		('CM_SITEMAP_MAX_ITEMS_PAGE', 'Maximální počet položek v mapě stránek', '20', '0', 'number'),
		('CM_SITEMAP_MAX_ITEMS', 'Maximální počet položek v mapě stránek (pro vyhledávače)', '50', '0', 'number'),
		('CM_SITEMAP_CAT_ICON', 'Název ikony pro sitemap', 'sitemap.png', '0', 'string'),
		('CM_ERR_CAT_ICON', 'Název ikony pro chybovou stránku', 'error.png', '0', 'string'),
		('CM_RSS_CAT_ICON', 'Název ikony pro stránku s rss kanály', 'rsslist.png', '0', 'string');
	END IF;

	-- 6.2 to 6.3
	IF EXISTS (SELECT * FROM `{PREFIX}config` WHERE `key` = 'VERSION' AND value = '6.2') THEN
	INSERT INTO `{PREFIX}config` (`key`, `label`, `value`, `protected`, `type`) VALUES
		('LOGIN_TIME', 'Doba po které je uživatel automaticky odhlášen (s)', '3600', '0', 'number'),
		('IMAGE_THUMB_W', 'Výchozí šířka miniatury', '150', '0', 'number'),
		('IMAGE_THUMB_H', 'Výchozí výška miniatury', '150', '0', 'number'),
		('SMTP_SERVER', 'Adresa smtp serveru pro odesílání pošty', 'localhost', '0', 'string'),
		('SMTP_SERVER_PORT', 'Port smtp serveru pro odesílání pošty', '25', '0', 'number'),
		('SMTP_SERVER_USERNAME', 'Uživatelské jméno smtp serveru pro odesílání pošty', null, '0', 'string'),
		('SMTP_SERVER_PASSWORD', 'Uživatelské heslo smtp serveru pro odesílání pošty', null, '0', 'string'),
		('NOREPLAY_MAIL', 'Název schránky odesílané pošty', null, '0', 'string'),
		('SHORT_TEXT_TAGS', 'tagy, které jsou povoleny ve zkrácených výpisech', '<strong><a><em><span>', '0', 'string');
	END IF;

	-- 6.3 to 6.4
	-- ======= r1
	IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '{PREFIX}categories' AND COLUMN_NAME = 'background') THEN
		ALTER TABLE `{PREFIX}categories` ADD COLUMN `background` VARCHAR(100) NULL  AFTER `icon` ;
	END IF;

	-- není potřeba protože je odstraněno
	-- INSERT INTO `{PREFIX}config` (`key`, `label`, `value`, `protected`, `type`) VALUES ('REVISION', 'verze revize', 1, 1, 'number');

	-- ======= r2
	IF NOT EXISTS (SELECT * FROM `{PREFIX}config` WHERE `key` = 'DEFAULT_PHOTO_W') THEN
		INSERT INTO `{PREFIX}config` (`key`, `label`, `value`, `protected`, `type`) VALUES
		('DEFAULT_PHOTO_W', 'Výchozí šířka fotky', 800, 0, 'number'),
		('DEFAULT_PHOTO_H', 'Výchozí výška fotky', 600, 0, 'number');
	END IF;

	-- ======= r3
	IF NOT EXISTS (SELECT * FROM `{PREFIX}config` WHERE `key` = 'STORE_ORIGINAl_FILES') THEN
		INSERT INTO `{PREFIX}config` (`key`, `label`, `value`, `protected`, `type`) VALUES
			('STORE_ORIGINAl_FILES', 'Ukládání originálních souborů', true, 0, 'bool'),
			('JQUERY_THEME', 'Téma JQuery UI', 'base', 0, 'string');
	END IF;

	-- ======= r4
	IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '{PREFIX}users' AND COLUMN_NAME = 'password_restore') THEN
		ALTER TABLE `{PREFIX}users` ADD COLUMN `password_restore` VARCHAR(100) NULL DEFAULT NULL AFTER `password` ;
	END IF;

	-- ======= r5
	IF NOT EXISTS (SELECT * FROM `{PREFIX}config` WHERE `key` = 'IMAGE_THUMB_CROP') THEN
		INSERT INTO `{PREFIX}config` (`key`, `label`, `value`, `protected`, `type`) VALUES
		('IMAGE_THUMB_CROP', 'Ořezávat miniatury', true, 0, 'bool');
	END IF;

	-- tabulka pro sessions Add in 6.4
	CREATE TABLE IF NOT EXISTS `{PREFIX}sessions` (
		`session_key` VARCHAR(32) NOT NULL ,
		`value` BLOB NULL ,
		`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
		`updated` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
		`ip` VARCHAR(55) NULL DEFAULT NULL ,
		`id_user` INT NULL DEFAULT 0 ,
		PRIMARY KEY (`session_key`) ,
		UNIQUE INDEX `ssession_key_UNIQUE` (`session_key` ASC) )
	ENGINE = InnoDB
	DEFAULT CHARACTER SET = utf8
	COLLATE = utf8_general_ci;

	-- odstranění REVISION > nahrazeno RELEASE
	DELETE FROM `{PREFIX}config` WHERE `key` = 'REVISION';

END;

CALL upgrade_7();

DROP PROCEDURE IF EXISTS upgrade_7;