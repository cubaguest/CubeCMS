-- phpMyAdmin SQL Dump
-- version 2.11.7
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Pondělí 13. října 2008, 18:32
-- Verze MySQL: 5.0.60
-- Verze PHP: 5.2.6-pl7-gentoo

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
  `sitemap_changefreq` enum('always','hourly','daily','weekly','monthly','yearly','never') NOT NULL default 'yearly',
  `sitemap_priority` float NOT NULL default '0.1',
  PRIMARY KEY  (`id_category`),
  KEY `key` (`urlkey`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Vypisuji data pro tabulku `vypecky_categories`
--

INSERT INTO `vypecky_categories` (`id_category`, `id_section`, `urlkey`, `label_cs`, `alt_cs`, `label_en`, `alt_en`, `label_de`, `alt_de`, `params`, `protected`, `priority`, `active`, `left_panel`, `right_panel`, `sitemap_changefreq`, `sitemap_priority`) VALUES
(1, 2, 'vypecky', 'Výpečky', 'Vepřové výpečky', NULL, NULL, NULL, NULL, '', 0, 10, 1, 1, 1, 'yearly', 0.1),
(2, 2, 'portfolio', 'Portfolio', 'Naše portfolio', NULL, NULL, NULL, NULL, '', 0, 9, 1, 0, 1, 'yearly', 0.1),
(3, 12, 'komiks', 'Komiks', 'Komiks Kuba a Kuba', 'Comics', 'Comics Kuba and Kuba', NULL, NULL, NULL, 0, 5, 1, 0, 0, 'yearly', 0.1),
(4, 11, 'ucet', 'Účet', 'Účet na výpečkách', 'Account', 'Account on vypecky', NULL, NULL, NULL, 0, 5, 1, 1, 1, 'yearly', 0.1),
(5, 1, 'forum', 'Fórum Výpeček', 'iframe', NULL, NULL, NULL, NULL, 'id=1', 0, 4, 0, 0, 0, 'yearly', 0.1),
(6, 7, 'sdilena-data', 'Sdílená data', 'Sdílená data pro potřeby obyvatelů výpeček', NULL, NULL, NULL, NULL, '', 0, 1, 1, 1, 1, 'yearly', 0.1),
(7, 6, 'email', 'Email', 'Přístup k výpečkovskému Mailu', NULL, NULL, NULL, NULL, '', 0, 1, 1, 0, 0, 'yearly', 0.1),
(8, 6, 'jak-zije-blahos', 'Jak žije Blahoš', 'Statistiky o Blahošovi', NULL, NULL, NULL, NULL, '', 0, 1, 1, 0, 0, 'yearly', 0.1),
(9, 8, 'kniha-navstev', 'Kniha návštěv', 'Napište nám', 'Guestbook', 'Our guestbook', NULL, NULL, NULL, 0, 1, 1, 1, 1, 'yearly', 0.1),
(10, 1, 'tlacenka', 'Tlačenka', 'Tlačenka aneb Blog na výpečkách', NULL, NULL, NULL, NULL, '', 0, 11, 1, 1, 1, 'yearly', 0.1),
(11, 3, 'fotogalerka', 'Fotogalerka', 'Fotogalerie z akcí a tak', NULL, NULL, NULL, NULL, '', 0, 5, 1, 1, 1, 'weekly', 0.5),
(12, 5, 'odkazy', 'Odkazy', 'Odkazy na zajímavé stránky', 'Links', 'Links to interesting web pages', NULL, NULL, 'sectiontable=links_section', 0, 5, 1, 1, 1, 'yearly', 0.1),
(13, 4, 'jitrnicky', 'Jitrničky', 'Jitrničky aneb novinky', NULL, NULL, NULL, NULL, NULL, 0, 5, 1, 1, 1, 'yearly', 0.1),
(14, 9, 'chyby', 'Chyby', 'Hlášení chyb výpečkovského enginu', 'Errors', 'Reporting errors', NULL, NULL, NULL, 0, 0, 1, 1, 1, 'yearly', 0.1),
(15, 2, 'sponsors', 'Sponzoři', 'Naši sponzoři', 'Sponsors', 'Our sponsors', NULL, NULL, NULL, 0, 0, 1, 1, 1, 'yearly', 0.1),
(16, 13, 'portalusers', 'Uživatelé', 'Uživatelé portálu', 'Users', 'Portal Users', NULL, NULL, NULL, 0, 0, 1, 1, 1, 'yearly', 0.1);
