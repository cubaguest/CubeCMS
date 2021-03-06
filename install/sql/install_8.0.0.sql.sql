-- phpMyAdmin SQL Dump
-- version 4.0.5
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Sob 10. srp 2013, 12:47
-- Verze serveru: 5.5.31-0ubuntu0.13.04.1
-- Verze PHP: 5.4.9-4ubuntu2.2

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
(3, 'VERSION', 'Verze jádra', '8.0.0', NULL, 1, 'string', 1, NULL, 0),
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
(20, 'hpslideshow', 1, 0, '1.0.0'),
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

--
-- Vypisuji data pro tabulku `{PREFIX}sessions`
--

INSERT INTO `{PREFIX}sessions` (`session_key`, `value`, `created`, `updated`, `ip`, `id_user`) VALUES
('oi4hn8rj3142p1klogens3bro5', 0x6c616e677c733a323a226373223b757365726e616d657c733a353a2261646d696e223b6d61696c7c733a303a22223b69645f757365727c693a313b69645f67726f75707c693a313b67726f75705f6e616d657c733a353a2261646d696e223b69705f616464726573737c733a393a223132372e302e302e31223b6c6f67696e74696d657c693a313337363133313635383b6c6f67696e7c623a313b61646d696e7c623a313b73697465737c613a303a7b7d, '2013-08-10 12:47:27', '2013-08-10 12:47:38', '127.0.0.1', 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}sites`
--

DROP TABLE IF EXISTS `{PREFIX}sites`;
CREATE TABLE IF NOT EXISTS `{PREFIX}sites` (
  `id_site` smallint(6) NOT NULL AUTO_INCREMENT,
  `domain` varchar(20) DEFAULT NULL,
  `dir` varchar(20) DEFAULT NULL,
  `table_prefix` varchar(20) NOT NULL,
  `is_main` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_site`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Vypisuji data pro tabulku `{PREFIX}sites`
--

INSERT INTO `{PREFIX}sites` (`id_site`, `domain`, `dir`, `table_prefix`, `is_main`) VALUES
(1, 'www', NULL, '{PREFIX}', 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}sites_groups`
--

DROP TABLE IF EXISTS `{PREFIX}sites_groups`;
CREATE TABLE IF NOT EXISTS `{PREFIX}sites_groups` (
  `id_site` smallint(6) NOT NULL,
  `id_group` int(11) NOT NULL,
  KEY `id_site` (`id_site`,`id_group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='tabulka propojení webů se skupinami adminů';

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}templates`
--

DROP TABLE IF EXISTS `{PREFIX}templates`;
CREATE TABLE IF NOT EXISTS `{PREFIX}templates` (
  `id_template` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(400) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `content` text,
  `type` varchar(20) NOT NULL DEFAULT 'text',
  `time_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_template`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
  PRIMARY KEY (`id_user`,`username`),
  KEY `id_group` (`id_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Vypisuji data pro tabulku `{PREFIX}users`
--

