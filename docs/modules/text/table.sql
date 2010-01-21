-- phpMyAdmin SQL Dump
-- version 3.2.2.1deb1
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Pondělí 11. ledna 2010, 12:46
-- Verze MySQL: 5.1.37
-- Verze PHP: 5.2.10-2ubuntu6.3

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
-- Struktura tabulky `vypecky_texts`
--

DROP TABLE IF EXISTS `vypecky_texts`;
CREATE TABLE IF NOT EXISTS `vypecky_texts` (
  `id_text` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `id_item` smallint(5) unsigned NOT NULL,
  `subkey` varchar(30) NOT NULL DEFAULT 'NULL',
  `label_cs` varchar(200) DEFAULT NULL,
  `text_cs` mediumtext,
  `text_panel_cs` varchar(1000) DEFAULT NULL,
  `changed` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `label_en` varchar(200) DEFAULT NULL,
  `text_en` mediumtext,
  `text_panel_en` varchar(1000) DEFAULT NULL,
  `label_de` varchar(200) DEFAULT NULL,
  `text_de` mediumtext,
  `text_panel_de` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id_text`),
  UNIQUE KEY `id_article` (`id_item`),
  FULLTEXT KEY `text_cs` (`text_cs`),
  FULLTEXT KEY `text_en` (`text_en`),
  FULLTEXT KEY `text_de` (`text_de`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;
