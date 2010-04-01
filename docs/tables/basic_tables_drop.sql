-- phpMyAdmin SQL Dump
-- version 3.2.2.1deb1
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Neděle 28. března 2010, 12:51
-- Verze MySQL: 5.1.37
-- Verze PHP: 5.2.10-2ubuntu6.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databáze: `dev`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_categories`
--

DROP TABLE IF EXISTS `vypecky_categories`;
CREATE TABLE IF NOT EXISTS `vypecky_categories` (
  `id_category` smallint(3) NOT NULL AUTO_INCREMENT,
  `module` varchar(20) DEFAULT NULL,
  `data_dir` varchar(100) DEFAULT NULL,
  `urlkey_cs` varchar(100) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `label_cs` varchar(50) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `alt_cs` varchar(200) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `urlkey_en` varchar(100) DEFAULT NULL,
  `label_en` varchar(50) DEFAULT NULL,
  `alt_en` varchar(200) DEFAULT NULL,
  `urlkey_de` varchar(100) DEFAULT NULL,
  `label_de` varchar(50) DEFAULT NULL,
  `alt_de` varchar(200) DEFAULT NULL,
  `keywords_cs` varchar(200) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `description_cs` varchar(500) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `keywords_en` varchar(200) DEFAULT NULL,
  `description_en` varchar(500) DEFAULT NULL,
  `keywords_de` varchar(200) DEFAULT NULL,
  `description_de` varchar(500) DEFAULT NULL,
  `ser_params` varchar(1000) DEFAULT NULL,
  `params` varchar(200) DEFAULT NULL,
  `protected` tinyint(1) NOT NULL DEFAULT '0',
  `priority` smallint(2) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'je-li kategorie aktivní',
  `individual_panels` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Jesltli jsou panely pro kategorii individuální',
  `sitemap_changefreq` enum('always','hourly','daily','weekly','monthly','yearly','never') NOT NULL DEFAULT 'yearly',
  `sitemap_priority` float NOT NULL DEFAULT '0.1',
  `show_in_menu` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Má li se položka zobrazit v menu',
  `show_when_login_only` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Jstli má bát položka zobrazena po přihlášení',
  `changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `default_right` enum('---','r--','-w-','--c','rw-','-wc','r-c','rwc') NOT NULL DEFAULT 'r--',
  `feeds` tinyint(1) NOT NULL DEFAULT '0',
  `icon` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_category`),
  FULLTEXT KEY `label_cs` (`label_cs`),
  FULLTEXT KEY `label_en` (`label_en`),
  FULLTEXT KEY `label_de` (`label_de`),
  FULLTEXT KEY `description_cs` (`description_cs`),
  FULLTEXT KEY `description_en` (`description_en`),
  FULLTEXT KEY `description_de` (`description_de`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=112 ;

--
-- Vypisuji data pro tabulku `vypecky_categories`
--

INSERT INTO `vypecky_categories` (`id_category`, `module`, `data_dir`, `urlkey_cs`, `label_cs`, `alt_cs`, `urlkey_en`, `label_en`, `alt_en`, `urlkey_de`, `label_de`, `alt_de`, `keywords_cs`, `description_cs`, `keywords_en`, `description_en`, `keywords_de`, `description_de`, `ser_params`, `params`, `protected`, `priority`, `active`, `individual_panels`, `sitemap_changefreq`, `sitemap_priority`, `show_in_menu`, `show_when_login_only`, `changed`, `default_right`, `feeds`, `icon`) VALUES
(100, 'categories', NULL, 'struktura/kategorie', 'kategorie', NULL, 'administration/categories', 'Categories', NULL, '', '', '', NULL, NULL, NULL, NULL, '', '', NULL, '', 1, 0, 1, 0, 'never', 0, 1, 1, '2010-03-21 11:51:54', '---', 0, NULL),
(101, 'login', 'ucet', 'ucet', 'účet', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 1, '2010-03-21 11:44:07', 'r--', 0, NULL),
(103, 'panels', 'panely', 'struktura/panely', 'panely', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-03-21 11:52:28', '---', 0, NULL),
(102, 'empty', 'struktura', 'struktura', 'struktura', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-03-21 11:51:21', '---', 0, NULL),
(104, 'empty', 'uzivatele', 'uzivatele', 'uživatelé', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-03-21 11:53:13', '---', 0, NULL),
(105, 'users', 'uzivatele-a-skupiny', 'uzivatele/uzivatele-a-skupiny', 'uživatelé a skupiny', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-03-21 11:53:47', '---', 0, NULL),
(106, 'empty', 'nastaveni', 'nastaveni', 'nastavení', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-03-21 11:54:34', '---', 0, NULL),
(107, 'configuration', 'globalni-nastaveni-systemu', 'nastaveni/globalni-nastaveni-systemu', 'globální nastavení systému', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-03-21 11:55:27', '---', 0, NULL),
(108, 'empty', 'sprava', 'sprava', 'správa', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-03-21 11:56:06', '---', 0, NULL),
(109, 'services', 'sprava-systemu', 'sprava/sprava-systemu', 'správa systému', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-03-21 11:57:04', '---', 0, NULL),
(110, 'empty', 'informace', 'informace', 'informace', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-03-21 11:57:32', '---', 0, NULL),
(111, 'phpinfo', 'php-info', 'informace/php-info', 'PHP info', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-03-21 11:58:03', '---', 0, NULL),
(1, 'text', NULL, 'uvod', 'úvod', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 10, 1, 0, 'never', 0, 1, 0, '2010-03-24 15:56:18', 'r--', 0, NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_config`
--