INSERT INTO `{PREFIX}users` (`id_user`, `username`, `password`, `password_restore`, `id_group`, `name`, `surname`, `mail`, `note`, `blocked`, `foto_file`, `deleted`, `created`, `last_login`) VALUES
(2, 'guest', NULL, NULL, 2, 'test', 'tetasdhf', '', NULL, 0, NULL, 0, NULL, NULL),
(1, 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', NULL, 1, 'admin', 'admin', '', NULL, 0, NULL, 0, NULL, NULL);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Vypisuji data pro tabulku `{PREFIX}users_logins`
--

INSERT INTO `{PREFIX}users_logins` (`id_user_login`, `id_user`, `user_login_ip`, `user_login_browser`, `user_login_time`) VALUES
(1, 1, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/28.0.1500.71 Chrome/28.0.1500.71 Safari/537.36', '2013-08-10 10:47:33');

-- --------------------------------------------------------

--
-- Struktura tabulky `cubecms_global_config`
--

DROP TABLE IF EXISTS `cubecms_global_config`;
CREATE TABLE IF NOT EXISTS `cubecms_global_config` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=131 ;

--
-- Vypisuji data pro tabulku `cubecms_global_config`
--

INSERT INTO `cubecms_global_config` (`id_config`, `key`, `label`, `value`, `values`, `protected`, `type`, `id_group`, `callback_func`, `hidden_value`) VALUES
(1, 'DEFAULT_ID_GROUP', NULL, '2', NULL, 0, 'number', 3, NULL, 0),
(2, 'DEFAULT_GROUP_NAME', NULL, 'guest', NULL, 0, 'string', 3, NULL, 0),
(3, 'DEFAULT_USER_NAME', NULL, 'anonym', NULL, 0, 'string', 3, NULL, 0),
(4, 'APP_LANGS', 'Všechny vybrané jazyky aplikace', 'cs', 'cs;en;de;ru;sk', 0, 'listmulti', 8, NULL, 0),
(5, 'DEFAULT_APP_LANG', 'Výchozí jazyk aplikace. Tento jazyk je potom u většiny položek povinný.', 'cs', 'cs;en;de;ru;sk', 0, 'list', 8, NULL, 0),
(6, 'IMAGES_DIR', NULL, 'images', NULL, 0, 'string', 3, NULL, 0),
(7, 'IMAGES_LANGS_DIR', NULL, 'langs', NULL, 0, 'string', 3, NULL, 0),
(8, 'DEBUG_LEVEL', 'Režim ladění stránek (0 pro vypnutí)', '2', NULL, 0, 'number', 3, NULL, 0),
(9, 'TEMPLATE_FACE', 'Název vzhledu stránek', 'default', NULL, 0, 'string', 4, NULL, 0),
(10, 'SITEMAP_PERIODE', 'Výchozí položka pro změnu mapy stránek pro vyhledávače', 'weekly', NULL, 0, 'string', 5, NULL, 0),
(11, 'SEARCH_RESULT_LENGHT', 'Délka řetězce s výsledkem hledání', '300', NULL, 0, 'number', 9, NULL, 0),
(12, 'SEARCH_HIGHLIGHT_TAG', 'Název tagu, který se užívá pro zvýraznění slova ve výsledcích hledání', 'strong', NULL, 0, 'string', 9, NULL, 0),
(13, 'SESSION_NAME', 'Název cookies s id session, která se ukládá u klienta', '{PREFIX}cookie', NULL, 0, 'string', 3, NULL, 0),
(14, 'WEB_NAME', 'Název stránek', 'VVE Engine', NULL, 0, 'string', 2, NULL, 0),
(61, 'CATEGORIES_STRUCTURE', NULL, 'O:18:"Category_Structure":7:{s:25:"\0Category_Structure\0level";i:0;s:22:"\0Category_Structure\0id";i:0;s:28:"\0Category_Structure\0idParent";N;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:1:{i:0;O:18:"Category_Structure":7:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";i:1;s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}s:30:"\0Category_Structure\0withHidden";b:0;s:4:"type";s:4:"main";}}s:30:"\0Category_Structure\0withHidden";b:0;s:4:"type";s:4:"main";}', NULL, 1, 'ser_object', 1, NULL, 0),
(58, 'USE_GLOBAL_ACCOUNTS_TB_PREFIXES', 'Prefixy tabulek pro které se má použít globální systém přihlašování', '{PREFIX}', '', 0, 'string', 3, NULL, 0),
(59, 'NAVIGATION_MENU_TABLE', 'Název tabulky s navigačním menu', '{PREFIX}navigation_panel', NULL, 0, 'string', 3, NULL, 0),
(60, 'SHARES_TABLE', 'Název tabulky s odkazy na sdílení (při global)', '{PREFIX}shares', NULL, 0, 'string', 3, NULL, 0),
(21, 'PAGE_TITLE_SEPARATOR', 'Oddělovač položek v nadpisu stránky', '|', NULL, 0, 'string', 4, NULL, 0),
(16, 'NAVIGATION_SEPARATOR', 'Oddělovač položek v navigaci mezi kategoriemi', '::', NULL, 0, 'string', 4, NULL, 0),
(119, 'USE_CATEGORY_ALT_IN_TITLE', 'Použít alternativní název kategorie v titulku stránky', 'false', NULL, 0, 'bool', 3, NULL, 0),
(19, 'PANEL_TYPES', 'Jaké druhy panelů jsou zapnuty a povoleny (musí je implementovat šablona)', 'left;right;bottom', 'left;right;bottom;top;center', 0, 'listmulti', 4, NULL, 0),
(97, 'FTP_HOST', 'Adresa ftp serveru, kde jsou stránky nahrány', 'localhost', NULL, 0, 'string', 3, NULL, 0),
(98, 'FTP_PORT', 'Port ftp serveru, kde jsou stránky nahrány', '22', NULL, 0, 'number', 3, NULL, 0),
(20, 'DATA_DIR', NULL, 'data', NULL, 0, 'string', 3, NULL, 0),
(22, 'USE_GLOBAL_ACCOUNTS', 'Globální systém přihlašování', 'false', NULL, 0, 'bool', 3, NULL, 0),
(23, 'GLOBAL_TABLES_PREFIX', 'Prefix globálních tabulek', '{PREFIX}', NULL, 0, 'string', 3, NULL, 0),
(27, 'PDF_PAGE_FORMAT', 'Formát stránky pro pdf výstup', 'A4', NULL, 0, 'string', 5, NULL, 0),
(28, 'PDF_PAGE_ORIENTATION', 'Natočení stránky pro pdf výstup (P=portrait, L=landscape)', 'P', 'P;L', 0, 'list', 5, NULL, 0),
(29, 'PDF_CREATOR', 'Název pdf kreatoru', 'TCPDF', NULL, 0, 'string', 5, NULL, 0),
(30, 'PDF_AUTHOR', 'Autor pdf', 'TCPDF', NULL, 0, 'string', 5, NULL, 0),
(31, 'PDF_HEADER_LOGO', 'Název loga v hlavičce pdf', NULL, NULL, 0, 'string', 5, NULL, 0),
(32, 'PDF_HEADER_LOGO_WIDTH', 'Šířka loga v hlavičce', NULL, NULL, 0, 'string', 5, NULL, 0),
(33, 'PDF_UNIT', 'Jednotky použité u pdf (pt=point, mm=millimeter, cm=centimeter, in=inch)', 'mm', 'mm;pt;cm;in', 0, 'list', 5, NULL, 0),
(34, 'PDF_MARGIN_HEADER', 'Odsazení hlavičky', '5', NULL, 0, 'string', 5, NULL, 0),
(35, 'PDF_MARGIN_FOOTER', 'Odsazení zápatí', '10', NULL, 0, 'string', 5, NULL, 0),
(36, 'PDF_MARGIN_TOP', 'Odsazení stránky z vrchu', '20', NULL, 0, 'string', 5, NULL, 0),
(37, 'PDF_MARGIN_BOTTOM', 'Odsazení stránky od spodu', '25', NULL, 0, 'string', 5, NULL, 0),
(38, 'PDF_MARGIN_LEFT', 'Odsazení z leva', '15', NULL, 0, 'string', 5, NULL, 0),
(39, 'PDF_MARGIN_RIGHT', 'Odsazení z prava', '15', NULL, 0, 'string', 5, NULL, 0),
(40, 'PDF_FONT_NAME_MAIN', 'Název hlavního fontu', 'arial', NULL, 0, 'string', 5, NULL, 0),
(41, 'PDF_FONT_SIZE_MAIN', 'Velikost hlavního fontu', '10', NULL, 0, 'string', 5, NULL, 0),
(42, 'PDF_FONT_NAME_DATA', 'Font pro data', 'arial', NULL, 0, 'string', 5, NULL, 0),
(43, 'PDF_FONT_SIZE_DATA', 'Velikost fontu pro data', '6', NULL, 0, 'string', 5, NULL, 0),
(44, 'PDF_FONT_MONOSPACED', 'Název pevného fontu', 'courier', NULL, 0, 'string', 5, NULL, 0),
(45, 'PDF_IMAGE_SCALE_RATIO', 'Zvětšení obrázků ve výstupním pdf', '1', NULL, 0, 'string', 5, NULL, 0),
(46, 'HEAD_MAGNIFICATION', 'zvětšovací poměr nadpisů', '1.1', NULL, 0, 'string', 5, NULL, 0),
(51, 'WEB_DESCRIPTION', 'Popis stránek', 'Web Pages', NULL, 0, 'string', 2, NULL, 0),
(50, 'FEED_NUM', 'Poček generovaných rss/atom kanálů', '10', NULL, 0, 'number', 5, NULL, 0),
(52, 'WEB_MASTER_NAME', 'Jméno webmastera', 'Webmaster Name', NULL, 0, 'string', 2, NULL, 0),
(53, 'WEB_MASTER_EMAIL', 'E-mail webmastera', 'webmaster@web.com', NULL, 0, 'string', 6, NULL, 0),
(54, 'FEED_TTL', 'Počet minut kešování kanálu', '30', NULL, 0, 'number', 5, NULL, 0),
(55, 'WEB_COPYRIGHT', 'Copyright poznámka k webu ({Y} - nahrazeno rokem)', 'Obsah toho webu je licencován podle ... Žádná s jeho částí nesmí být použita bez vědomí webmastera. Copyrigth {Y}', NULL, 0, 'string', 2, NULL, 0),
(56, 'SEARCH_ARTICLE_REL_MULTIPLIER', 'Násobič pro relevanci nadpisu článku (1 - nekonečno)', '5', NULL, 0, 'number', 9, NULL, 0),
(57, 'ADMIN_MENU_STRUCTURE', 'Administrační menu', '', NULL, 1, 'ser_object', 1, NULL, 0),
(62, 'MAIN_PAGE_TITLE', 'Nadpis hlavní stránky', 'Main Title Page', NULL, 0, 'string', 2, NULL, 0),
(63, 'ALLOW_EXTERNAL_JS', 'Povolení externích JavaScript souborů', 'true', NULL, 0, 'bool', 3, NULL, 0),
(74, 'CM_SITEMAP_MAX_ITEMS', 'Maximální počet položek v mapě stránek (pro vyhledávače)', '50', NULL, 0, 'number', 5, NULL, 0),
(73, 'CM_SITEMAP_MAX_ITEMS_PAGE', 'Maximální počet položek v mapě stránek', '20', NULL, 0, 'number', 4, NULL, 0),
(75, 'CM_SITEMAP_CAT_ICON', 'Název ikony pro sitemap', 'sitemap.png', NULL, 0, 'string', 4, NULL, 0),
(76, 'CM_ERR_CAT_ICON', 'Název ikony pro chybovou stránku', 'error.png', NULL, 0, 'string', 4, NULL, 0),
(77, 'CM_RSS_CAT_ICON', 'Název ikony pro stránku s rss kanály', 'rsslist.png', NULL, 0, 'string', 4, NULL, 0),
(78, 'LOGIN_TIME', 'Doba po které je uživatel automaticky odhlášen (s)', '3600', NULL, 0, 'number', 3, NULL, 0),
(79, 'IMAGE_THUMB_W', 'Výchozí šířka miniatury', '500', NULL, 0, 'number', 7, NULL, 0),
(80, 'IMAGE_THUMB_H', 'Výchozí výška miniatury', '500', NULL, 0, 'number', 7, NULL, 0),
(81, 'SMTP_SERVER', 'Adresa smtp serveru pro odesílání pošty', 'localhost', NULL, 0, 'string', 6, NULL, 0),
(82, 'SMTP_SERVER_PORT', 'Port smtp serveru pro odesílání pošty', NULL, NULL, 0, 'number', 6, NULL, 0),
(83, 'SMTP_SERVER_USERNAME', 'Uživatelské jméno smtp serveru pro odesílání pošty', NULL, NULL, 0, 'string', 6, NULL, 0),
(84, 'SMTP_SERVER_PASSWORD', 'Uživatelské heslo smtp serveru pro odesílání pošty', NULL, NULL, 0, 'string', 6, NULL, 1),
(85, 'SHORT_TEXT_TAGS', 'tagy, které jsou povoleny ve zkrácených výpisech', '<strong><a><em><span>', NULL, 0, 'string', 3, NULL, 0),
(86, 'NOREPLAY_MAIL', 'Název schránky odesílané pošty', 'noreplay@web.com', NULL, 0, 'string', 6, NULL, 0),
(94, 'TOKENS_STORE', 'Kde se mají ukládat bezpečnostní tokeny', 'db', 'session;db;file', 0, 'list', 3, NULL, 0),
(88, 'DEFAULT_PHOTO_W', 'Výchozí šířka fotky', '1024', NULL, 0, 'number', 7, NULL, 0),
(89, 'DEFAULT_PHOTO_H', 'Výchozí výška fotky', '768', NULL, 0, 'number', 7, NULL, 0),
(90, 'STORE_ORIGINAl_FILES', 'Ukládání originálních souborů', '1', NULL, 0, 'bool', 7, NULL, 0),
(91, 'JQUERY_THEME', 'Téma JQuery UI', 'base', NULL, 0, 'string', 4, NULL, 0),
(93, 'IMAGE_THUMB_CROP', 'Ořezávat miniatury', '1', NULL, 0, 'bool', 7, NULL, 0),
(95, 'MAIN_TPL_VIEWS', 'Vzhledy hlavní šablony', NULL, NULL, 0, 'string', 4, NULL, 0),
(96, 'PIROBOX_THEME', 'Téma JsPluginu Pirobox', 'white', 'black;blackwhite;shadow;white;whiteblack', 0, 'list', 4, NULL, 0),
(99, 'FTP_USER', 'Uživatel ftp serveru, kde jsou stránky nahrány', 'user', NULL, 0, 'string', 3, NULL, 0),
(100, 'FTP_PASSOWRD', 'Heslo uživatele ftp serveru, kde jsou stránky nahrány', NULL, NULL, 0, 'string', 3, NULL, 1),
(101, 'USE_IMAGEMAGICK', 'Jeslti se má používat knihovna Imagick pro práci s obrázky', '0', NULL, 0, 'bool', 3, NULL, 0),
(102, 'SUB_SITE_DOMAIN', 'Doména podstránek', NULL, NULL, 0, 'string', 1, NULL, 0),
(103, 'SUB_SITE_DIR', 'Adresár s podstránkami', NULL, NULL, 0, 'string', 1, NULL, 0),
(104, 'SUB_SITE_USE_HTACCESS', 'Jestli je pro subdomény použit htaccess', '0', NULL, 0, 'bool', 1, NULL, 0),
(105, 'MAIN_SITE_TABLE_PREFIX', 'Prefix tabulek hlavních stránek (některé moduly využívají globální tabulky)', NULL, NULL, 0, 'string', 1, NULL, 0),
(106, 'SMTP_SERVER_ENCRYPT', 'Šifrování spojení k SMTP serveru (tls, ssl)', NULL, NULL, 0, 'string', 6, NULL, 0),
(107, 'ARTICLES_IN_LIST', 'Výchozí počet článků na jednu stránku', '5', NULL, 0, 'number', 4, NULL, 0),
(108, 'ARTICLE_TITLE_IMG_W', 'Titulní obrázek článku - šířka', '1024', NULL, 0, 'number', 7, NULL, 0),
(109, 'ARTICLE_TITLE_IMG_H', 'Titulní obrázek článku - výška', '1024', NULL, 0, 'number', 7, NULL, 0),
(110, 'ARTICLE_TITLE_IMG_DIR', 'Titulní obrázek článku - adresář', 'title-images', NULL, 0, '', 7, NULL, 0),
(111, 'FCB_APP_ID', 'Facebook App ID (pokud nějáká existuje)', NULL, NULL, 0, 'string', 11, NULL, 0),
(112, 'FCB_PAGE_URL', 'Adresa stránky/skupiny na Facebooku', NULL, NULL, 0, 'string', 11, NULL, 0),
(113, 'FCB_ADMINS', 'Facebook administrátoři komentářů (ID uživatelů oddělené čárkou)', NULL, NULL, 0, 'string', 11, NULL, 0),
(114, 'FCB_SHOW_LIKE_THIS_BUTTON', 'Zobrazit tlačítko "Like this" Facebooku', '1', NULL, 0, 'bool', 11, NULL, 0),
(115, 'GOOGLE_ANALYTICS_CODE', 'Kód pro Google Analytics', NULL, NULL, 0, 'string', 11, NULL, 0),
(116, 'GOOGLE_SHOW_PLUS_BUTTON', 'Zobrazit tlačítko Google +1', '1', NULL, 0, 'bool', 11, NULL, 0),
(117, 'SHARE_TOOLS_BUTTON_SHOW', 'Zobrazit tlačítko sdílení pomocí ostatních služeb', '1', NULL, 0, 'bool', 11, NULL, 0),
(118, 'IMAGE_COMPRESS_QUALITY', 'kvalita komprese obrázků', '90', NULL, 0, 'number', 7, NULL, 0),
(120, 'FCB_APP_SECRET_KEY', 'Facebook App Secret Key', NULL, NULL, 0, 'string', 11, NULL, 1),
(121, 'FCB_PAGE_ID', 'ID stránky/skupiny na Facebooku', NULL, NULL, 0, 'string', 11, NULL, 0),
(122, 'FCB_ACCESS_TOKEN', 'Access token pro přístup k Facebooku', NULL, NULL, 0, 'string', 11, NULL, 1),
(123, 'ARTICLE_TITLE_IMG_C', 'Ořezávat titulní obrázky', 'true', NULL, 0, 'bool', 7, NULL, 0),
(124, 'CACHE_TEXT_IMAGES', 'Zapnutí kešování obrázků v textu', 'true', NULL, 0, 'bool', 7, NULL, 0),
(125, 'CACHE_TEXT_IMAGES_CROP', 'Ořezání kešovaného obrázku při zadání obou rozměrů', 'false', NULL, 0, 'bool', 7, NULL, 0),
(126, 'MEMCACHE_SERVER', 'MemCache server - adresa', NULL, NULL, 0, 'string', 3, NULL, 0),
(127, 'MEMCACHE_PORT', 'MemCache server - port', NULL, NULL, 0, 'number', 3, NULL, 0),
(128, 'ANALYTICS_DISABLED_HOSTS', 'IP adresy pro které je analýza stránek vypnuta (odělené čárkou)', '127.0.0.1', NULL, 0, 'string', 11, NULL, 0),
(129, 'ENABLE_LANG_AUTODETECTION', 'Zapnutí autodetekce jazyka', 'false', NULL, 0, 'bool', 8, NULL, 0),
(130, 'DEFAULT_LANG_SUBSTITUTION', 'Nahrazovat jazyk výchozím jazykem', 'false', NULL, 0, 'bool', 8, NULL, 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `cubecms_global_config_groups`
--

DROP TABLE IF EXISTS `cubecms_global_config_groups`;
CREATE TABLE IF NOT EXISTS `cubecms_global_config_groups` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- Vypisuji data pro tabulku `cubecms_global_config_groups`
--

INSERT INTO `cubecms_global_config_groups` (`id_group`, `name_cs`, `name_sk`, `name_en`, `name_de`, `desc_cs`, `desc_sk`, `desc_en`, `desc_de`) VALUES
(1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'Základní nastavení', 'Základné nastavenie', 'Basic settings', 'Grundeinstellungen', 'Základní nastavení aplikace', 'Základné nastavenia aplikácie', 'Basic settings', NULL),
(3, 'Pokročilá nastavení', 'Rozšírené nastavenia', 'Advanced settings', 'Erweiterte Einstellungen', 'Nastavení chování jádra (přihlášení, subdomény, atd.)', 'Nastavenie správania jadra (prihlásení, subdomény, atď)', 'Adjustment of the Kernel (login, subdomains, etc.)', NULL),
(4, 'Vzhled', 'Vzhľad', 'Appearance', 'Aussehen', 'Nastavení vzhledu stránek', 'Nastavenie vzhľadu stránok', 'Setting up of site', NULL),
(6, 'E-maily', 'E-maily', 'E-mails', 'E-Mails', 'Nastavení e-mailových služeb', 'Nastavenie e-mailových služieb', 'Setting up e-mail service', NULL),
(7, 'Obrázky', 'Obrázky', 'Images', 'Bilder', 'Nastavení obrázků (velikost)', 'Nastavenie obrázkov (veľkosť)', 'Picture settings (size)', NULL),
(8, 'Lokalizace a jazyky', 'Lokalizácia a jazyky', 'Localization and languages', 'Ortsbestimmung und Sprachen', 'Nastavení jazyků prostředí a lokalizace aplikace', 'Nastavenie jazykov prostredia a lokalizácia aplikácie', 'The language environment and positioning applications', NULL),
(9, 'Hledání', 'Hľadanie', 'Search', 'Suche', 'Nastavení výsledků hledání', 'Nastavenie výsledkov hľadania', 'Search Settings', NULL),
(5, 'Exporty', 'Exporty', 'Exports', 'Exporte', 'Nastavení exportů (rss, pdf, ...)', 'Nastavenie exportov (rss, pdf, ...)', 'Export Settings (RSS, PDF, ...)', NULL),
(10, 'E-Shop nastavení', NULL, NULL, NULL, 'Nastavení elektronického obchodu. Toto nastavení je lépe upravovat přímo v nastavení obchodu.', NULL, NULL, NULL),
(11, 'Soc. sítě/analýza', 'Soc. sítě/analýza', 'Soc. Networks/Analysis', NULL, 'Nastavení sociálních sítí a analytických nástrojů. (např. Facebook, Google Analytics,...)', NULL, NULL, NULL),
(20, 'Moduly', 'Moduly', 'Modules', 'Module', 'Nastavení modulů', 'Nastavenie modulov', 'Modules Settings', NULL);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
