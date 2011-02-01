-- INSERT INTO `{PREFIX}config` (`key`, `label`, `value`, `protected`, `type`) VALUES ('TEST_RELEASE_1', 'Test release', 'test', '0', 'string');

-- table category
-- add column visibility
ALTER TABLE `{PREFIX}categories` ADD COLUMN `visibility` SMALLINT NULL DEFAULT 1  AFTER `sitemap_priority` ;
-- hidden
UPDATE `{PREFIX}categories` SET `visibility` = 5 WHERE `show_in_menu` = 0 AND `show_when_login_only` = 0;
-- visible for all
UPDATE `{PREFIX}categories` SET `visibility` = 1 WHERE (`show_in_menu` != `show_when_login_only`);
-- visible when user is login
UPDATE `{PREFIX}categories` SET `visibility` = 2 WHERE `show_in_menu` = 1 AND `show_when_login_only` = 1;
-- remove old colums
ALTER TABLE `{PREFIX}categories` DROP COLUMN `show_when_login_only` , DROP COLUMN `show_in_menu` ;

-- konfiguracni volby
INSERT INTO `{PREFIX}config` (`key`, `label`, `value`, `values`, `protected`, `type`) VALUES
('TOKENS_STORE', 'Kde se mají ukládat bezpečnostní tokeny', 'session', 'session;db;file', 0, 'list'),
('MAIN_TPL_VIEWS', 'Vzhledy hlavní šablony', null, null, 0, 'string');
