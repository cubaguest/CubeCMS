--
-- Přidání tabulky se skupinami voleb pro hlavní nastavení
--
--
-- add congig_groups TABLE
CREATE TABLE IF NOT EXISTS `{PREFIX}config_groups` (
  `id_group` INT NOT NULL AUTO_INCREMENT ,
  `name_cs` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL ,
  `name_sk` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_slovak_ci' NULL DEFAULT NULL ,
  `name_en` VARCHAR(45) NULL DEFAULT NULL ,
  `name_de` VARCHAR(45) NULL DEFAULT NULL ,
  `desc_cs` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL ,
  `desc_sk` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_slovak_ci' NULL DEFAULT NULL ,
  `desc_en` VARCHAR(200) NULL DEFAULT NULL ,
  `desc_de` VARCHAR(200) NULL DEFAULT NULL ,
  PRIMARY KEY (`id_group`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

-- set id groups
SET @grp_protect = 1;
SET @grp_basic = 2;
SET @grp_adv = 3;
SET @grp_appear = 4;
SET @grp_exports = 5;
SET @grp_mails = 6;
SET @grp_images = 7;
SET @grp_langs = 8;
SET @grp_search = 9;

-- insert groups
INSERT INTO `{PREFIX}config_groups` (`id_group`, `name_cs`, `name_sk`, `name_en`, `name_de`, `desc_cs`, `desc_sk`, `desc_en`) VALUES
(@grp_basic, 'Základní nastavení', 'Základné nastavenie', 'Basic settings', 'Grundeinstellungen', 'Základní nastavení aplikace', 'Základné nastavenia aplikácie', 'Basic settings'),
(@grp_adv, 'Pokročilá nastavení', 'Rozšírené nastavenia', 'Advanced settings', 'Erweiterte Einstellungen', 'Nastavení chování jádra (přihlášení, subdomény, atd.)', 'Nastavenie správania jadra (prihlásení, subdomény, atď)', 'Adjustment of the Kernel (login, subdomains, etc.)'),
(@grp_appear, 'Vzhled', 'Vzhľad', 'Appearance', 'Aussehen', 'Nastavení vzhledu stránek', 'Nastavenie vzhľadu stránok', 'Setting up of site'),
(@grp_mails, 'E-maily', 'E-maily', 'E-mails', 'E-Mails', 'Nastavení e-mailových služeb', 'Nastavenie e-mailových služieb', 'Setting up e-mail service'),
(@grp_images, 'Obrázky', 'Obrázky', 'Images', 'Bilder', 'Nastavení obrázků (velikost)', 'Nastavenie obrázkov (veľkosť)', 'Picture settings (size)'),
(@grp_langs, 'Lokalizace a jazyky', 'Lokalizácia a jazyky', 'Localization and languages', 'Ortsbestimmung und Sprachen', 'Nastavení jazyků prostředí a lokalizace aplikace', 'Nastavenie jazykov prostredia a lokalizácia aplikácie', 'The language environment and positioning applications'),
(@grp_search, 'Hledání', 'Hľadanie', 'Search', 'Suche', 'Nastavení výsledků hledání', 'Nastavenie výsledkov hľadania', 'Search Settings'),
(@grp_exports, 'Exporty', 'Exporty', 'Exports', 'Exporte', 'Nastavení exportů (rss, pdf, ...)', 'Nastavenie exportov (rss, pdf, ...)', 'Export Settings (RSS, PDF, ...)'),
(@grp_protect, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- add column to config table
ALTER TABLE `{PREFIX}config` ADD COLUMN `id_group` SMALLINT NOT NULL DEFAULT 0  AFTER `type` ;

-- basic
UPDATE `{PREFIX}config` SET `id_group` = @grp_basic WHERE `key` = 'DEFAULT_ID_GROUP'
OR `key` = 'WEB_NAME' OR `key` = 'WEB_DESCRIPTION' OR `key` = 'WEB_MASTER_NAME'
OR `key` = 'WEB_COPYRIGHT' OR `key` = 'MAIN_PAGE_TITLE';

-- appearance
UPDATE `{PREFIX}config` SET `id_group` = @grp_appear WHERE `key` = 'TEMPLATE_FACE' OR `key` = 'PANEL_TYPES'
OR `key` = 'PAGE_TITLE_SEPARATOR' OR `key` = 'NAVIGATION_SEPARATOR' OR `key` = 'HEADLINE_SEPARATOR' OR `key` = 'CM_SITEMAP_CAT_ICON'
OR `key` = 'CM_ERR_CAT_ICON' OR `key` = 'CM_RSS_CAT_ICON' OR `key` = 'CM_SITEMAP_MAX_ITEMS_PAGE' OR `key` = 'JQUERY_THEME' OR `key` = 'MAIN_TPL_VIEWS'
;
-- advanced
UPDATE `{PREFIX}config` SET `id_group` = @grp_adv WHERE `key` = 'DEFAULT_ID_GROUP'
OR `key` = 'DEFAULT_GROUP_NAME' OR `key` = 'DEFAULT_USER_NAME' OR `key` = 'DEFAULT_GROUP_NAME'
OR `key` = 'IMAGES_LANGS_DIR' OR `key` = 'IMAGES_DIR' OR `key` = 'DEBUG_LEVEL' OR `key` = 'SESSION_NAME' OR `key` = 'USE_GLOBAL_ACCOUNTS_TB_PREFIXES'
OR `key` = 'NAVIGATION_MENU_TABLE' OR `key` = 'SHARES_TABLE' OR `key` = 'DATA_DIR' OR `key` = 'USE_GLOBAL_ACCOUNTS'
 OR `key` = 'GLOBAL_TABLES_PREFIX' OR `key` = 'USE_SUBDOMAIN_HTACCESS_WORKAROUND' OR `key` = 'ALLOW_EXTERNAL_JS' OR `key` = 'LOGIN_TIME' OR `key` = 'SHORT_TEXT_TAGS'
 OR `key` = 'TOKENS_STORE';

-- exports
UPDATE `{PREFIX}config` SET `id_group` = @grp_exports WHERE `key` = 'SITEMAP_PERIODE'
OR `key` = 'FEED_NUM' OR `key` = 'FEED_TTL' OR `key` = 'CM_SITEMAP_MAX_ITEMS'
OR `key` = 'PDF_PAGE_FORMAT' OR `key` = 'PDF_PAGE_ORIENTATION' OR `key` = 'PDF_CREATOR' OR `key` = 'PDF_AUTHOR'
OR `key` = 'PDF_HEADER_LOGO' OR `key` = 'PDF_HEADER_LOGO_WIDTH' OR `key` = 'PDF_UNIT'
OR `key` = 'PDF_MARGIN_HEADER' OR `key` = 'PDF_MARGIN_FOOTER' OR `key` = 'PDF_MARGIN_TOP' OR `key` = 'PDF_MARGIN_BOTTOM' OR `key` = 'PDF_MARGIN_LEFT' OR `key` = 'PDF_MARGIN_RIGHT'
OR `key` = 'PDF_FONT_NAME_MAIN' OR `key` = 'PDF_FONT_SIZE_MAIN' OR `key` = 'PDF_FONT_NAME_DATA' OR `key` = 'PDF_FONT_SIZE_DATA' OR `key` = 'PDF_FONT_MONOSPACED' OR `key` = 'PDF_IMAGE_SCALE_RATIO'
OR `key` = 'HEAD_MAGNIFICATION';

-- mails
UPDATE `{PREFIX}config` SET `id_group` = @grp_mails WHERE `key` = 'WEB_MASTER_EMAIL'
OR `key` = 'SMTP_SERVER' OR `key` = 'SMTP_SERVER_PORT' OR `key` = 'SMTP_SERVER_USERNAME' OR `key` = 'SMTP_SERVER_PASSWORD'
OR `key` = 'NOREPLAY_MAIL';

-- images
UPDATE `{PREFIX}config` SET `id_group` = @grp_images WHERE `key` = 'USE_IMAGEMAGICK'
OR `key` = 'IMAGE_THUMB_W' OR `key` = 'IMAGE_THUMB_H' OR `key` = 'DEFAULT_PHOTO_W' OR `key` = 'DEFAULT_PHOTO_H' OR `key` = 'STORE_ORIGINAl_FILES' OR `key` = 'IMAGE_THUMB_CROP' OR `key` = '' OR `key` = ''
OR `key` = '' OR `key` = '' OR `key` = '';

-- langs
UPDATE `{PREFIX}config` SET `id_group` = @grp_langs WHERE `key` = 'APP_LANGS' OR `key` = 'DEFAULT_APP_LANG';

-- search
UPDATE `{PREFIX}config` SET `id_group` = @grp_search WHERE `key` = 'SEARCH_RESULT_LENGHT' OR `key` = 'SEARCH_HIGHLIGHT_TAG'
OR `key` = 'SEARCH_ARTICLE_REL_MULTIPLIER';

-- protected
UPDATE `{PREFIX}config` SET `id_group` = @grp_protect WHERE `key` = 'CATEGORIES_STRUCTURE' OR `key` = 'VERSION'
OR `key` = 'RELEASE' OR `key` = 'ADMIN_MENU_STRUCTURE';

-- THEME pro pirobox
INSERT INTO `{PREFIX}config` (`key`, `id_group` , `label`, `value`,`values`, `protected`, `type`) VALUES ('PIROBOX_THEME', @grp_appear, 'Téma JsPluginu Pirobox', 'white', 'black;blackwhite;shadow;white;whiteblack' , '0', 'list');

-- Doplnení nekterých popisku do konfigu
UPDATE `{PREFIX}config` SET `label`='Jestli se má na práci s obrázky použít knihovna ImageMagick (Musí být instalována na serveru)' WHERE `key`='USE_IMAGEMAGICK';
UPDATE `{PREFIX}config` SET `label`='Jaké druhy panelů jsou zapnuty a povoleny (musí je implementovat šablona)' WHERE `key`='PANEL_TYPES';
UPDATE `{PREFIX}config` SET `label`='Oddělovač slov v nadpisu H1' WHERE `key`='HEADLINE_SEPARATOR';
UPDATE `{PREFIX}config` SET `label`='Oddělovač položek v navigaci mezi kategoriemi' WHERE `key`='NAVIGATION_SEPARATOR';
UPDATE `{PREFIX}config` SET `label`='Oddělovač položek v nadpisu stránky' WHERE `key`='PAGE_TITLE_SEPARATOR';
UPDATE `{PREFIX}config` SET `label`='Název stránek' WHERE `key`='WEB_NAME';
UPDATE `{PREFIX}config` SET `label`='Název cookies s id session, která se ukládá u klienta' WHERE `key`='SESSION_NAME';
UPDATE `{PREFIX}config` SET `label`='Název tagu, který se užívá pro zvýraznění slova ve výsledcích hledání' WHERE `key`='SEARCH_HIGHLIGHT_TAG';
UPDATE `{PREFIX}config` SET `label`='Délka řetězce s výsledkem hledání' WHERE `key`='SEARCH_RESULT_LENGHT';
UPDATE `{PREFIX}config` SET `label`='Výchozí položka pro změnu mapy stránek pro vyhledávače' WHERE `key`='SITEMAP_PERIODE';
UPDATE `{PREFIX}config` SET `label`='Název vzhledu stránek' WHERE `key`='TEMPLATE_FACE';
UPDATE `{PREFIX}config` SET `label`='Režim ladění stránek (0 pro vypnutí)' WHERE `key`='DEBUG_LEVEL';
UPDATE `{PREFIX}config` SET `label`='Výchozí jazyk aplikace. Tento jazyk je potom u většiny položek povinný.' WHERE `key`='DEFAULT_APP_LANG';
UPDATE `{PREFIX}config` SET `label`='Všechny vybrané jazyky aplikace' WHERE `key`='APP_LANGS';