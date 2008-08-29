-- phpMyAdmin SQL Dump
-- version 2.11.7
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Neděle 24. srpna 2008, 20:26
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
-- Struktura tabulky `vypecky_panels`
--

CREATE TABLE IF NOT EXISTS `vypecky_panels` (
  `id_panel` smallint(3) NOT NULL auto_increment,
  `priority` smallint(2) NOT NULL default '0',
  `label` varchar(30) NOT NULL,
  `id_item` smallint(5) unsigned default NULL,
  `position` enum('left','right') NOT NULL default 'left',
  `enable` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id_panel`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Vypisuji data pro tabulku `vypecky_panels`
--

INSERT INTO `vypecky_panels` (`id_panel`, `priority`, `label`, `id_item`, `position`, `enable`) VALUES
(1, 0, 'Nejnovější tlačenka', 1, 'right', 0),
(2, 4, 'Aktuální komiks', 6, 'right', 0),
(3, 5, 'Fotogalerie', 4, 'right', 0),
(4, 10, 'Výpečky', 2, 'left', 0),
(5, 5, 'Účet', 17, 'left', 0),
(6, 0, 'Jitrničky', 5, 'left', 1),
(7, 0, 'Sponzoři', 18, 'right', 1);
