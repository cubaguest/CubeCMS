-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Pondělí 30. srpna 2010, 15:44
-- Verze MySQL: 5.1.41
-- Verze PHP: 5.3.2-1ubuntu4.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Databáze: `dev`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_categories`
--

CREATE TABLE IF NOT EXISTS `zus_categories` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=141 ;

--
-- Vypisuji data pro tabulku `zus_categories`
--

INSERT INTO `zus_categories` (`id_category`, `module`, `data_dir`, `urlkey_cs`, `label_cs`, `alt_cs`, `urlkey_en`, `label_en`, `alt_en`, `urlkey_de`, `label_de`, `alt_de`, `keywords_cs`, `description_cs`, `keywords_en`, `description_en`, `keywords_de`, `description_de`, `ser_params`, `params`, `protected`, `priority`, `active`, `individual_panels`, `sitemap_changefreq`, `sitemap_priority`, `show_in_menu`, `show_when_login_only`, `changed`, `default_right`, `feeds`, `icon`) VALUES
(1, 'categories', NULL, 'admin/struktura/kategorie', 'kategorie', NULL, 'administration/categories', 'Categories', NULL, '', '', '', NULL, NULL, NULL, NULL, '', '', NULL, '', 1, 0, 1, 0, 'never', 0, 1, 1, '2010-05-10 14:18:05', '---', 0, NULL),
(2, 'login', 'ucet', 'ucet', 'účet', NULL, 'account', 'account', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 1, '2010-05-10 14:12:36', 'r--', 0, NULL),
(3, 'empty', NULL, 'admin/struktura', 'struktura', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-05-10 14:17:47', '---', 0, NULL),
(4, 'panels', NULL, 'admin/struktura/panely', 'panely', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-05-10 14:17:55', '---', 0, NULL),
(5, 'empty', NULL, 'admin/uzivatele', 'uživatelé', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-05-10 14:17:17', '---', 0, NULL),
(6, 'users', NULL, 'admin/uzivatele/uzivatele-a-skupiny', 'uživatelé a skupiny', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-05-10 14:17:29', '---', 0, NULL),
(7, 'empty', NULL, 'admin/nastaveni', 'nastavení', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-05-10 14:18:51', '---', 0, NULL),
(8, 'configuration', 'nastaveni-systemu', 'admin/nastaveni/nastaveni-systemu', 'nastavení systému', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-05-10 14:21:27', '---', 0, NULL),
(9, 'empty', NULL, 'admin/sprava', 'správa', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-05-10 14:20:44', '---', 0, NULL),
(10, 'services', 'system', 'admin/sprava/system', 'systém', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-05-10 14:21:15', '---', 0, NULL),
(11, 'empty', NULL, 'admin/informace', 'informace', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-05-10 14:22:07', '---', 0, NULL),
(12, 'phpinfo', 'php-info', 'admin/informace/php-info', 'PHP info', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-05-10 14:22:38', '---', 0, NULL),
(13, 'text', 'hlaseni-chyb', 'admin/informace/hlaseni-chyb', 'hlášení chyb', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-05-10 14:23:21', '---', 0, NULL),
(100, 'text', 'o-nas', 'o-nas', 'o nás', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'yearly', 0.7, 1, 0, '2010-06-29 14:23:17', 'r--', 0, NULL),
(125, 'upgrade', NULL, 'admin/sprava/upgrade', 'upgrade', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-06-25 16:39:11', '---', 0, NULL),
(115, 'empty', 'vzdelavani', 'vzdelavani', 'vzdělávání', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-06-23 10:39:55', 'r--', 0, NULL),
(102, 'news', NULL, 'aktuality', 'aktuality', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'always', 0, 1, 0, '2010-08-27 16:32:43', 'r--', 1, NULL),
(103, 'mails', 'maily', 'admin/sprava/maily', 'maily', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-08-17 12:05:07', '---', 0, NULL),
(135, 'photogalery', 'fotky', 'o-nas/fotky', 'fotky', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'always', 0, 1, 0, '2010-08-17 12:52:15', 'r--', 0, NULL),
(105, 'templates', 'sablony', 'admin/sprava/sablony', 'šablony', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-05-28 20:40:40', '---', 0, NULL),
(110, 'lecturers', 'nasi-lektori', 'o-nas/nasi-lektori', 'naši lektoři', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'a:1:{s:7:"cropimg";b:1;}', NULL, 0, 0, 1, 0, 'monthly', 0.3, 1, 0, '2010-06-30 22:45:53', 'r--', 0, NULL),
(114, 'courses', NULL, 'vzdelavani/aktualni-nabidka-kurzu', 'aktuální nabídka kurzů', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'a:2:{i:1;s:1:"1";i:2;a:2:{i:0;s:1:"1";i:1;s:2:"28";}}', NULL, 0, 0, 1, 0, 'monthly', 1, 1, 0, '2010-08-27 10:03:23', 'r--', 1, NULL),
(116, 'text', 'zakladni-informace', 'vzdelavani/zakladni-informace', 'základní informace', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'monthly', 0.6, 1, 0, '2010-06-23 10:41:29', 'r--', 0, NULL),
(117, 'text', 'kurzy-na-klic', 'vzdelavani/kurzy-na-klic', 'kurzy na klíč', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'yearly', 0.8, 1, 0, '2010-06-23 10:43:26', 'r--', 0, NULL),
(118, 'articles', 'clanky', 'clanky', 'články', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'a:1:{s:6:"scroll";s:2:"10";}', NULL, 0, 0, 1, 0, 'monthly', 0.5, 1, 0, '2010-08-17 12:51:46', 'r--', 1, NULL),
(137, 'search', 'hledani', 'hledani', 'hledání', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'always', 0, 1, 0, '2010-08-18 10:14:24', 'r--', 0, NULL),
(138, 'actionswgal', NULL, 'testy/akce-plus-fotky', 'akce plus fotky', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'always', 0, 1, 0, '2010-08-24 12:20:45', 'r--', 1, NULL),
(139, 'articles', 'clanky-z-akci', 'testy/akce/clanky-z-akci', 'články z akcí', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'always', 0, 1, 0, '2010-08-26 13:05:20', 'r--', 1, NULL),
(140, 'actionswgal', 'akce-s-gal', 'testy/akce/akce-s-gal', 'akce s gal', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'always', 0, 1, 0, '2010-08-27 16:21:02', 'r--', 0, NULL),
(123, 'text', 'nasi-partneri', 'o-nas/nasi-partneri', 'naši partneři', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-06-24 12:53:07', 'r--', 0, NULL),
(124, 'articleslist', 'titulni-strana', 'titulni-strana', 'titulní strana', 'titulni', NULL, NULL, NULL, NULL, NULL, NULL, 'titulni', 'titulni', NULL, NULL, NULL, NULL, 'a:1:{s:6:"catsid";a:1:{i:0;s:3:"102";}}', NULL, 0, 0, 1, 1, 'weekly', 1, 1, 1, '2010-08-03 23:08:53', 'r--', 0, NULL),
(136, 'photogalerymed', 'fotogalerky', 'testy/fotogalerky', 'fotogalerky', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'always', 0, 1, 0, '2010-08-17 15:52:55', 'r--', 1, NULL),
(127, 'titlepage', 'main', 'main', 'Titulni strana', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 10, 1, 0, 'always', 0, 1, 0, '2010-08-03 12:07:13', 'r--', 0, NULL),
(128, 'pokus', 'testy', 'testy', 'testy', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'always', 0, 1, 0, '2010-08-03 19:08:11', 'r--', 0, NULL),
(129, 'articles', 'clanky', 'o-nas/clanky', 'články', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'always', 0, 1, 0, '2010-08-04 17:57:11', 'r--', 1, NULL),
(132, 'articleswgal', 'clanky-s-galerii', 'testy/clanky-s-galerii', 'članky s galerii', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'always', 0, 1, 0, '2010-08-10 09:46:45', 'r--', 1, NULL),
(131, 'news', NULL, 'testy/novinky-studenti', 'novinky studenti', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'always', 0, 1, 0, '2010-08-10 09:52:20', '---', 1, NULL),
(133, 'actions', 'akce', 'testy/akce', 'Akce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'always', 0, 1, 0, '2010-08-10 12:18:00', 'r--', 1, NULL),
(134, 'cinemaprogram', 'kino', 'testy/kino', 'kino', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'always', 0, 1, 0, '2010-08-11 09:59:21', 'r--', 0, NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `zus_comments`
--

CREATE TABLE IF NOT EXISTS `zus_comments` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `zus_config`
--

CREATE TABLE IF NOT EXISTS `zus_config` (
  `id_config` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL,
  `label` varchar(1000) DEFAULT NULL,
  `value` text,
  `values` varchar(200) DEFAULT NULL,
  `protected` tinyint(1) NOT NULL DEFAULT '0',
  `type` enum('string','number','bool','list','listmulti','ser_object') NOT NULL DEFAULT 'string',
  PRIMARY KEY (`id_config`),
  UNIQUE KEY `key` (`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=78 ;

