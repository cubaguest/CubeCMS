/* UPDATE_MAIN_SITE */
-- new config values
INSERT INTO `cubecms_global_config` (`key`, `label`, `value`, `protected`, `type`, `id_group`) 
VALUES
('USE_CATEGORY_ALT_IN_TITLE', 'Použít alternativní název kategorie v titulku stránky', "false", false, 'bool', 3),
('FCB_APP_SECRET_KEY', 'Facebook App Secret Key', NULL, false, 'string', 11),
('FCB_PAGE_ID', 'ID stránky/skupiny na Facebooku', NULL, false, 'string', 11)
;
-- new col for hidden values for ex. keys or passwords
ALTER TABLE `cubecms_global_config` ADD COLUMN `hidden_value` TINYINT(1) NULL DEFAULT 0  AFTER `callback_func` ;

-- set hidden values for passwords
UPDATE `cubecms_global_config` SET `hidden_value`= 1 WHERE `key` = 'FTP_PASSOWRD';
UPDATE `cubecms_global_config` SET `hidden_value`= 1 WHERE `key` = 'SMTP_SERVER_PASSWORD';
UPDATE `cubecms_global_config` SET `hidden_value`= 1 WHERE `key` = 'FCB_APP_SECRET_KEY';

/* END_UPDATE */

ALTER TABLE `{PREFIX}_config` ADD COLUMN `hidden_value` TINYINT(1) NULL DEFAULT 0  AFTER `callback_func` ;

UPDATE `{PREFIX}_config` SET `hidden_value`= 1 WHERE `key` = 'FTP_PASSOWRD';
UPDATE `{PREFIX}_config` SET `hidden_value`= 1 WHERE `key` = 'SMTP_SERVER_PASSWORD';