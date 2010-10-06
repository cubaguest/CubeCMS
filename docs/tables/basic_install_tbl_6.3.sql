-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Pátek 24. září 2010, 14:07
-- Verze MySQL: 5.1.41
-- Verze PHP: 5.3.2-1ubuntu4.2


-- Základní struktura pro stránky.
-- INSTALACE:
-- 1. Nahradit "{PREFIX}" prefixem tabulek
-- 2. Spustit jako celý SQL dotaz

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}categories`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}categories` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=167 ;

--
-- Vypisuji data pro tabulku `{PREFIX}categories`
--

INSERT INTO `{PREFIX}categories` (`id_category`, `module`, `data_dir`, `urlkey_cs`, `label_cs`, `alt_cs`, `urlkey_en`, `label_en`, `alt_en`, `urlkey_de`, `label_de`, `alt_de`, `keywords_cs`, `description_cs`, `keywords_en`, `description_en`, `keywords_de`, `description_de`, `ser_params`, `params`, `protected`, `priority`, `active`, `individual_panels`, `sitemap_changefreq`, `sitemap_priority`, `show_in_menu`, `show_when_login_only`, `changed`, `default_right`, `feeds`, `icon`) VALUES
(1, 'categories', NULL, 'admin/struktura/kategorie', 'kategorie', NULL, 'administration/categories', 'Categories', NULL, '', '', '', NULL, NULL, NULL, NULL, '', '', NULL, '', 1, 0, 1, 0, 'never', 0, 1, 1, '2010-05-10 14:18:05', '---', 0, NULL),
(2, 'login', 'ucet', 'ucet', 'účet', NULL, 'account', 'account', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 1, '2010-05-10 14:12:36', 'r--', 0, NULL),
(3, 'empty', NULL, 'admin/struktura', 'struktura', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-05-10 14:17:47', '---', 0, NULL),
(4, 'panels', NULL, 'admin/struktura/panely', 'panely', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-05-10 14:17:55', '---', 0, NULL),
(5, 'empty', NULL, 'admin/uzivatele', 'uživatelé', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-05-10 14:17:17', '---', 0, NULL),
(6, 'users', NULL, 'admin/uzivatele/uzivatele-a-skupiny', 'uživatelé a skupiny', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-05-10 14:17:29', '---', 0, NULL),
(8, 'configuration', 'nastaveni', 'admin/system/settings', 'nastavení', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-09-24 14:04:31', '---', 0, NULL),
(9, 'empty', 'sprava', 'admin/sprava', 'obsah', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-09-24 14:00:47', '---', 0, NULL),
(10, 'services', 'system', 'admin/system/services', 'služby', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-09-24 14:03:59', '---', 0, NULL),
(11, 'empty', NULL, 'admin/informace', 'informace', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-05-10 14:22:07', '---', 0, NULL),
(12, 'phpinfo', 'php-info', 'admin/informace/php-info', 'PHP info', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-05-10 14:22:38', '---', 0, NULL),
(13, 'text', 'hlaseni-chyb', 'admin/informace/hlaseni-chyb', 'hlášení chyb', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-05-10 14:23:21', '---', 0, NULL),
(100, 'text', 'uvod', 'uvod', 'úvod', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'a:2:{s:18:"allow_private_zone";b:1;s:13:"allow_private";b:1;}', NULL, 0, 10, 1, 0, 'yearly', 1, 1, 0, '2010-09-24 14:05:39', 'r--', 0, NULL),
(125, 'upgrade', 'upgrade', 'admin/system/upgrade', 'upgrade', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-09-24 14:02:18', '---', 0, NULL),
(103, 'mails', 'maily', 'admin/sprava/maily', 'maily', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-08-17 12:05:07', '---', 0, NULL),
(105, 'templates', 'sablony', 'admin/sprava/sablony', 'šablony', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-05-28 20:40:40', '---', 0, NULL),
(166, 'empty', 'system', 'system', 'systém', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-09-24 13:59:18', '---', 0, NULL),
(137, 'search', 'hledani', 'hledani', 'hledání', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'always', 0, 1, 0, '2010-08-18 10:14:24', 'r--', 0, NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}comments`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}comments` (
  `id_comment` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_category` smallint(6) NOT NULL,
  `id_article` smallint(6) NOT NULL,
  `id_parent` smallint(6) DEFAULT '0',
  `nick` varchar(100) NOT NULL,
  `comment` varchar(500) NOT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '1',
  `censored` tinyint(1) NOT NULL DEFAULT '0',
  `corder` smallint(6) NOT NULL DEFAULT '1',
  `level` smallint(6) NOT NULL DEFAULT '0',
  `time_add` datetime NOT NULL,
  `ip_address` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id_comment`),
  KEY `id_category` (`id_category`,`id_article`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Vypisuji data pro tabulku `{PREFIX}comments`
--


-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}config`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}config` (
  `id_config` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL,
  `label` varchar(1000) DEFAULT NULL,
  `value` text,
  `values` varchar(200) DEFAULT NULL,
  `protected` tinyint(1) NOT NULL DEFAULT '0',
  `type` enum('string','number','bool','list','listmulti','ser_object') NOT NULL DEFAULT 'string',
  PRIMARY KEY (`id_config`),
  UNIQUE KEY `key` (`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=87 ;

--
-- Vypisuji data pro tabulku `{PREFIX}config`
--

INSERT INTO `{PREFIX}config` (`id_config`, `key`, `label`, `value`, `values`, `protected`, `type`) VALUES
(1, 'DEFAULT_ID_GROUP', NULL, '2', NULL, 0, 'number'),
(2, 'DEFAULT_GROUP_NAME', NULL, 'guest', NULL, 0, 'string'),
(3, 'DEFAULT_USER_NAME', NULL, 'anonym', NULL, 0, 'string'),
(4, 'APP_LANGS', NULL, 'cs', 'cs;en;de;ru;sk', 0, 'listmulti'),
(5, 'DEFAULT_APP_LANG', NULL, 'cs', 'cs;en;de;ru;sk', 0, 'list'),
(6, 'IMAGES_DIR', NULL, 'images', NULL, 0, 'string'),
(7, 'IMAGES_LANGS_DIR', NULL, 'langs', NULL, 0, 'string'),
(8, 'DEBUG_LEVEL', NULL, '2', NULL, 0, 'number'),
(9, 'TEMPLATE_FACE', NULL, 'default', NULL, 0, 'string'),
(10, 'SITEMAP_PERIODE', NULL, 'weekly', NULL, 0, 'string'),
(11, 'SEARCH_RESULT_LENGHT', NULL, '300', NULL, 0, 'number'),
(12, 'SEARCH_HIGHLIGHT_TAG', NULL, 'strong', NULL, 0, 'string'),
(13, 'SESSION_NAME', NULL, '{PREFIX}cookie', NULL, 0, 'string'),
(14, 'WEB_NAME', NULL, 'VVE Engine', NULL, 0, 'string'),
(61, 'CATEGORIES_STRUCTURE', NULL, 'O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:0;s:22:"\0Category_Structure\0id";i:0;s:28:"\0Category_Structure\0idParent";N;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:2:{i:5;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:3:"100";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:14;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:3:"137";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}', NULL, 1, 'ser_object'),
(58, 'USE_GLOBAL_ACCOUNTS_TB_PREFIXES', 'Prefixy tabulek pro které se má použít globální systém přihlašování', '{PREFIX}', '', 0, 'string'),
(59, 'NAVIGATION_MENU_TABLE', 'Název tabulky s navigačním menu', '{PREFIX}navigation_panel', NULL, 0, 'string'),
(60, 'SHARES_TABLE', 'Název tabulky s odkazy na sdílení (při global)', '{PREFIX}shares', NULL, 0, 'string'),
(21, 'PAGE_TITLE_SEPARATOR', NULL, '|', NULL, 0, 'string'),
(16, 'NAVIGATION_SEPARATOR', NULL, '::', NULL, 0, 'string'),
(17, 'HEADLINE_SEPARATOR', NULL, ' - ', NULL, 0, 'string'),
(19, 'PANEL_TYPES', NULL, 'left;right;bottom', 'left;right;bottom;top', 0, 'listmulti'),
(18, 'USE_IMAGEMAGICK', NULL, 'false', NULL, 0, 'bool'),
(20, 'DATA_DIR', NULL, 'data', NULL, 0, 'string'),
(22, 'USE_GLOBAL_ACCOUNTS', 'Globální systém přihlašování', 'false', NULL, 0, 'bool'),
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
(41, 'PDF_FONT_SIZE_MAIN', 'Velikost hlavního fontu', '10', NULL, 0, 'string'),
(42, 'PDF_FONT_NAME_DATA', 'Font pro data', 'arial', NULL, 0, 'string'),
(43, 'PDF_FONT_SIZE_DATA', 'Velikost fontu pro data', '6', NULL, 0, 'string'),
(44, 'PDF_FONT_MONOSPACED', 'Název pevného fontu', 'courier', NULL, 0, 'string'),
(45, 'PDF_IMAGE_SCALE_RATIO', 'Zvětšení obrázků ve výstupním pdf', '1', NULL, 0, 'string'),
(46, 'HEAD_MAGNIFICATION', 'zvětšovací poměr nadpisů', '1.1', NULL, 0, 'string'),
(51, 'WEB_DESCRIPTION', 'Popis stránek', 'Web Pages', NULL, 0, 'string'),
(50, 'FEED_NUM', 'Poček generovaných rss/atom kanálů', '10', NULL, 0, 'number'),
(52, 'WEB_MASTER_NAME', 'Jméno webmastera', 'Webmaster Name', NULL, 0, 'string'),
(53, 'WEB_MASTER_EMAIL', 'E-mail webmastera', 'webmaster@web.com', NULL, 0, 'string'),
(54, 'FEED_TTL', 'Počet minut kešování kanálu', '30', NULL, 0, 'number'),
(55, 'WEB_COPYRIGHT', 'Copyright poznámka k webu ({Y} - nahrazeno rokem)', 'Obsah toho webu je licencován podle ... Žádná s jeho částí nesmí být použita bez vědomí webmastera. Copyrigth {Y}', NULL, 0, 'string'),
(56, 'SEARCH_ARTICLE_REL_MULTIPLIER', 'Násobič pro relevanci nadpisu článku (1 - nekonečno)', '5', NULL, 0, 'number'),
(57, 'ADMIN_MENU_STRUCTURE', 'Administrační menu', 'O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:0;s:22:"\0Category_Structure\0id";i:0;s:28:"\0Category_Structure\0idParent";N;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:6:{i:1;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:1:"3";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:2:{i:0;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";i:1;s:28:"\0Category_Structure\0idParent";s:1:"3";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:1;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:1:"4";s:28:"\0Category_Structure\0idParent";s:1:"3";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}i:2;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:1:"5";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:1:{i:0;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:1:"6";s:28:"\0Category_Structure\0idParent";s:1:"5";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}i:4;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:1:"9";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:2:{i:1;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:3:"103";s:28:"\0Category_Structure\0idParent";s:1:"9";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:2;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:3:"105";s:28:"\0Category_Structure\0idParent";s:1:"9";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}i:5;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:3:"166";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:3:{i:0;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:3:"125";s:28:"\0Category_Structure\0idParent";s:3:"166";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:1;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:2:"10";s:28:"\0Category_Structure\0idParent";s:3:"166";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:2;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:1:"8";s:28:"\0Category_Structure\0idParent";s:3:"166";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}i:6;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:2:"11";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:2:{i:0;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:2:"12";s:28:"\0Category_Structure\0idParent";s:2:"11";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:1;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:2:"13";s:28:"\0Category_Structure\0idParent";s:2:"11";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}i:7;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:1:"2";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}', NULL, 1, 'ser_object'),
(62, 'MAIN_PAGE_TITLE', 'Nadpis hlavní stránky', 'Main Title Page', NULL, 0, 'string'),
(63, 'ALLOW_EXTERNAL_JS', 'Povolení externích JavaScript souborů', 'true', NULL, 0, 'bool'),
(68, 'VERSION', 'Verze jádra', '6.3', NULL, 1, 'string'),
(74, 'CM_SITEMAP_MAX_ITEMS', 'Maximální počet položek v mapě stránek (pro vyhledávače)', '50', NULL, 0, 'number'),
(73, 'CM_SITEMAP_MAX_ITEMS_PAGE', 'Maximální počet položek v mapě stránek', '20', NULL, 0, 'number'),
(75, 'CM_SITEMAP_CAT_ICON', 'Název ikony pro sitemap', 'sitemap.png', NULL, 0, 'string'),
(76, 'CM_ERR_CAT_ICON', 'Název ikony pro chybovou stránku', 'error.png', NULL, 0, 'string'),
(77, 'CM_RSS_CAT_ICON', 'Název ikony pro stránku s rss kanály', 'rsslist.png', NULL, 0, 'string'),
(78, 'LOGIN_TIME', 'Doba po které je uživatel automaticky odhlášen (s)', '3600', NULL, 0, 'number'),
(79, 'IMAGE_THUMB_W', 'Výchozí šířka miniatury', '150', NULL, 0, 'number'),
(80, 'IMAGE_THUMB_H', 'Výchozí výška miniatury', '150', NULL, 0, 'number'),
(81, 'SMTP_SERVER', 'Adresa smtp serveru pro odesílání pošty', 'localhost', NULL, 0, 'string'),
(82, 'SMTP_SERVER_PORT', 'Port smtp serveru pro odesílání pošty', NULL, NULL, 0, 'number'),
(83, 'SMTP_SERVER_USERNAME', 'Uživatelské jméno smtp serveru pro odesílání pošty', NULL, NULL, 0, 'string'),
(84, 'SMTP_SERVER_PASSWORD', 'Uživatelské heslo smtp serveru pro odesílání pošty', NULL, NULL, 0, 'string'),
(85, 'SHORT_TEXT_TAGS', 'tagy, které jsou povoleny ve zkrácených výpisech', '<strong><a><em><span>', NULL, 0, 'string'),
(86, 'NOREPLAY_MAIL', 'Název schránky odesílané pošty', 'noreplay@web.com', NULL, 0, 'string');

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}groups`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}groups` (
  `id_group` smallint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID skupiny',
  `name` varchar(15) DEFAULT NULL COMMENT 'Nazev skupiny',
  `label` varchar(100) DEFAULT NULL,
  `used` tinyint(1) NOT NULL DEFAULT '1',
  `default_right` varchar(3) NOT NULL,
  PRIMARY KEY (`id_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Vypisuji data pro tabulku `{PREFIX}groups`
--

INSERT INTO `{PREFIX}groups` (`id_group`, `name`, `label`, `used`, `default_right`) VALUES
(1, 'admin', 'Administrátor', 1, 'rwc'),
(2, 'guest', 'Host', 1, 'r--');

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}mails_addressbook`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}mails_addressbook` (
  `id_mail` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_group` smallint(6) NOT NULL DEFAULT '1',
  `name` varchar(30) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `surname` varchar(30) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `mail` varchar(100) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `note` varchar(400) DEFAULT NULL,
  PRIMARY KEY (`id_mail`),
  KEY `GROUP` (`id_group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Vypisuji data pro tabulku `{PREFIX}mails_addressbook`
--


-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}mails_groups`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}mails_groups` (
  `id_group` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `note` varchar(400) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Vypisuji data pro tabulku `{PREFIX}mails_groups`
--

INSERT INTO `{PREFIX}mails_groups` (`id_group`, `name`, `note`) VALUES
(1, 'Základní', 'Základní skupina');

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}mails_sends`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}mails_sends` (
  `id_mail` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_user` smallint(6) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `recipients` varchar(2000) CHARACTER SET utf8 DEFAULT NULL,
  `subject` varchar(500) CHARACTER SET utf8 DEFAULT NULL,
  `content` text CHARACTER SET utf8,
  `attachments` varchar(500) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id_mail`),
  KEY `id_user` (`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Vypisuji data pro tabulku `{PREFIX}mails_sends`
--


-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}modules_instaled`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}modules_instaled` (
  `id_module` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET utf8 NOT NULL,
  `version_major` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `version_minor` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_module`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Vypisuji data pro tabulku `{PREFIX}modules_instaled`
--

INSERT INTO `{PREFIX}modules_instaled` (`id_module`, `name`, `version_major`, `version_minor`) VALUES
(1, 'text', 1, 1),
(2, 'upgrade', 1, 1),
(3, 'mails', 2, 1),
(4, 'search', 1, 0),
(5, 'users', 2, 0),
(6, 'panels', 1, 0),
(7, 'empty', 1, 0),
(8, 'services', 1, 0),
(9, 'configuration', 1, 0),
(10, 'templates', 1, 1),
(11, 'phpinfo', 1, 0),
(12, 'categories', 1, 0),
(13, 'login', 1, 0);


--
-- Vypisuji data pro tabulku `{PREFIX}navigation_panel`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}navigation_panel` (
  `id_link` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(200) NOT NULL,
  `name` varchar(50) NOT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `type` enum('subdomain','project','group','partner') NOT NULL DEFAULT 'subdomain',
  `follow` tinyint(1) NOT NULL DEFAULT '1',
  `params` varchar(200) DEFAULT NULL,
  `ord` smallint(3) NOT NULL DEFAULT '100',
  `newwin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_link`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}panels`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}panels` (
  `id_panel` smallint(3) NOT NULL AUTO_INCREMENT,
  `id_cat` smallint(5) NOT NULL DEFAULT '0' COMMENT 'id kategorie panelu',
  `id_show_cat` smallint(5) unsigned DEFAULT '0' COMMENT 'id kategorie ve které se má daný panel zobrazit',
  `position` varchar(20) NOT NULL DEFAULT '' COMMENT 'Název boxu do kterého panel patří',
  `porder` smallint(5) NOT NULL DEFAULT '0' COMMENT 'Řazení panelu',
  `pparams` varchar(1000) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `background` varchar(100) DEFAULT NULL,
  `pname_cs` varchar(100) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `pname_en` varchar(100) DEFAULT NULL,
  `pname_de` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_panel`),
  KEY `id_cat` (`id_cat`),
  KEY `id_show_cat` (`id_show_cat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Vypisuji data pro tabulku `{PREFIX}panels`
--


-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}rights`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}rights` (
  `id_right` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_category` smallint(6) NOT NULL,
  `id_group` smallint(6) NOT NULL,
  `right` enum('---','r--','-w-','--c','rw-','-wc','r-c','rwc') NOT NULL DEFAULT 'r--',
  PRIMARY KEY (`id_right`),
  KEY `id_category` (`id_category`,`id_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37 ;

--
-- Vypisuji data pro tabulku `{PREFIX}rights`
--

INSERT INTO `{PREFIX}rights` (`id_right`, `id_category`, `id_group`, `right`) VALUES
(1, 1, 1, 'rwc'),
(2, 1, 2, '---'),
(3, 9, 2, '---'),
(4, 12, 2, '---'),
(5, 12, 1, 'rwc'),
(6, 9, 1, 'rwc'),
(7, 6, 2, '---'),
(8, 6, 1, 'rwc'),
(9, 11, 2, '---'),
(10, 8, 2, '---'),
(11, 5, 2, '---'),
(12, 5, 1, 'rwc'),
(13, 100, 2, 'r--'),
(14, 11, 1, 'rwc'),
(15, 8, 1, 'rwc'),
(16, 4, 2, '---'),
(17, 4, 1, 'rwc'),
(18, 100, 1, 'rwc'),
(19, 13, 2, '---'),
(20, 10, 2, '---'),
(21, 3, 2, '---'),
(22, 3, 1, 'rwc'),
(23, 13, 1, 'rwc'),
(24, 10, 1, 'rwc'),
(25, 2, 2, 'r--'),
(26, 2, 1, 'rwc'),
(27, 103, 1, 'rwc'),
(28, 103, 2, '---'),
(29, 105, 1, 'rwc'),
(30, 105, 2, '---'),
(31, 137, 2, 'r--'),
(32, 125, 1, 'rwc'),
(33, 125, 2, '---'),
(34, 137, 1, 'rwc'),
(35, 166, 2, '---'),
(36, 166, 1, 'rwc');

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}search_apis`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}search_apis` (
  `id_api` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `id_category` smallint(5) unsigned NOT NULL,
  `url` varchar(200) NOT NULL,
  `api` varchar(20) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id_api`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Vypisuji data pro tabulku `{PREFIX}search_apis`
--


-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}shares`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}shares` (
  `id_share` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `link` varchar(300) NOT NULL,
  `icon` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id_share`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Vypisuji data pro tabulku `{PREFIX}shares`
--

INSERT INTO `{PREFIX}shares` (`id_share`, `link`, `icon`, `name`) VALUES
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
-- Struktura tabulky `{PREFIX}templates`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}templates` (
  `id_template` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(400) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `content` text,
  `type` varchar(20) NOT NULL DEFAULT 'text',
  `time_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_template`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Vypisuji data pro tabulku `{PREFIX}templates`
--


-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}texts`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}texts` (
  `id_text` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `id_item` smallint(5) unsigned NOT NULL,
  `subkey` varchar(30) NOT NULL DEFAULT 'nokey',
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
  KEY `id_item` (`id_item`),
  FULLTEXT KEY `label_cs` (`label_cs`),
  FULLTEXT KEY `label_en` (`label_en`),
  FULLTEXT KEY `label_de` (`label_de`),
  FULLTEXT KEY `text_clear_de` (`text_clear_de`),
  FULLTEXT KEY `text_clear_en` (`text_clear_en`),
  FULLTEXT KEY `text_clear_cs` (`text_clear_cs`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Vypisuji data pro tabulku `{PREFIX}texts`
--


-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}texts_has_private_groups`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}texts_has_private_groups` (
  `id_group` smallint(6) NOT NULL,
  `id_text` smallint(6) NOT NULL,
  PRIMARY KEY (`id_group`,`id_text`),
  KEY `fk_tb_groups_id_group` (`id_group`),
  KEY `fk_tb_texts_id_text` (`id_text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `{PREFIX}texts_has_private_groups`
--


-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}texts_has_private_users`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}texts_has_private_users` (
  `id_user` smallint(6) NOT NULL,
  `id_text` smallint(6) NOT NULL,
  PRIMARY KEY (`id_user`,`id_text`),
  KEY `fk_tb_users_id_user` (`id_user`),
  KEY `fk_tb_texts_id_text` (`id_text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `{PREFIX}texts_has_private_users`
--


-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}users`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}users` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Vypisuji data pro tabulku `{PREFIX}users`
--

INSERT INTO `{PREFIX}users` (`id_user`, `username`, `password`, `id_group`, `name`, `surname`, `mail`, `note`, `blocked`, `foto_file`, `deleted`) VALUES
(2, 'guest', NULL, 2, 'test', 'tetasdhf', '', NULL, 0, NULL, 0),
(1, 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 1, 'admin', 'admin', '', NULL, 0, NULL, 0);