--
-- Vypisuji data pro tabulku `zus_config`
--

INSERT INTO `zus_config` (`id_config`, `key`, `label`, `value`, `values`, `protected`, `type`) VALUES
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
(13, 'SESSION_NAME', NULL, 'zus_cookie', NULL, 0, 'string'),
(14, 'WEB_NAME', NULL, 'VVE Engine', NULL, 0, 'string'),
(61, 'CATEGORIES_STRUCTURE', NULL, 'O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:0;s:22:"\0Category_Structure\0id";i:0;s:28:"\0Category_Structure\0idParent";N;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:8:{i:0;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:3:"124";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:2;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:3:"102";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:3;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:3:"100";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:4:{i:0;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:3:"110";s:28:"\0Category_Structure\0idParent";s:3:"100";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:1;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:3:"123";s:28:"\0Category_Structure\0idParent";s:3:"100";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:2;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:3:"129";s:28:"\0Category_Structure\0idParent";s:3:"100";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:3;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:3:"135";s:28:"\0Category_Structure\0idParent";s:3:"100";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}i:5;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:3:"115";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:3:{i:0;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:3:"116";s:28:"\0Category_Structure\0idParent";s:3:"115";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:1;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:3:"114";s:28:"\0Category_Structure\0idParent";s:3:"115";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:2;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:3:"117";s:28:"\0Category_Structure\0idParent";s:3:"115";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}i:6;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:3:"118";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:12;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:3:"127";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:13;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:3:"128";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:6:{i:0;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:3:"132";s:28:"\0Category_Structure\0idParent";s:3:"128";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:1;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:3:"131";s:28:"\0Category_Structure\0idParent";s:3:"128";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:2;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:3:"133";s:28:"\0Category_Structure\0idParent";s:3:"128";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:2:{i:0;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:3;s:22:"\0Category_Structure\0id";s:3:"139";s:28:"\0Category_Structure\0idParent";s:3:"133";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:1;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:3;s:22:"\0Category_Structure\0id";s:3:"140";s:28:"\0Category_Structure\0idParent";s:3:"133";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}i:3;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:3:"134";s:28:"\0Category_Structure\0idParent";s:3:"128";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:4;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:3:"136";s:28:"\0Category_Structure\0idParent";s:3:"128";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:5;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:3:"138";s:28:"\0Category_Structure\0idParent";s:3:"128";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}i:14;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:3:"137";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}', NULL, 1, 'ser_object'),
(58, 'USE_GLOBAL_ACCOUNTS_TB_PREFIXES', 'Prefixy tabulek pro které se má použít globální systém přihlašování', 'zus_', '', 0, 'string'),
(59, 'NAVIGATION_MENU_TABLE', 'Název tabulky s navigačním menu', 'zus_navigation_panel', NULL, 0, 'string'),
(60, 'SHARES_TABLE', 'Název tabulky s odkazy na sdílení (při global)', 'zus_shares', NULL, 0, 'string'),
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
(51, 'WEB_DESCRIPTION', 'Popis stránek', 'Testovací stránky VVE enginu verze 6.0', NULL, 0, 'string'),
(50, 'FEED_NUM', 'Poček generovaných rss/atom kanálů', '10', NULL, 0, 'number'),
(52, 'WEB_MASTER_NAME', 'Jméno webmastera', 'Jakub Matas', NULL, 0, 'string'),
(53, 'WEB_MASTER_EMAIL', 'E-mail webmastera', 'jakubmatas@gmail.com', NULL, 0, 'string'),
(54, 'FEED_TTL', 'Počet minut kešování kanálu', '30', NULL, 0, 'number'),
(55, 'WEB_COPYRIGHT', 'Copyright poznámka k webu ({Y} - nahrazeno rokem)', 'Obsah toho webu je licencován podle ... Žádná s jeho částí nesmí být použita bez vědomí webmastera. Copyrigth {Y}', NULL, 0, 'string'),
(56, 'SEARCH_ARTICLE_REL_MULTIPLIER', 'Násobič pro relevanci nadpisu článku (1 - nekonečno)', '5', NULL, 0, 'number'),
(57, 'ADMIN_MENU_STRUCTURE', 'Administrační menu', 'O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:0;s:22:"\0Category_Structure\0id";i:0;s:28:"\0Category_Structure\0idParent";N;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:6:{i:1;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:1:"3";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:2:{i:0;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";i:1;s:28:"\0Category_Structure\0idParent";s:1:"3";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:1;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:1:"4";s:28:"\0Category_Structure\0idParent";s:1:"3";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}i:2;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:1:"5";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:1:{i:0;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:1:"6";s:28:"\0Category_Structure\0idParent";s:1:"5";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}i:3;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:1:"7";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:1:{i:0;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:1:"8";s:28:"\0Category_Structure\0idParent";s:1:"7";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}i:4;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:1:"9";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:4:{i:0;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:2:"10";s:28:"\0Category_Structure\0idParent";s:1:"9";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:1;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:3:"103";s:28:"\0Category_Structure\0idParent";s:1:"9";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:2;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:3:"105";s:28:"\0Category_Structure\0idParent";s:1:"9";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:3;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:3:"125";s:28:"\0Category_Structure\0idParent";s:1:"9";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}i:5;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:2:"11";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:2:{i:0;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:2:"12";s:28:"\0Category_Structure\0idParent";s:2:"11";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:1;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:2:"13";s:28:"\0Category_Structure\0idParent";s:2:"11";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}i:6;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:1:"2";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}', NULL, 1, 'ser_object'),
(62, 'MAIN_PAGE_TITLE', 'Nadpis hlavní stránky', 'VVE Engine Main Title Page', NULL, 0, 'string'),
(63, 'ALLOW_EXTERNAL_JS', 'Povolení externích JavaScript souborů', 'true', NULL, 0, 'bool'),
(68, 'VERSION', 'Verze jádra', '6.2', NULL, 1, 'string'),
(74, 'CM_SITEMAP_MAX_ITEMS', 'Maximální počet položek v mapě stránek (pro vyhledávače)', '50', NULL, 0, 'number'),
(73, 'CM_SITEMAP_MAX_ITEMS_PAGE', 'Maximální počet položek v mapě stránek', '20', NULL, 0, 'number'),
(75, 'CM_SITEMAP_CAT_ICON', 'Název ikony pro sitemap', 'sitemap.png', NULL, 0, 'string'),
(76, 'CM_ERR_CAT_ICON', 'Název ikony pro chybovou stránku', 'error.png', NULL, 0, 'string'),
(77, 'CM_RSS_CAT_ICON', 'Název ikony pro stránku s rss kanály', 'rsslist.png', NULL, 0, 'string');

