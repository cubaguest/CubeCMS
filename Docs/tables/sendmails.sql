-- phpMyAdmin SQL Dump
-- version 2.11.7
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Sobota 01. listopadu 2008, 18:17
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
-- Struktura tabulky `rialto_sendmails`
--

CREATE TABLE IF NOT EXISTS `vypecky_sendmails` (
  `id_mail` smallint(5) unsigned NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `mail` varchar(100) NOT NULL,
  PRIMARY KEY  (`id_mail`),
  KEY `id_item` (`id_item`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_sendmailstexts`
--

CREATE TABLE IF NOT EXISTS `vypecky_sendmailstexts` (
  `id_textmail` smallint(5) unsigned NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `subject` varchar(200) default NULL,
  `text` text,
  `replay_mail` varchar(100) default NULL,
  PRIMARY KEY  (`id_textmail`),
  KEY `id_item` (`id_item`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;
