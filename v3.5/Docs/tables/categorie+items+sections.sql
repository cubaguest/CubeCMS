-- phpMyAdmin SQL Dump
-- version 2.11.7
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Pátek 22. srpna 2008, 08:07
-- Verze MySQL: 5.0.60
-- Verze PHP: 5.2.6-pl2-gentoo

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

CREATE TABLE IF NOT EXISTS `vypecky_categories` (
  `id_category` smallint(3) NOT NULL auto_increment,
  `id_section` smallint(3) NOT NULL,
  `urlkey` varchar(50) NOT NULL,
  `label_cs` varchar(50) default NULL,
  `alt_cs` varchar(200) default NULL,
  `label_en` varchar(50) default NULL,
  `alt_en` varchar(200) default NULL,
  `label_de` varchar(50) default NULL,
  `alt_de` varchar(200) default NULL,
  `params` varchar(200) default NULL,
  `protected` tinyint(1) NOT NULL default '0',
  `priority` smallint(2) NOT NULL default '0',
  `active` tinyint(1) NOT NULL default '1' COMMENT 'je-li kategorie aktivní',
  `left_panel` tinyint(1) NOT NULL default '1' COMMENT 'Je li zobrazen levý panel',
  `right_panel` tinyint(1) NOT NULL default '1' COMMENT 'Ja li zobrazen pravý panel',
  PRIMARY KEY  (`id_category`),
  KEY `key` (`urlkey`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Vypisuji data pro tabulku `vypecky_categories`
--

INSERT INTO `vypecky_categories` (`id_category`, `id_section`, `urlkey`, `label_cs`, `alt_cs`, `label_en`, `alt_en`, `label_de`, `alt_de`, `params`, `protected`, `priority`, `active`, `left_panel`, `right_panel`) VALUES
(1, 2, 'vypecky', 'Výpečky', 'Vepřové výpečky', NULL, NULL, NULL, NULL, '', 0, 10, 1, 1, 1),
(2, 2, 'portfolio', 'Portfolio', 'Naše portfolio', NULL, NULL, NULL, NULL, '', 0, 9, 1, 0, 1),
(3, 12, 'komiks', 'Komiks', 'Komiks Kuba a Kuba', 'Comics', 'Comics Kuba and Kuba', NULL, NULL, NULL, 0, 5, 1, 0, 0),
(4, 11, 'ucet', 'Účet', 'Účet na výpečkách', 'Account', 'Account on vypecky', NULL, NULL, NULL, 0, 5, 1, 1, 1),
(5, 1, 'forum', 'Fórum Výpeček', 'iframe', NULL, NULL, NULL, NULL, 'id=1', 0, 4, 0, 0, 0),
(6, 7, 'sdilena-data', 'Sdílená data', 'Sdílená data pro potřeby obyvatelů výpeček', NULL, NULL, NULL, NULL, '', 0, 1, 1, 1, 1),
(7, 6, 'email', 'Email', 'Přístup k výpečkovskému Mailu', NULL, NULL, NULL, NULL, '', 0, 1, 1, 0, 0),
(8, 6, 'jak-zije-blahos', 'Jak žije Blahoš', 'Statistiky o Blahošovi', NULL, NULL, NULL, NULL, '', 0, 1, 1, 0, 0),
(9, 8, 'kniha-navstev', 'Kniha návštěv', 'Napište nám', 'Guestbook', 'Our guestbook', NULL, NULL, NULL, 0, 1, 1, 1, 1),
(10, 1, 'tlacenka', 'Tlačenka', 'Tlačenka aneb Blog na výpečkách', NULL, NULL, NULL, NULL, '', 0, 11, 1, 1, 1),
(11, 3, 'fotogalerka', 'Fotogalerka', 'Fotogalerie z akcí a tak', NULL, NULL, NULL, NULL, '', 0, 5, 1, 0, 0),
(12, 5, 'odkazy', 'Odkazy', 'Odkazy na zajímavé stránky', 'Links', 'Links to interesting web pages', NULL, NULL, 'sectiontable=links_section', 0, 5, 1, 1, 1),
(13, 4, 'jitrnicky', 'Jitrničky', 'Jitrničky aneb novinky', NULL, NULL, NULL, NULL, NULL, 0, 5, 1, 1, 1),
(14, 9, 'chyby', 'Chyby', 'Hlášení chyb výpečkovského enginu', 'Errors', 'Reporting errors', NULL, NULL, NULL, 0, 0, 1, 1, 1),
(15, 2, 'sponsors', 'Sponzoři', 'Naši sponzoři', 'Sponsors', 'Our sponsors', NULL, NULL, NULL, 0, 0, 1, 1, 1),
(16, 13, 'portalusers', 'Uživatelé', 'Uživatelé portálu', 'Users', 'Portal Users', NULL, NULL, NULL, 0, 0, 1, 1, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_items`
--

CREATE TABLE IF NOT EXISTS `vypecky_items` (
  `id_item` smallint(6) NOT NULL auto_increment,
  `label` varchar(30) default NULL,
  `alt` varchar(100) default NULL,
  `group_admin` enum('r--','rw-','rwc','r-c','-wc','--c','-w-','---') default 'rwc',
  `group_user` enum('r--','rw-','rwc','r-c','-wc','--c','-w-','---') default 'rw-',
  `group_guest` enum('r--','rw-','rwc','r-c','-wc','--c','-w-','---') default 'r--',
  `group_poweruser` enum('r--','rw-','rwc','r-c','-wc','--c','-w-','---') default 'rwc',
  `scroll` smallint(5) unsigned default '0',
  `comments` tinyint(1) default '0',
  `ratings` tinyint(1) default '0',
  `priority` smallint(6) NOT NULL default '0',
  `id_category` smallint(6) NOT NULL,
  `id_module` smallint(5) unsigned NOT NULL,
  PRIMARY KEY  (`id_item`),
  KEY `id_category` (`id_category`),
  KEY `id_module` (`id_module`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Vypisuji data pro tabulku `vypecky_items`
--

INSERT INTO `vypecky_items` (`id_item`, `label`, `alt`, `group_admin`, `group_user`, `group_guest`, `group_poweruser`, `scroll`, `comments`, `ratings`, `priority`, `id_category`, `id_module`) VALUES
(1, 'Tlačenka', 'Tlačenka aneb blog na Výpečkách', 'rwc', 'rw-', 'r--', 'rwc', 10, 1, 1, 0, 10, 11),
(2, 'Výpečky', 'Naše logo', 'rwc', 'r--', 'r--', 'rwc', 0, 0, 0, 5, 1, 12),
(3, 'Portfolio', 'Portfolio našeho grafika', 'rwc', 'r--', 'r--', 'rwc', 0, 0, 0, 0, 2, 12),
(4, 'Fotogalerie', 'Fotografie z akcí', 'rwc', 'rw-', 'r--', 'rwc', 1, 1, 0, 0, 11, 8),
(5, 'Jitrničky', 'Jitrničky aneb novinky na výpečkách', 'rwc', 'rw-', 'r--', 'rwc', 4, 1, 0, 0, 13, 2),
(6, 'Komiks', 'Komiks Kuba a Kuba', 'rwc', 'r--', 'r--', 'rwc', 1, 0, 1, 0, 3, 13),
(7, 'Úvodní proslov', 'Úvodní slovo ke komiksům', 'rwc', 'r--', 'r--', 'rwc', 0, 0, 0, 5, 3, 1),
(8, 'Odkazy', 'Odkazy', 'rwc', 'rw-', 'r--', 'rwc', 0, 0, 0, 0, 12, 14),
(9, 'Odkazy', NULL, 'rwc', 'r--', 'r--', 'rwc', 0, 0, 0, 0, 12, 1),
(10, 'Jak žije Blahoš', 'Jak si žije Server Blahoš u nás ve sklepě', 'rwc', 'r--', 'r--', 'rwc', 0, 0, 0, 0, 8, 10),
(11, 'O Blahošovi', 'Úvodní text o Blahošovi', 'rwc', 'r--', 'r--', 'rwc', 0, 0, 0, 5, 8, 1),
(12, 'WebMail Výpeček', 'Přístup k Webmailovému klientu Výpeček', 'rwc', 'r--', 'r--', 'rwc', 0, 0, 0, 0, 7, 10),
(13, 'Sdílená data', 'Sdílená data pro potřeby Výpeček', 'rwc', 'r--', 'r--', 'rwc', 0, 0, 0, 0, 6, 10),
(14, 'Naše Data', 'Úvodní slovo ke zdíleným datů', 'rwc', 'rw-', 'r--', 'rwc', 0, 0, 0, 0, 6, 1),
(15, 'Kniha návštěv', 'Kniha návštěv - nebojte se a napište nám', 'rwc', 'rw-', 'r--', 'rwc', 10, 0, 0, 0, 9, 9),
(16, 'Chyby enginu', 'Chyby a opravy v Enginu Výpeček', 'rwc', 'rw-', '---', 'rwc', 0, 0, 0, 0, 14, 15),
(17, 'Můj účet', 'Nastavení mého účtu', 'rwc', 'rw-', 'r--', 'rwc', 0, 0, 0, 0, 4, 4),
(18, 'Sponzoři', 'Naši sponzoři', 'rwc', 'rw-', 'r--', 'rwc', 0, 0, 0, 0, 15, 16),
(19, 'Uživatelé', 'Uživatelé na portálu', 'rwc', 'r--', '---', 'rwc', 20, 0, 0, 0, 16, 17);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_sections`
--

CREATE TABLE IF NOT EXISTS `vypecky_sections` (
  `id_section` smallint(3) NOT NULL auto_increment,
  `label_cs` varchar(50) default NULL,
  `alt_cs` varchar(200) default NULL,
  `label_en` varchar(50) default NULL,
  `alt_en` varchar(200) default NULL,
  `label_de` varchar(50) default NULL,
  `alt_de` varchar(200) default NULL,
  `priority` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`id_section`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Vypisuji data pro tabulku `vypecky_sections`
--

INSERT INTO `vypecky_sections` (`id_section`, `label_cs`, `alt_cs`, `label_en`, `alt_en`, `label_de`, `alt_de`, `priority`) VALUES
(1, 'Tlačenka', NULL, NULL, NULL, NULL, NULL, 100),
(2, 'Výpečky', NULL, NULL, NULL, NULL, NULL, 90),
(3, 'Fotogalerie', NULL, NULL, NULL, NULL, NULL, 80),
(4, 'Jitrničky', NULL, NULL, NULL, NULL, NULL, 70),
(5, 'Odkazy', NULL, NULL, NULL, NULL, NULL, 50),
(6, 'Blahoš', NULL, NULL, NULL, NULL, NULL, 40),
(7, 'Ke stažení', NULL, NULL, NULL, NULL, NULL, 30),
(8, 'Kontakt', NULL, NULL, NULL, NULL, NULL, 20),
(9, 'Chyby', NULL, NULL, NULL, NULL, NULL, 10),
(10, 'Účet', NULL, NULL, NULL, NULL, NULL, 0),
(11, 'Reklama', NULL, NULL, NULL, NULL, NULL, 5),
(12, 'Komiks', 'Komiks Kuba a Kuba', NULL, NULL, NULL, NULL, 60),
(13, 'Správa', 'Správa portálu', 'Settings', 'Portal settings', NULL, NULL, 0);