-- --------------------------------------------------------

--
-- Struktura tabulky `zus_groups`
--

CREATE TABLE IF NOT EXISTS `zus_groups` (
  `id_group` smallint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID skupiny',
  `name` varchar(15) DEFAULT NULL COMMENT 'Nazev skupiny',
  `label` varchar(100) DEFAULT NULL,
  `used` tinyint(1) NOT NULL DEFAULT '1',
  `default_right` varchar(3) NOT NULL,
  PRIMARY KEY (`id_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Vypisuji data pro tabulku `zus_groups`
--

INSERT INTO `zus_groups` (`id_group`, `name`, `label`, `used`, `default_right`) VALUES
(1, 'admin', 'Administrátor', 1, 'rwc'),
(2, 'guest', 'Host', 1, 'r--'),
(7, 'student', 'Skupina studentů', 1, '');

-- --------------------------------------------------------

--
-- Struktura tabulky `zus_mails_addressbook`
--

CREATE TABLE IF NOT EXISTS `zus_mails_addressbook` (
  `id_mail` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_group` smallint(6) NOT NULL DEFAULT '1',
  `name` varchar(30) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `surname` varchar(30) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `mail` varchar(100) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `note` varchar(400) DEFAULT NULL,
  PRIMARY KEY (`id_mail`),
  KEY `GROUP` (`id_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--
-- Vypisuji data pro tabulku `zus_mails_addressbook`
--



-- --------------------------------------------------------

--
-- Struktura tabulky `zus_mails_groups`
--

CREATE TABLE IF NOT EXISTS `zus_mails_groups` (
  `id_group` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `note` varchar(400) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Vypisuji data pro tabulku `zus_mails_groups`
--

INSERT INTO `zus_mails_groups` (`id_group`, `name`, `note`) VALUES
(1, 'Základní', 'Základní skupina'),
(3, 'Partneři', ''),
(8, 'test', ''),
(9, 'další', ''),
(10, 'ještě', ''),
(13, 'Newsletter', '');

-- --------------------------------------------------------

--
-- Struktura tabulky `zus_mails_sends`
--

CREATE TABLE IF NOT EXISTS `zus_mails_sends` (
  `id_mail` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_user` smallint(6) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `recipients` varchar(2000) CHARACTER SET utf8 DEFAULT NULL,
  `subject` varchar(500) CHARACTER SET utf8 DEFAULT NULL,
  `content` text CHARACTER SET utf8,
  `attachments` varchar(500) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id_mail`),
  KEY `id_user` (`id_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Vypisuji data pro tabulku `zus_mails_sends`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `zus_modules_instaled`
--

CREATE TABLE IF NOT EXISTS `zus_modules_instaled` (
  `id_module` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET utf8 NOT NULL,
  `version_major` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `version_minor` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_module`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;

--
-- Vypisuji data pro tabulku `zus_modules_instaled`
--

INSERT INTO `zus_modules_instaled` (`id_module`, `name`, `version_major`, `version_minor`) VALUES
(3, 'text', 1, 0),
(11, 'upgrade', 1, 1),
(20, 'mails', 2, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `zus_panels`
--

CREATE TABLE IF NOT EXISTS `zus_panels` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Vypisuji data pro tabulku `zus_panels`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `zus_rights`
--

CREATE TABLE IF NOT EXISTS `zus_rights` (
  `id_right` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_category` smallint(6) NOT NULL,
  `id_group` smallint(6) NOT NULL,
  `right` enum('---','r--','-w-','--c','rw-','-wc','r-c','rwc') NOT NULL DEFAULT 'r--',
  PRIMARY KEY (`id_right`),
  KEY `id_category` (`id_category`,`id_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=321 ;

--
-- Vypisuji data pro tabulku `zus_rights`
--

INSERT INTO `zus_rights` (`id_right`, `id_category`, `id_group`, `right`) VALUES
(1, 1, 1, 'rwc'),
(6, 1, 2, '---'),
(186, 9, 2, '---'),
(192, 12, 2, '---'),
(191, 12, 1, 'rwc'),
(185, 9, 1, 'rwc'),
(176, 6, 2, '---'),
(175, 6, 1, 'rwc'),
(190, 11, 2, '---'),
(184, 8, 2, '---'),
(170, 5, 2, '---'),
(169, 5, 1, 'rwc'),
(196, 100, 2, 'r--'),
(189, 11, 1, 'rwc'),
(183, 8, 1, 'rwc'),
(164, 4, 2, '---'),
(163, 4, 1, 'rwc'),
(195, 100, 1, 'rwc'),
(194, 13, 2, '---'),
(188, 10, 2, '---'),
(182, 7, 2, '---'),
(156, 3, 2, '---'),
(155, 3, 1, 'rwc'),
(193, 13, 1, 'rwc'),
(187, 10, 1, 'rwc'),
(181, 7, 1, 'rwc'),
(150, 2, 2, 'r--'),
(149, 2, 1, 'rwc'),
(226, 115, 2, 'r--'),
(225, 115, 1, 'rwc'),
(199, 102, 1, 'rwc'),
(200, 102, 2, 'r--'),
(201, 103, 1, 'rwc'),
(202, 103, 2, 'rwc'),
(305, 135, 7, 'r--'),
(304, 135, 2, 'r--'),
(205, 105, 1, 'rwc'),
(206, 105, 2, '---'),
(228, 116, 2, 'r--'),
(227, 116, 1, 'rwc'),
(215, 110, 1, 'rwc'),
(216, 110, 2, 'r--'),
(223, 114, 1, 'rwc'),
(224, 114, 2, 'r--'),
(229, 117, 1, 'r--'),
(230, 117, 2, 'r--'),
(231, 118, 1, 'rwc'),
(232, 118, 2, 'r--'),
(314, 138, 7, 'r--'),
(313, 138, 2, 'r--'),
(311, 137, 7, 'r--'),
(310, 137, 2, 'r--'),
(241, 123, 1, 'rwc'),
(242, 123, 2, 'r--'),
(243, 124, 1, 'rwc'),
(244, 124, 2, 'r--'),
(245, 125, 1, 'rwc'),
(246, 125, 2, '---'),
(308, 136, 7, 'r--'),
(307, 136, 2, 'r--'),
(249, 6, 7, '---'),
(250, 114, 7, 'r--'),
(251, 8, 7, '---'),
(252, 116, 7, 'r--'),
(253, 13, 7, '---'),
(254, 1, 7, '---'),
(255, 117, 7, 'r--'),
(256, 12, 7, '---'),
(257, 4, 7, '---'),
(258, 105, 7, '---'),
(259, 125, 7, '---'),
(260, 10, 7, '---'),
(261, 123, 7, 'r--'),
(262, 103, 7, '---'),
(263, 110, 7, 'r--'),
(264, 118, 7, 'r--'),
(265, 3, 7, '---'),
(266, 11, 7, '---'),
(267, 7, 7, '---'),
(268, 5, 7, '---'),
(306, 136, 1, 'rwc'),
(270, 124, 7, 'r--'),
(303, 135, 1, 'rwc'),
(312, 138, 1, 'rwc'),
(273, 9, 7, '---'),
(274, 115, 7, 'r--'),
(275, 102, 7, 'r--'),
(309, 137, 1, 'rwc'),
(277, 100, 7, 'r--'),
(278, 2, 7, 'r--'),
(279, 127, 1, 'rwc'),
(280, 127, 2, 'r--'),
(281, 127, 7, 'r--'),
(282, 128, 1, 'rwc'),
(283, 128, 2, 'r--'),
(284, 128, 7, 'r--'),
(285, 129, 1, 'rwc'),
(286, 129, 2, 'r--'),
(287, 129, 7, 'r--'),
(296, 132, 7, 'r--'),
(295, 132, 2, 'r--'),
(294, 132, 1, 'rwc'),
(291, 131, 1, 'rwc'),
(292, 131, 2, '---'),
(293, 131, 7, 'r--'),
(297, 133, 1, 'rwc'),
(298, 133, 2, 'r--'),
(299, 133, 7, 'r--'),
(300, 134, 1, 'rwc'),
(301, 134, 2, 'r--'),
(302, 134, 7, 'r--'),
(315, 139, 1, 'rwc'),
(316, 139, 2, 'r--'),
(317, 139, 7, 'r--'),
(318, 140, 1, 'rwc'),
(319, 140, 2, 'r--'),
(320, 140, 7, 'r--');

-- --------------------------------------------------------

--
-- Struktura tabulky `zus_search_apis`
--

CREATE TABLE IF NOT EXISTS `zus_search_apis` (
  `id_api` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `id_category` smallint(5) unsigned NOT NULL,
  `url` varchar(200) NOT NULL,
  `api` varchar(20) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id_api`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Vypisuji data pro tabulku `zus_search_apis`
--

INSERT INTO `zus_search_apis` (`id_api`, `id_category`, `url`, `api`, `name`) VALUES
(1, 137, 'http://www.google.cz/search?q={search}', 'google', 'google');

-- --------------------------------------------------------

--
-- Struktura tabulky `zus_shares`
--

CREATE TABLE IF NOT EXISTS `zus_shares` (
  `id_share` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `link` varchar(300) NOT NULL,
  `icon` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id_share`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Vypisuji data pro tabulku `zus_shares`
--

INSERT INTO `zus_shares` (`id_share`, `link`, `icon`, `name`) VALUES
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
-- Struktura tabulky `zus_templates`
--

CREATE TABLE IF NOT EXISTS `zus_templates` (
  `id_template` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(400) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `content` text,
  `type` varchar(20) NOT NULL DEFAULT 'text',
  `time_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_template`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Vypisuji data pro tabulku `zus_templates`
--
-- --------------------------------------------------------

--
-- Struktura tabulky `zus_texts`
--

CREATE TABLE IF NOT EXISTS `zus_texts` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `zus_users`
--

CREATE TABLE IF NOT EXISTS `zus_users` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=29 ;

--
-- Vypisuji data pro tabulku `zus_users`
--

INSERT INTO `zus_users` (`id_user`, `username`, `password`, `id_group`, `name`, `surname`, `mail`, `note`, `blocked`, `foto_file`, `deleted`) VALUES
(1, 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 1, 'Jakub', 'Matas', 'jakubmatas@gmail.com', 'administrátor', 0, NULL, 0),
(2, 'guest', '', 2, 'host', 'host', '', 'host systému', 0, NULL, 0),
(3, 'cuba', 'd033e22ae348aeb5660fc2140aec35850c4da997', 1, 'Jakub', 'Matas', 'jakubmatas@gmail.com', 'Normální uživatel', 0, 'cuba1.jpg', 0),
(28, 'cenek', '35675e68f4b5af7b995d9205ad0fc43842f16450', 7, 'cen', 'vgjhvjh', 'cenek@seynam.com', '', 0, NULL, 0);