DROP TABLE IF EXISTS `vypecky_config`;
CREATE TABLE IF NOT EXISTS `vypecky_config` (
  `id_config` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL,
  `label` varchar(1000) DEFAULT NULL,
  `value` text,
  `values` varchar(200) DEFAULT NULL,
  `protected` tinyint(1) NOT NULL DEFAULT '0',
  `type` enum('string','number','bool','list','listmulti','ser_object') NOT NULL DEFAULT 'string',
  PRIMARY KEY (`id_config`),
  UNIQUE KEY `key` (`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=61 ;

--
-- Vypisuji data pro tabulku `vypecky_config`
--

INSERT INTO `vypecky_config` (`id_config`, `key`, `label`, `value`, `values`, `protected`, `type`) VALUES
(1, 'DEFAULT_ID_GROUP', NULL, '2', NULL, 0, 'number'),
(2, 'DEFAULT_GROUP_NAME', NULL, 'guest', NULL, 0, 'string'),
(3, 'DEFAULT_USER_NAME', NULL, 'anonym', NULL, 0, 'string'),
(4, 'APP_LANGS', NULL, 'cs;en', 'cs;en;de;ru;sk', 0, 'listmulti'),
(5, 'DEFAULT_APP_LANG', NULL, 'cs', 'cs;en;de;ru;sk', 0, 'list'),
(6, 'IMAGES_DIR', NULL, 'images', NULL, 0, 'string'),
(7, 'IMAGES_LANGS_DIR', NULL, 'langs', NULL, 0, 'string'),
(8, 'DEBUG_LEVEL', NULL, '2', NULL, 0, 'number'),
(9, 'TEMPLATE_FACE', NULL, 'default', NULL, 0, 'string'),
(10, 'SITEMAP_PERIODE', NULL, 'weekly', NULL, 0, 'string'),
(11, 'SEARCH_RESULT_LENGHT', NULL, '300', NULL, 0, 'number'),
(12, 'SEARCH_HIGHLIGHT_TAG', NULL, 'strong', NULL, 0, 'string'),
(13, 'SESSION_NAME', NULL, 'vypecky_cookie', NULL, 0, 'string'),
(14, 'WEB_NAME', NULL, 'Vepřové Výpečky', NULL, 0, 'string'),
(15, 'CATEGORIES_STRUCTURE', NULL, 'O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:0;s:22:"\0Category_Structure\0id";i:0;s:28:"\0Category_Structure\0idParent";N;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:4:{i:0;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";i:1;s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:2;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:3:"114";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:3;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:3:"115";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:4;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:3:"116";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}', NULL, 1, 'ser_object'),
(58, 'USE_GLOBAL_ACCOUNTS_TB_PREFIXES', 'Prefixy tabulek pro které se má použít globální systém přihlašování', 'vypecky_', '', 0, 'string'),
(59, 'NAVIGATION_MENU_TABLE', 'Název tabulky s navigačním menu', 'vypecky_navigation_panel', NULL, 0, 'string'),
(60, 'SHARES_TABLE', 'Název tabulky s odkazy na sdílení (při global)', 'vypecky_shares', NULL, 0, 'string'),
(21, 'PAGE_TITLE_SEPARATOR', NULL, '|', NULL, 0, 'string'),
(16, 'NAVIGATION_SEPARATOR', NULL, '::', NULL, 0, 'string'),
(17, 'HEADLINE_SEPARATOR', NULL, ' - ', NULL, 0, 'string'),
(19, 'PANEL_TYPES', NULL, 'left;right;bottom', 'left;right;bottom;top', 0, 'listmulti'),
(18, 'USE_IMAGEMAGICK', NULL, 'false', NULL, 0, 'bool'),
(20, 'DATA_DIR', NULL, 'data', NULL, 0, 'string'),
(22, 'USE_GLOBAL_ACCOUNTS', 'Globální systém přihlašování', 'true', NULL, 0, 'bool'),
(23, 'GLOBAL_TABLES_PREFIX', 'Prefix globálních tabulek', 'global_', NULL, 0, 'string'),
(25, 'USE_SUBDOMAIN_HTACCESS_WORKAROUND', NULL, NULL, NULL, 0, 'string'),
(27, 'PDF_PAGE_FORMAT', 'Formát stránky pro pdf výstup', 'A4', NULL, 0, 'string'),
(28, 'PDF_PAGE_ORIENTATION', 'Natočení stránky pro pdf výstup (P=portrait, L=landscape)', 'P', 'P;L', 0, 'list'),
(29, 'PDF_CREATOR', 'Název pdf kreatoru', 'TCPDF', NULL, 0, 'string'),
(30, 'PDF_AUTHOR', 'Autor pdf', 'TCPDF', NULL, 0, 'string'),
(31, 'PDF_HEADER_LOGO', 'Název loga v hlavičce pdf', NULL, NULL, 0, 'string'),
(32, 'PDF_HEADER_LOGO_WIDTH', 'Šířka loga v hlavičce', NULL, NULL, 0, 'string'),
(33, 'PDF_UNIT', 'Jednotky použité u pdf (pt=point, mm=millimeter, cm=centimeter, in=inch)', 'mm', 'mm;pt;cm;in', 0, 'list'),
(34, 'PDF_MARGIN_HEADER', 'Odsazení hlavičky', '5', NULL, 0, 'string'),
(35, 'PDF_MARGIN_FOOTER', 'Odsazení zápatí', '10', NULL, 0, 'string'),
(36, 'PDF_MARGIN_TOP', 'Odsazení stránky z vrchu', '20', NULL, 0, 'string'),
(37, 'PDF_MARGIN_BOTTOM', 'Odsazení stránky od spodu', '25', NULL, 0, 'string'),
(38, 'PDF_MARGIN_LEFT', 'Odsazení z leva', '15', NULL, 0, 'string'),
(39, 'PDF_MARGIN_RIGHT', 'Odsazení z prava', '15', NULL, 0, 'string'),
(40, 'PDF_FONT_NAME_MAIN', 'Název hlavního fontu', 'arial', NULL, 0, 'string'),
(41, 'PDF_FONT_SIZE_MAIN', 'Velikost hlavního fontu', '12', NULL, 0, 'string'),
(42, 'PDF_FONT_NAME_DATA', 'Font pro data', 'arial', NULL, 0, 'string'),
(43, 'PDF_FONT_SIZE_DATA', 'Velikost fontu pro data', '6', NULL, 0, 'string'),
(44, 'PDF_FONT_MONOSPACED', 'Název pevného fontu', 'courier', NULL, 0, 'string'),
(45, 'PDF_IMAGE_SCALE_RATIO', 'Zvětšení obrázků ve výstupním pdf', '1', NULL, 0, 'string'),
(46, 'HEAD_MAGNIFICATION', 'zvětšovací poměr nadpisů', '1.1', NULL, 0, 'string'),
(51, 'WEB_DESCRIPTION', 'Popis stránek', 'Testovací stránky VVE enginu verze 6.0', NULL, 0, 'string'),
(50, 'FEED_NUM', 'Poček generovaných rss/atom kanálů', '10', NULL, 0, 'number'),
(52, 'WEB_MASTER_NAME', 'Jméno webmastera', 'Jakub Matas', NULL, 0, 'string'),
(53, 'WEB_MASTER_EMAIL', 'E-mail webmastera', 'jakubmatas@gmail.com', NULL, 0, 'string'),
(54, 'FEED_TTL', 'Počet minut kešování kanálu', '30', NULL, 0, 'number'),
(55, 'WEB_COPYRIGHT', 'Copyright poznámka k webu ({Y} - nahrazeno rokem)', 'Obsah toho webu je licencován podle ... Žádná s jeho částí nesmí být použita bez vědomí webmastera. Copyrigth {Y}', NULL, 0, 'string'),
(56, 'SEARCH_ARTICLE_REL_MULTIPLIER', 'Násobič pro relevanci nadpisu článku (1 - nekonečno)', '5', NULL, 0, 'number'),
(57, 'ADMIN_MENU_STRUCTURE', 'Administrační menu', 'O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:0;s:22:"\0Category_Structure\0id";i:0;s:28:"\0Category_Structure\0idParent";N;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:6:{i:1;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:3:"102";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:2:{i:0;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";i:100;s:28:"\0Category_Structure\0idParent";s:3:"102";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:1;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:3:"103";s:28:"\0Category_Structure\0idParent";s:3:"102";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}i:2;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:3:"104";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:1:{i:0;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:3:"105";s:28:"\0Category_Structure\0idParent";s:3:"104";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}i:3;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:3:"106";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:1:{i:0;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:3:"107";s:28:"\0Category_Structure\0idParent";s:3:"106";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}i:4;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:3:"108";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:1:{i:0;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:3:"109";s:28:"\0Category_Structure\0idParent";s:3:"108";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}i:5;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:3:"110";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:1:{i:0;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:3:"111";s:28:"\0Category_Structure\0idParent";s:3:"110";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}i:6;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:3:"101";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}', NULL, 1, 'ser_object');

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_groups`
--

DROP TABLE IF EXISTS `vypecky_groups`;
CREATE TABLE IF NOT EXISTS `vypecky_groups` (
  `id_group` smallint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID skupiny',
  `name` varchar(15) DEFAULT NULL COMMENT 'Nazev skupiny',
  `label` varchar(100) DEFAULT NULL,
  `used` tinyint(1) NOT NULL DEFAULT '1',
  `default_right` varchar(3) NOT NULL,
  PRIMARY KEY (`id_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Vypisuji data pro tabulku `vypecky_groups`
--

INSERT INTO `vypecky_groups` (`id_group`, `name`, `label`, `used`, `default_right`) VALUES
(1, 'admin', 'Administrátor', 1, 'rwc'),
(2, 'guest', 'Host', 1, 'r--'),
(3, 'user', 'Uživatel', 1, 'r--'),
(4, 'poweruser', 'uživatel s většími právy', 1, 'rw-'),
(5, 'test', 'Testovací skupina', 1, 'r--'),
(6, 'instruktor', '', 1, 'r--');

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_modules_instaled`
--

DROP TABLE IF EXISTS `vypecky_modules_instaled`;
CREATE TABLE IF NOT EXISTS `vypecky_modules_instaled` (
  `id_module` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET utf8 NOT NULL,
  `version` float NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_module`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Vypisuji data pro tabulku `vypecky_modules_instaled`
--


-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_panels`
--

DROP TABLE IF EXISTS `vypecky_panels`;
CREATE TABLE IF NOT EXISTS `vypecky_panels` (
  `id_panel` smallint(3) NOT NULL AUTO_INCREMENT,
  `id_cat` smallint(5) NOT NULL DEFAULT '0' COMMENT 'id kategorie panelu',
  `id_show_cat` smallint(5) unsigned DEFAULT '0' COMMENT 'id kategorie ve které se má daný panel zobrazit',
  `position` varchar(20) NOT NULL DEFAULT '' COMMENT 'Název boxu do kterého panel patří',
  `porder` smallint(5) NOT NULL DEFAULT '0' COMMENT 'Řazení panelu',
  `template` varchar(100) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `background` varchar(100) DEFAULT NULL,
  `pname_cs` varchar(100) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `pname_en` varchar(100) DEFAULT NULL,
  `pname_de` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_panel`),
  KEY `id_cat` (`id_cat`),
  KEY `id_show_cat` (`id_show_cat`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Vypisuji data pro tabulku `vypecky_panels`
--

INSERT INTO `vypecky_panels` (`id_panel`, `id_cat`, `id_show_cat`, `position`, `porder`, `template`, `icon`, `background`, `pname_cs`, `pname_en`, `pname_de`) VALUES
(1, 1, 0, 'left', 0, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_rights`
--

DROP TABLE IF EXISTS `vypecky_rights`;
CREATE TABLE IF NOT EXISTS `vypecky_rights` (
  `id_right` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_category` smallint(6) NOT NULL,
  `id_group` smallint(6) NOT NULL,
  `right` enum('---','r--','-w-','--c','rw-','-wc','r-c','rwc') NOT NULL DEFAULT 'r--',
  PRIMARY KEY (`id_right`),
  KEY `id_category` (`id_category`,`id_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=73 ;

--
-- Vypisuji data pro tabulku `vypecky_rights`
--

INSERT INTO `vypecky_rights` (`id_right`, `id_category`, `id_group`, `right`) VALUES
(1, 100, 1, 'rwc'),
(2, 101, 1, 'rwc'),
(3, 101, 2, 'r--'),
(4, 101, 3, 'r--'),
(5, 101, 4, 'r--'),
(6, 100, 2, '---'),
(7, 100, 3, '---'),
(8, 100, 4, '---'),
(9, 102, 1, 'rwc'),
(10, 102, 2, '---'),
(11, 102, 3, '---'),
(12, 102, 4, '---'),
(13, 103, 1, 'rwc'),
(14, 103, 2, '---'),
(15, 103, 3, '---'),
(16, 103, 4, '---'),
(17, 104, 1, 'rwc'),
(18, 104, 2, '---'),
(19, 104, 3, '---'),
(20, 104, 4, '---'),
(21, 105, 1, 'rwc'),
(22, 105, 2, '---'),
(23, 105, 3, '---'),
(24, 105, 4, '---'),
(25, 106, 1, 'rwc'),
(26, 106, 2, '---'),
(27, 106, 3, '---'),
(28, 106, 4, '---'),
(29, 107, 1, 'rwc'),
(30, 107, 2, '---'),
(31, 107, 3, '---'),
(32, 107, 4, '---'),
(33, 108, 1, 'rwc'),
(34, 108, 2, '---'),
(35, 108, 3, '---'),
(36, 108, 4, '---'),
(37, 109, 1, 'rwc'),
(38, 109, 2, '---'),
(39, 109, 3, '---'),
(40, 109, 4, '---'),
(41, 110, 1, 'rwc'),
(42, 110, 2, '---'),
(43, 110, 3, '---'),
(44, 110, 4, '---'),
(45, 111, 1, 'rwc'),
(46, 111, 2, '---'),
(47, 111, 3, '---'),
(48, 111, 4, '---'),
(49, 1, 1, 'rwc'),
(50, 1, 2, 'r--'),
(51, 1, 3, 'r--'),
(52, 1, 4, 'r--');

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_shares`
--

DROP TABLE IF EXISTS `vypecky_shares`;
CREATE TABLE IF NOT EXISTS `vypecky_shares` (
  `id_share` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `link` varchar(300) NOT NULL,
  `icon` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id_share`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Vypisuji data pro tabulku `vypecky_shares`
--

INSERT INTO `vypecky_shares` (`id_share`, `link`, `icon`, `name`) VALUES
(1, 'http://www.linkuj.cz/?id=linkuj&amp;url={URL}&amp;title={TITLE}', 'http://linkuj.cz/img/linkuj_icon.gif', 'linkuj.cz'),
(2, 'http://www.jagg.cz/bookmarks.php?action=add&amp;address={URL}&amp;title={TITLE}', 'http://www.jagg.cz/icon.png', 'jagg.cz'),
(3, 'http://vybrali.sme.sk/submit.php?url={URL}', 'http://zena.sme.sk/storm/imgs/toolbar/doasdf_c.gif', 'vybrali.sme.sk'),
(4, 'http://www.google.com/bookmarks/mark?op=edit&amp;bkmk={URL}&amp;title={TITLE}', 'icons_logos/google.gif', 'Google'),
(5, 'http://www.facebook.com/share.php?u={URL}&amp;title={TITLE}', 'icons_logos/facebook.gif', 'Facebook'),
(6, 'http://delicious.com/save?v=5&amp;noui&amp;jump=close&amp;url={URL}&amp;title={TITLE}', 'http://static.delicious.com/img/delicious.gif', 'delicious.com'),
(7, 'http://digg.com/submit?url={URL}&amp;title={TITLE}&amp;topic={TITLE}', 'http://digg.com/img/badges/16x16-digg-guy.gif', 'digg.com'),
(8, 'http://www.diigo.com/post?url={URL}&amp;title={TITLE}', 'http://www.diigo.com/images/ii_blue.gif', 'diigo.com'),
(9, 'http://pridat.eu/zalozku/?url={URL}&amp;title={TITLE}', 'http://i.pridat.eu/wwwpridateu.gif', 'pridat.eu'),
(10, 'http://www.bookmarky.cz/a.php?cmd=add&amp;url={URL}&amp;title={TITLE}', 'http://www.bookmarky.cz/bookmarky16x16.gif', 'bookmarky.cz');

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_texts`
--

DROP TABLE IF EXISTS `vypecky_texts`;
CREATE TABLE IF NOT EXISTS `vypecky_texts` (
  `id_text` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `id_item` smallint(5) unsigned NOT NULL,
  `subkey` varchar(30) NOT NULL DEFAULT 'NULL',
  `label_cs` varchar(200) DEFAULT NULL,
  `text_cs` mediumtext,
  `text_clear_cs` mediumtext,
  `text_panel_cs` varchar(1000) DEFAULT NULL,
  `changed` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `label_en` varchar(200) DEFAULT NULL,
  `text_en` mediumtext,
  `text_clear_en` mediumtext,
  `text_panel_en` varchar(1000) DEFAULT NULL,
  `label_de` varchar(200) DEFAULT NULL,
  `text_de` mediumtext,
  `text_clear_de` mediumtext,
  `text_panel_de` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id_text`),
  UNIQUE KEY `id_article` (`id_item`),
  FULLTEXT KEY `label_cs` (`label_cs`),
  FULLTEXT KEY `label_en` (`label_en`),
  FULLTEXT KEY `label_de` (`label_de`),
  FULLTEXT KEY `text_clear_de` (`text_clear_de`),
  FULLTEXT KEY `text_clear_en` (`text_clear_en`),
  FULLTEXT KEY `text_clear_cs` (`text_clear_cs`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Vypisuji data pro tabulku `vypecky_texts`
--


-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_users`
--

DROP TABLE IF EXISTS `vypecky_users`;
CREATE TABLE IF NOT EXISTS `vypecky_users` (
  `id_user` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID uzivatele',
  `username` varchar(20) NOT NULL COMMENT 'Uzivatelske jmeno',
  `password` varchar(100) DEFAULT NULL COMMENT 'Heslo',
  `id_group` smallint(3) unsigned DEFAULT '3',
  `name` varchar(30) NOT NULL,
  `surname` varchar(30) NOT NULL,
  `mail` varchar(50) NOT NULL,
  `note` varchar(500) DEFAULT NULL,
  `blocked` tinyint(1) NOT NULL DEFAULT '0',
  `foto_file` varchar(30) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_user`,`username`),
  KEY `id_group` (`id_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

--
-- Vypisuji data pro tabulku `vypecky_users`
--

INSERT INTO `vypecky_users` (`id_user`, `username`, `password`, `id_group`, `name`, `surname`, `mail`, `note`, `blocked`, `foto_file`, `deleted`) VALUES
(1, 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 1, 'Jakub', 'Matas', 'jakubmatas@gmail.com', 'administrátor', 0, NULL, 0),
(2, 'guest', '', 2, 'host', 'host', '', 'host systému', 0, NULL, 0),
(3, 'cuba', 'd033e22ae348aeb5660fc2140aec35850c4da997', 1, 'Jakub', 'Matas', 'jakubmatas@gmail.com', 'Normální uživatel', 0, 'cuba1.jpg', 0);
