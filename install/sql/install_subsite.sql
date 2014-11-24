-- phpMyAdmin SQL Dump
-- version 4.0.5
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Úte 20. kvě 2014, 13:03
-- Verze serveru: 5.5.37-0ubuntu0.14.04.1
-- Verze PHP: 5.5.9-1ubuntu4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}autorun`
--

DROP TABLE IF EXISTS `{PREFIX}autorun`;
CREATE TABLE IF NOT EXISTS `{PREFIX}autorun` (
  `id_autorun` int(11) NOT NULL AUTO_INCREMENT,
  `autorun_module_name` varchar(20) NOT NULL,
  `autorun_period` varchar(10) NOT NULL DEFAULT 'daily',
  `autorun_url` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id_autorun`),
  KEY `period` (`autorun_period`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Vypisuji data pro tabulku `{PREFIX}autorun`
--

INSERT INTO `{PREFIX}autorun` (`id_autorun`, `autorun_module_name`, `autorun_period`, `autorun_url`) VALUES
(1, 'services', 'weekly', NULL),
(2, 'mailsnewsletters', 'hourly', NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}banners`
--

DROP TABLE IF EXISTS `{PREFIX}banners`;
CREATE TABLE IF NOT EXISTS `{PREFIX}banners` (
  `id_banner` int(11) NOT NULL AUTO_INCREMENT,
  `banner_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `banner_file` varchar(50) NOT NULL,
  `banner_active` tinyint(1) NOT NULL DEFAULT '1',
  `banner_box` varchar(20) DEFAULT NULL,
  `banner_order` smallint(6) NOT NULL DEFAULT '0',
  `banner_url` varchar(200) DEFAULT NULL,
  `banner_text` varchar(300) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `banner_time_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `banner_new_window` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_banner`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}banners_clicks`
--

DROP TABLE IF EXISTS `{PREFIX}banners_clicks`;
CREATE TABLE IF NOT EXISTS `{PREFIX}banners_clicks` (
  `id_banner_click` int(11) NOT NULL AUTO_INCREMENT,
  `id_banner` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
  `banner_click_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `banner_click_ip` int(11) DEFAULT '0',
  `banner_click_browser` varchar(200) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`id_banner_click`),
  KEY `banner` (`id_banner`),
  KEY `timebanner` (`id_banner`,`banner_click_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}categories`
--

DROP TABLE IF EXISTS `{PREFIX}categories`;
CREATE TABLE IF NOT EXISTS `{PREFIX}categories` (
  `id_category` smallint(3) NOT NULL AUTO_INCREMENT,
  `module` varchar(30) DEFAULT NULL,
  `data_dir` varchar(100) DEFAULT NULL,
  `urlkey_cs` varchar(100) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `disable_cs` tinyint(1) NOT NULL DEFAULT '0',
  `label_cs` varchar(200) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `alt_cs` varchar(200) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `urlkey_en` varchar(100) DEFAULT NULL,
  `disable_en` tinyint(1) NOT NULL DEFAULT '0',
  `label_en` varchar(200) DEFAULT NULL,
  `alt_en` varchar(200) DEFAULT NULL,
  `urlkey_de` varchar(100) DEFAULT NULL,
  `disable_de` tinyint(1) NOT NULL DEFAULT '0',
  `label_de` varchar(200) DEFAULT NULL,
  `alt_de` varchar(200) DEFAULT NULL,
  `urlkey_sk` varchar(100) CHARACTER SET utf8 COLLATE utf8_slovak_ci DEFAULT NULL,
  `disable_sk` tinyint(1) NOT NULL DEFAULT '0',
  `label_sk` varchar(200) CHARACTER SET utf8 COLLATE utf8_slovak_ci DEFAULT NULL,
  `alt_sk` varchar(200) CHARACTER SET utf8 COLLATE utf8_slovak_ci DEFAULT NULL,
  `keywords_cs` varchar(200) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `description_cs` varchar(500) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `keywords_en` varchar(200) DEFAULT NULL,
  `description_en` varchar(500) DEFAULT NULL,
  `keywords_de` varchar(200) DEFAULT NULL,
  `description_de` varchar(500) DEFAULT NULL,
  `keywords_sk` varchar(200) CHARACTER SET utf8 COLLATE utf8_slovak_ci DEFAULT NULL,
  `description_sk` varchar(500) CHARACTER SET utf8 COLLATE utf8_slovak_ci DEFAULT NULL,
  `ser_params` varchar(1000) DEFAULT NULL,
  `params` varchar(200) DEFAULT NULL,
  `protected` tinyint(1) NOT NULL DEFAULT '0',
  `priority` smallint(2) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'je-li kategorie aktivní',
  `individual_panels` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Jesltli jsou panely pro kategorii individuální',
  `sitemap_changefreq` enum('always','hourly','daily','weekly','monthly','yearly','never') NOT NULL DEFAULT 'yearly',
  `sitemap_priority` float NOT NULL DEFAULT '0.1',
  `visibility` smallint(6) DEFAULT '1',
  `changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `default_right` enum('---','r--','-w-','--c','rw-','-wc','r-c','rwc') NOT NULL DEFAULT 'r--',
  `feeds` tinyint(1) NOT NULL DEFAULT '0',
  `icon` varchar(100) DEFAULT NULL,
  `background` varchar(100) DEFAULT NULL,
  `id_owner_user` smallint(6) DEFAULT '0',
  `allow_handle_access` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_category`),
  KEY `urlkey_cs` (`urlkey_cs`),
  KEY `urlkey_sk` (`urlkey_sk`),
  KEY `urlkey_en` (`urlkey_en`),
  KEY `urlkey_de` (`urlkey_de`),
  KEY `urlkey_disable_cs` (`disable_cs`),
  KEY `urlkey_disable_en` (`disable_en`),
  KEY `urlkey_disable_de` (`disable_de`),
  KEY `urlkey_disable_sk` (`disable_sk`),
  KEY `individual_panel` (`individual_panels`),
  FULLTEXT KEY `label_cs` (`label_cs`),
  FULLTEXT KEY `label_en` (`label_en`),
  FULLTEXT KEY `label_de` (`label_de`),
  FULLTEXT KEY `label_sk` (`label_sk`),
  FULLTEXT KEY `description_cs` (`description_cs`),
  FULLTEXT KEY `description_en` (`description_en`),
  FULLTEXT KEY `description_de` (`description_de`),
  FULLTEXT KEY `description_sk` (`description_sk`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Vypisuji data pro tabulku `{PREFIX}categories`
--

INSERT INTO `{PREFIX}categories` (`id_category`, `module`, `data_dir`, `urlkey_cs`, `disable_cs`, `label_cs`, `alt_cs`, `urlkey_en`, `disable_en`, `label_en`, `alt_en`, `urlkey_de`, `disable_de`, `label_de`, `alt_de`, `urlkey_sk`, `disable_sk`, `label_sk`, `alt_sk`, `keywords_cs`, `description_cs`, `keywords_en`, `description_en`, `keywords_de`, `description_de`, `keywords_sk`, `description_sk`, `ser_params`, `params`, `protected`, `priority`, `active`, `individual_panels`, `sitemap_changefreq`, `sitemap_priority`, `visibility`, `changed`, `default_right`, `feeds`, `icon`, `background`, `id_owner_user`, `allow_handle_access`) VALUES
(1, 'login', 'ucet', 'ucet', 0, 'účet', NULL, 'account', 0, 'account', NULL, NULL, 0, NULL, NULL, 'ucet', 0, 'účet', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, '2011-06-21 07:18:45', 'r--', 0, NULL, NULL, 0, 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}category_redirect`
--

DROP TABLE IF EXISTS `{PREFIX}category_redirect`;
CREATE TABLE IF NOT EXISTS `{PREFIX}category_redirect` (
  `id_category_redirect` int(11) NOT NULL AUTO_INCREMENT,
  `id_category` int(11) DEFAULT NULL,
  `lang` varchar(2) DEFAULT NULL,
  `redirect_from` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_category_redirect`),
  KEY `id_cat` (`id_category`),
  KEY `lang_id_cat` (`id_category`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}comments`
--

DROP TABLE IF EXISTS `{PREFIX}comments`;
CREATE TABLE IF NOT EXISTS `{PREFIX}comments` (
  `id_comment` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_category` smallint(6) NOT NULL,
  `id_article` smallint(6) NOT NULL,
  `id_parent` smallint(6) DEFAULT '0',
  `comment_nick` varchar(100) NOT NULL,
  `comment_comment` varchar(500) NOT NULL,
  `comment_public` tinyint(1) NOT NULL DEFAULT '1',
  `comment_censored` tinyint(1) NOT NULL DEFAULT '0',
  `comment_corder` smallint(6) NOT NULL DEFAULT '1',
  `comment_level` smallint(6) NOT NULL DEFAULT '0',
  `comment_time_add` datetime NOT NULL,
  `comment_ip_address` varchar(15) DEFAULT NULL,
  `comment_confirmed` tinyint(1) DEFAULT NULL,
  `comment_mail` varchar(60) DEFAULT NULL,
  `comment_admin_viewed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_comment`),
  KEY `id_category` (`id_category`,`id_article`),
  KEY `id_article` (`id_article`),
  KEY `order` (`comment_corder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}config`
--

DROP TABLE IF EXISTS `{PREFIX}config`;
CREATE TABLE IF NOT EXISTS `{PREFIX}config` (
  `id_config` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL,
  `label` varchar(1000) DEFAULT NULL,
  `value` text,
  `values` varchar(200) DEFAULT NULL,
  `protected` tinyint(1) NOT NULL DEFAULT '0',
  `type` enum('string','number','bool','list','listmulti','ser_object') NOT NULL DEFAULT 'string',
  `id_group` smallint(6) NOT NULL DEFAULT '0',
  `callback_func` varchar(100) DEFAULT NULL,
  `hidden_value` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_config`),
  UNIQUE KEY `key` (`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Vypisuji data pro tabulku `{PREFIX}config`
--

INSERT INTO `{PREFIX}config` (`id_config`, `key`, `label`, `value`, `values`, `protected`, `type`, `id_group`, `callback_func`, `hidden_value`) VALUES
(1, 'CATEGORIES_STRUCTURE', NULL, 'O:18:"Category_Structure":7:{s:25:"\0Category_Structure\0level";i:0;s:22:"\0Category_Structure\0id";i:0;s:28:"\0Category_Structure\0idParent";N;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:1:{i:0;O:18:"Category_Structure":7:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:1:"1";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}s:30:"\0Category_Structure\0withHidden";b:0;s:4:"type";s:4:"main";}}s:30:"\0Category_Structure\0withHidden";b:0;s:4:"type";s:4:"main";}', NULL, 1, 'ser_object', 1, NULL, 0),
(2, 'ADMIN_MENU_STRUCTURE', 'Administrační menu', '', NULL, 1, 'ser_object', 1, NULL, 0),
(3, 'VERSION', 'Verze jádra', '8.0.3', NULL, 1, 'string', 1, NULL, 0),
(5, 'FCB_ACCESS_TOKEN', 'Access token pro přístup k Facebooku', NULL, NULL, 0, 'string', 11, NULL, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}config_groups`
--

DROP TABLE IF EXISTS `{PREFIX}config_groups`;
CREATE TABLE IF NOT EXISTS `{PREFIX}config_groups` (
  `id_group` int(11) NOT NULL AUTO_INCREMENT,
  `name_cs` varchar(45) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `name_sk` varchar(45) CHARACTER SET utf8 COLLATE utf8_slovak_ci DEFAULT NULL,
  `name_en` varchar(45) DEFAULT NULL,
  `name_de` varchar(45) DEFAULT NULL,
  `desc_cs` varchar(200) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `desc_sk` varchar(200) CHARACTER SET utf8 COLLATE utf8_slovak_ci DEFAULT NULL,
  `desc_en` varchar(200) DEFAULT NULL,
  `desc_de` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Vypisuji data pro tabulku `{PREFIX}config_groups`
--

INSERT INTO `{PREFIX}config_groups` (`id_group`, `name_cs`, `name_sk`, `name_en`, `name_de`, `desc_cs`, `desc_sk`, `desc_en`, `desc_de`) VALUES
(2, 'Základní nastavení', 'Základné nastavenie', 'Basic settings', 'Grundeinstellungen', 'Základní nastavení aplikace', 'Základné nastavenia aplikácie', 'Basic settings', NULL),
(3, 'Pokročilá nastavení', 'Rozšírené nastavenia', 'Advanced settings', 'Erweiterte Einstellungen', 'Nastavení chování jádra (přihlášení, subdomény, atd.)', 'Nastavenie správania jadra (prihlásení, subdomény, atď)', 'Adjustment of the Kernel (login, subdomains, etc.)', NULL),
(4, 'Vzhled', 'Vzhľad', 'Appearance', 'Aussehen', 'Nastavení vzhledu stránek', 'Nastavenie vzhľadu stránok', 'Setting up of site', NULL),
(6, 'E-maily', 'E-maily', 'E-mails', 'E-Mails', 'Nastavení e-mailových služeb', 'Nastavenie e-mailových služieb', 'Setting up e-mail service', NULL),
(7, 'Obrázky', 'Obrázky', 'Images', 'Bilder', 'Nastavení obrázků (velikost)', 'Nastavenie obrázkov (veľkosť)', 'Picture settings (size)', NULL),
(8, 'Lokalizace a jazyky', 'Lokalizácia a jazyky', 'Localization and languages', 'Ortsbestimmung und Sprachen', 'Nastavení jazyků prostředí a lokalizace aplikace', 'Nastavenie jazykov prostredia a lokalizácia aplikácie', 'The language environment and positioning applications', NULL),
(9, 'Hledání', 'Hľadanie', 'Search', 'Suche', 'Nastavení výsledků hledání', 'Nastavenie výsledkov hľadania', 'Search Settings', NULL),
(5, 'Exporty', 'Exporty', 'Exports', 'Exporte', 'Nastavení exportů (rss, pdf, ...)', 'Nastavenie exportov (rss, pdf, ...)', 'Export Settings (RSS, PDF, ...)', NULL),
(1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}custom_menu_items`
--

DROP TABLE IF EXISTS `{PREFIX}custom_menu_items`;
CREATE TABLE IF NOT EXISTS `{PREFIX}custom_menu_items` (
  `id_custom_menu_item` int(11) NOT NULL AUTO_INCREMENT,
  `id_category` int(11) NOT NULL DEFAULT '0',
  `menu_item_box` varchar(45) NOT NULL,
  `menu_item_name_cs` varchar(50) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `menu_item_name_en` varchar(50) DEFAULT NULL,
  `menu_item_name_de` varchar(50) DEFAULT NULL,
  `menu_item_name_sk` varchar(50) CHARACTER SET utf8 COLLATE utf8_slovak_ci DEFAULT NULL,
  `menu_item_link` varchar(200) CHARACTER SET latin1 DEFAULT NULL,
  `menu_item_new_window` tinyint(1) DEFAULT '0',
  `menu_item_order` int(11) NOT NULL DEFAULT '0',
  `menu_item_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_custom_menu_item`),
  KEY `fk_category` (`id_category`),
  KEY `box` (`menu_item_box`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}forms`
--

DROP TABLE IF EXISTS `{PREFIX}forms`;
CREATE TABLE IF NOT EXISTS `{PREFIX}forms` (
  `id_form` int(11) NOT NULL AUTO_INCREMENT,
  `form_name` varchar(200) DEFAULT NULL,
  `form_message` varchar(1000) DEFAULT NULL,
  `form_send_to_mails` varchar(500) DEFAULT NULL,
  `form_send_to_users` varchar(100) DEFAULT NULL,
  `form_sended` int(11) DEFAULT '0',
  `form_time_add` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `form_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_form`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}forms_elements`
--

DROP TABLE IF EXISTS `{PREFIX}forms_elements`;
CREATE TABLE IF NOT EXISTS `{PREFIX}forms_elements` (
  `id_form_element` int(11) NOT NULL AUTO_INCREMENT,
  `id_form` int(11) NOT NULL,
  `form_element_name` varchar(50) NOT NULL,
  `form_element_label` varchar(50) NOT NULL,
  `form_element_type` varchar(20) NOT NULL DEFAULT 'text',
  `form_element_value` varchar(200) DEFAULT NULL,
  `form_element_required` tinyint(1) DEFAULT '0',
  `form_element_options` varchar(1000) DEFAULT NULL,
  `form_element_order` smallint(6) DEFAULT '1',
  `form_element_validator` varchar(50) DEFAULT NULL,
  `form_element_ismultiple` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_form_element`),
  KEY `order` (`id_form`,`form_element_order`),
  KEY `id_form` (`id_form`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}groups`
--

DROP TABLE IF EXISTS `{PREFIX}groups`;
CREATE TABLE IF NOT EXISTS `{PREFIX}groups` (
  `id_group` smallint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID skupiny',
  `name` varchar(15) DEFAULT NULL COMMENT 'Nazev skupiny',
  `label` varchar(100) DEFAULT NULL,
  `used` tinyint(1) NOT NULL DEFAULT '1',
  `default_right` varchar(3) NOT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Vypisuji data pro tabulku `{PREFIX}groups`
--

INSERT INTO `{PREFIX}groups` (`id_group`, `name`, `label`, `used`, `default_right`, `admin`) VALUES
(1, 'admin', 'Administrátor', 1, 'rwc', 1),
(2, 'guest', 'Host', 1, 'r--', 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}hpslideshow_images`
--

DROP TABLE IF EXISTS `{PREFIX}hpslideshow_images`;
CREATE TABLE IF NOT EXISTS `{PREFIX}hpslideshow_images` (
  `id_image` int(11) NOT NULL AUTO_INCREMENT,
  `id_category` smallint(6) DEFAULT '0',
  `image_label_cs` varchar(400) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `image_label_en` varchar(400) DEFAULT NULL,
  `image_label_de` varchar(400) DEFAULT NULL,
  `image_label_sk` varchar(400) CHARACTER SET utf8 COLLATE utf8_slovak_ci DEFAULT NULL,
  `image_link_cs` varchar(100) DEFAULT NULL,
  `image_link_en` varchar(100) DEFAULT NULL,
  `image_link_de` varchar(100) DEFAULT NULL,
  `image_link_sk` varchar(100) DEFAULT NULL,
  `image_order` smallint(6) NOT NULL DEFAULT '0',
  `image_active` tinyint(1) NOT NULL DEFAULT '1',
  `image_file` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id_image`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}mails_addressbook`
--

DROP TABLE IF EXISTS `{PREFIX}mails_addressbook`;
CREATE TABLE IF NOT EXISTS `{PREFIX}mails_addressbook` (
  `id_addressbook_mail` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_addressbook_group` smallint(6) NOT NULL DEFAULT '1',
  `addressbook_name` varchar(30) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `addressbook_surname` varchar(30) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `addressbook_mail` varchar(100) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `addressbook_note` varchar(400) DEFAULT NULL,
  `addressbook_valid` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id_addressbook_mail`),
  KEY `GROUP` (`id_addressbook_group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}mails_addressbook_groups`
--

DROP TABLE IF EXISTS `{PREFIX}mails_addressbook_groups`;
CREATE TABLE IF NOT EXISTS `{PREFIX}mails_addressbook_groups` (
  `id_addressbook_group` int(11) NOT NULL AUTO_INCREMENT,
  `addressbook_group_name` varchar(45) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `addressbook_group_note` varchar(400) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id_addressbook_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Vypisuji data pro tabulku `{PREFIX}mails_addressbook_groups`
--

INSERT INTO `{PREFIX}mails_addressbook_groups` (`id_addressbook_group`, `addressbook_group_name`, `addressbook_group_note`) VALUES
(1, 'Základní', 'Základní skupina');

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}mails_newsletters`
--

DROP TABLE IF EXISTS `{PREFIX}mails_newsletters`;
CREATE TABLE IF NOT EXISTS `{PREFIX}mails_newsletters` (
  `id_newsletter` int(11) NOT NULL AUTO_INCREMENT,
  `id_newsletter_template` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `newsletter_subject` varchar(100) DEFAULT NULL,
  `newsletter_date_send` date DEFAULT NULL,
  `newsletter_deleted` tinyint(4) DEFAULT '0',
  `newsletter_active` tinyint(4) DEFAULT '0',
  `newsletter_content` text,
  `newsletter_groups_ids` varchar(200) DEFAULT NULL,
  `newsletter_viewed` int(11) DEFAULT '0',
  PRIMARY KEY (`id_newsletter`),
  KEY `fk_users` (`id_user`),
  KEY `fk_newsletters_templates` (`id_newsletter_template`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}mails_newsletters_queue`
--

DROP TABLE IF EXISTS `{PREFIX}mails_newsletters_queue`;
CREATE TABLE IF NOT EXISTS `{PREFIX}mails_newsletters_queue` (
  `id_newsletter_queue` int(11) NOT NULL AUTO_INCREMENT,
  `id_newsletter` int(11) NOT NULL,
  `newsletter_queue_mail` varchar(100) NOT NULL,
  `newsletter_queue_name` varchar(100) DEFAULT NULL,
  `newsletter_queue_surname` varchar(100) DEFAULT NULL,
  `newsletter_queue_date_send` date NOT NULL,
  PRIMARY KEY (`id_newsletter_queue`,`id_newsletter`),
  KEY `fk_newsletter` (`id_newsletter`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}mails_newsletters_templates`
--

DROP TABLE IF EXISTS `{PREFIX}mails_newsletters_templates`;
CREATE TABLE IF NOT EXISTS `{PREFIX}mails_newsletters_templates` (
  `id_newsletter_template` int(11) NOT NULL AUTO_INCREMENT,
  `newsletter_template_name` varchar(100) NOT NULL,
  `newsletter_template_deleted` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id_newsletter_template`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}mails_sends`
--

DROP TABLE IF EXISTS `{PREFIX}mails_sends`;
CREATE TABLE IF NOT EXISTS `{PREFIX}mails_sends` (
  `id_mail` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_user` smallint(6) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `recipients` text,
  `subject` varchar(500) DEFAULT NULL,
  `content` text,
  `attachments` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id_mail`),
  KEY `id_user` (`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}mails_send_queue`
--

DROP TABLE IF EXISTS `{PREFIX}mails_send_queue`;
CREATE TABLE IF NOT EXISTS `{PREFIX}mails_send_queue` (
  `id_mail` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` smallint(6) NOT NULL,
  `mail` varchar(100) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `undeliverable` tinyint(1) DEFAULT '0',
  `mail_data` blob,
  PRIMARY KEY (`id_mail`),
  UNIQUE KEY `id_mail_UNIQUE` (`id_mail`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}modules_instaled`
--

DROP TABLE IF EXISTS `{PREFIX}modules_instaled`;
CREATE TABLE IF NOT EXISTS `{PREFIX}modules_instaled` (
  `id_module` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `version_major` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `version_minor` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `version` varchar(5) NOT NULL DEFAULT '1.0.0',
  PRIMARY KEY (`id_module`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

--
-- Vypisuji data pro tabulku `{PREFIX}modules_instaled`
--

INSERT INTO `{PREFIX}modules_instaled` (`id_module`, `name`, `version_major`, `version_minor`, `version`) VALUES
(1, 'text', 1, 4, '1.4.0'),
(2, 'upgrade', 1, 1, '1.1.0'),
(3, 'mails', 4, 0, '4.0.0'),
(4, 'search', 1, 0, '1.0.0'),
(5, 'users', 2, 0, '2.0.0'),
(6, 'panels', 1, 1, '1.1.0'),
(7, 'empty', 1, 0, '1.0.0'),
(8, 'services', 2, 0, '2.0.0'),
(9, 'configuration', 3, 0, '3.0.0'),
(10, 'templates', 1, 1, '1.1.0'),
(11, 'phpinfo', 1, 0, '1.0.0'),
(12, 'categories', 2, 0, '2.0.0'),
(13, 'login', 1, 0, '1.0.0'),
(14, 'quicktools', 1, 0, '1.0.0'),
(15, 'forms', 1, 0, '1.0.0'),
(16, 'banners', 1, 0, '1.0.0'),
(17, 'custommenu', 1, 0, '1.0.0'),
(18, 'mailsaddressbook', 1, 0, '1.0.0'),
(19, 'mailsnewsletters', 1, 1, '1.1.0'),
(20, 'hpslideshow', 1, 0, '1.1.0'),
(21, 'catsbulkedit', 1, 0, '1.0.0'),
(22, 'crontab', 1, 0, '1.0.0'),
(23, 'trstaticstexts', 1, 0, '1.0.0'),
(24, 'redirect', 1, 0, '1.0.0');

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}navigation_panel`
--

DROP TABLE IF EXISTS `{PREFIX}navigation_panel`;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}panels`
--

DROP TABLE IF EXISTS `{PREFIX}panels`;
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
  `pname_sk` varchar(100) CHARACTER SET utf8 COLLATE utf8_slovak_ci DEFAULT NULL,
  `panel_force_global` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_panel`),
  KEY `id_cat` (`id_cat`),
  KEY `id_show_cat` (`id_show_cat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}quicktools`
--

DROP TABLE IF EXISTS `{PREFIX}quicktools`;
CREATE TABLE IF NOT EXISTS `{PREFIX}quicktools` (
  `id_tool` int(11) NOT NULL,
  `id_user` smallint(6) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `url` varchar(300) DEFAULT NULL,
  `icon` varchar(45) DEFAULT NULL,
  `order` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id_tool`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}rights`
--

DROP TABLE IF EXISTS `{PREFIX}rights`;
CREATE TABLE IF NOT EXISTS `{PREFIX}rights` (
  `id_right` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_category` smallint(6) NOT NULL,
  `id_group` smallint(6) NOT NULL,
  `right` enum('---','r--','-w-','--c','rw-','-wc','r-c','rwc') NOT NULL DEFAULT 'r--',
  PRIMARY KEY (`id_right`),
  KEY `id_cat_grp` (`id_category`,`id_group`),
  KEY `id_cat` (`id_category`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Vypisuji data pro tabulku `{PREFIX}rights`
--

INSERT INTO `{PREFIX}rights` (`id_right`, `id_category`, `id_group`, `right`) VALUES
(1, 1, 1, 'rwc'),
(2, 1, 2, 'r--');

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}search_apis`
--

DROP TABLE IF EXISTS `{PREFIX}search_apis`;
CREATE TABLE IF NOT EXISTS `{PREFIX}search_apis` (
  `id_api` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `id_category` smallint(5) unsigned NOT NULL,
  `url` varchar(200) NOT NULL,
  `api` varchar(20) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id_api`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}secure_tokens`
--

DROP TABLE IF EXISTS `{PREFIX}secure_tokens`;
CREATE TABLE IF NOT EXISTS `{PREFIX}secure_tokens` (
  `id_secure_token` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `secure_token` varchar(40) NOT NULL,
  `secure_token_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_secure_token`),
  KEY `token_user_time` (`secure_token`,`id_user`,`secure_token_created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}sessions`
--

DROP TABLE IF EXISTS `{PREFIX}sessions`;
CREATE TABLE IF NOT EXISTS `{PREFIX}sessions` (
  `session_key` varchar(32) NOT NULL,
  `value` blob,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ip` varchar(55) DEFAULT NULL,
  `id_user` int(11) DEFAULT '0',
  PRIMARY KEY (`session_key`),
  UNIQUE KEY `ssession_key_UNIQUE` (`session_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}texts`
--

DROP TABLE IF EXISTS `{PREFIX}texts`;
CREATE TABLE IF NOT EXISTS `{PREFIX}texts` (
  `id_text` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `id_item` smallint(5) unsigned NOT NULL,
  `id_user` int(11) DEFAULT '0',
  `subkey` varchar(30) NOT NULL DEFAULT 'nokey',
  `changed` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `label_cs` varchar(200) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `text_cs` mediumtext CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `text_clear_cs` mediumtext CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `label_en` varchar(200) DEFAULT NULL,
  `text_en` mediumtext,
  `text_clear_en` mediumtext,
  `label_de` varchar(200) DEFAULT NULL,
  `text_de` mediumtext,
  `text_clear_de` mediumtext,
  `label_sk` varchar(1000) CHARACTER SET utf8 COLLATE utf8_slovak_ci DEFAULT NULL,
  `text_sk` mediumtext CHARACTER SET utf8 COLLATE utf8_slovak_ci,
  `text_clear_sk` mediumtext CHARACTER SET utf8 COLLATE utf8_slovak_ci,
  `data` text,
  PRIMARY KEY (`id_text`),
  KEY `id_item` (`id_item`),
  KEY `subkey` (`id_item`,`subkey`),
  FULLTEXT KEY `label_cs` (`label_cs`),
  FULLTEXT KEY `label_en` (`label_en`),
  FULLTEXT KEY `label_de` (`label_de`),
  FULLTEXT KEY `label_sk` (`label_sk`),
  FULLTEXT KEY `text_clear_de` (`text_clear_de`),
  FULLTEXT KEY `text_clear_en` (`text_clear_en`),
  FULLTEXT KEY `text_clear_cs` (`text_clear_cs`),
  FULLTEXT KEY `text_clear_sk` (`text_clear_sk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}texts_has_private_groups`
--

DROP TABLE IF EXISTS `{PREFIX}texts_has_private_groups`;
CREATE TABLE IF NOT EXISTS `{PREFIX}texts_has_private_groups` (
  `id_group` smallint(6) NOT NULL,
  `id_text` smallint(6) NOT NULL,
  PRIMARY KEY (`id_group`,`id_text`),
  KEY `fk_tb_groups_id_group` (`id_group`),
  KEY `fk_tb_texts_id_text` (`id_text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}texts_has_private_users`
--

DROP TABLE IF EXISTS `{PREFIX}texts_has_private_users`;
CREATE TABLE IF NOT EXISTS `{PREFIX}texts_has_private_users` (
  `id_user` smallint(6) NOT NULL,
  `id_text` smallint(6) NOT NULL,
  PRIMARY KEY (`id_user`,`id_text`),
  KEY `fk_tb_users_id_user` (`id_user`),
  KEY `fk_tb_texts_id_text` (`id_text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}users`
--

DROP TABLE IF EXISTS `{PREFIX}users`;
CREATE TABLE IF NOT EXISTS `{PREFIX}users` (
  `id_user` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID uzivatele',
  `username` varchar(20) NOT NULL COMMENT 'Uzivatelske jmeno',
  `password` varchar(100) DEFAULT NULL COMMENT 'Heslo',
  `password_restore` varchar(100) DEFAULT NULL,
  `id_group` smallint(3) unsigned DEFAULT '3',
  `name` varchar(30) NOT NULL,
  `surname` varchar(30) NOT NULL,
  `mail` varchar(50) NOT NULL,
  `note` varchar(500) DEFAULT NULL,
  `blocked` tinyint(1) NOT NULL DEFAULT '0',
  `foto_file` varchar(30) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` datetime DEFAULT NULL,
  `external_auth_id` varchar(200) DEFAULT NULL,
  `authenticator` varchar(20) DEFAULT 'internal',
  PRIMARY KEY (`id_user`,`username`),
  KEY `id_group` (`id_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Vypisuji data pro tabulku `{PREFIX}users`
--

INSERT INTO `{PREFIX}users` (`id_user`, `username`, `password`, `password_restore`, `id_group`, `name`, `surname`, `mail`, `note`, `blocked`, `foto_file`, `deleted`, `created`, `last_login`, `external_auth_id`, `authenticator`) VALUES
(2, 'guest', NULL, NULL, 2, 'test', 'tetasdhf', '', NULL, 0, NULL, 0, NULL, NULL, NULL, 'internal'),
(1, 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', NULL, 1, 'admin', 'admin', '', NULL, 0, NULL, 0, NULL, NULL, NULL, 'internal');

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}users_logins`
--

DROP TABLE IF EXISTS `{PREFIX}users_logins`;
CREATE TABLE IF NOT EXISTS `{PREFIX}users_logins` (
  `id_user_login` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL DEFAULT '0',
  `user_login_ip` varchar(15) DEFAULT NULL,
  `user_login_browser` varchar(200) DEFAULT NULL,
  `user_login_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_user_login`),
  KEY `idu_by_time` (`id_user`,`user_login_time`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}users_settings`
--

DROP TABLE IF EXISTS `{PREFIX}users_settings`;
CREATE TABLE IF NOT EXISTS `{PREFIX}users_settings` (
  `id_user_setting` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `setting_name` varchar(50) DEFAULT NULL,
  `setting_value` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id_user_setting`),
  KEY `id_user` (`id_user`),
  KEY `user_setting` (`setting_name`,`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
