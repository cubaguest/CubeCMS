-- phpMyAdmin SQL Dump
-- version 2.11.7
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Pátek 22. srpna 2008, 16:10
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
-- Struktura tabulky `vypecky_sponsors`
--

CREATE TABLE IF NOT EXISTS `vypecky_sponsors` (
  `id_sponsor` smallint(5) unsigned NOT NULL auto_increment,
  `id_item` smallint(6) NOT NULL,
  `urlkey` varchar(50) NOT NULL,
  `name_cs` varchar(50) NOT NULL,
  `label_cs` varchar(500) default NULL,
  `name_en` varchar(50) default NULL,
  `label_en` varchar(500) default NULL,
  `name_de` varchar(50) default NULL,
  `label_de` varchar(500) default NULL,
  `url` varchar(100) default NULL,
  `logo_image` varchar(100) default NULL,
  `deleted` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id_sponsor`),
  KEY `id_item` (`id_item`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=61 ;

--
-- Vypisuji data pro tabulku `vypecky_sponsors`
--

INSERT INTO `vypecky_sponsors` (`id_sponsor`, `id_item`, `urlkey`, `name_cs`, `label_cs`, `name_en`, `label_en`, `name_de`, `label_de`, `url`, `logo_image`, `deleted`) VALUES
(1, 18, 'deza-as', 'Deza a.s.', '<p>dehtové závody ve Valašském Meziříčí. Tato firma patří mezi osm největších chemiček v České Republice a je naším hlavním sponzorem</p>', 'Deza a.s.', '<p>Corporation</p>', NULL, NULL, 'http://www.deza.cz', '65275b44-b-6-deza-212.jpg', 0),
(2, 18, 'albert-as', 'Albert a.s.', '<p>Malovelkoobchod s potravinami. <span style="font-size: medium;"><strong>Náš sponzor pro stravu!!</strong></span></p>', 'Alber a.s.', NULL, NULL, NULL, 'http://www.albert.cz', 'albert4.gif', 0),
(58, 18, 'deza-as', 'Deza a.s.', NULL, NULL, NULL, NULL, NULL, 'www.deza.cz', '65275b44-b-6-deza-27.jpg', 1),
(60, 18, 'tak-serri', 'Tak šeřří', '<p><span style="color: #ffcc00;"><strong>Albert a.s.</strong></span></p>', NULL, '<p>In english</p>', NULL, NULL, 'http://www.albert.cz', '65275b44-b-6-deza-211.jpg', 1),
(59, 18, 'deza-as', 'Deza a.s.', NULL, NULL, NULL, NULL, NULL, 'http://www.deza.cz', '65275b44-b-6-deza-28.jpg', 1),
(41, 18, 'albert-as', 'Albert a.s.', NULL, NULL, NULL, NULL, NULL, NULL, 'albert.gif', 1),
(57, 18, 'deza-as', 'Deza a.s.', NULL, NULL, NULL, NULL, NULL, 'www.deza.cz', '65275b44-b-6-deza-26.jpg', 1),
(56, 18, 'deza-as', 'Deza a.s.', NULL, NULL, NULL, NULL, NULL, 'www.deza.cz', '65275b44-b-6-deza-25.jpg', 1),
(52, 18, 'deza-as', 'Deza a.s.', NULL, NULL, NULL, NULL, NULL, NULL, '65275b44-b-6-deza-21.jpg', 1),
(51, 18, 'deza-as', 'Deza a.s.', NULL, NULL, NULL, NULL, NULL, NULL, '65275b44-b-6-deza-2.jpg', 1),
(55, 18, 'deza-as', 'Deza a.s.', NULL, NULL, NULL, NULL, NULL, NULL, '65275b44-b-6-deza-24.jpg', 1),
(54, 18, 'deza-as', 'Deza a.s.', NULL, NULL, NULL, NULL, NULL, NULL, '65275b44-b-6-deza-23.jpg', 1),
(53, 18, 'deza-as', 'Deza a.s.', NULL, NULL, NULL, NULL, NULL, NULL, '65275b44-b-6-deza-22.jpg', 1);
