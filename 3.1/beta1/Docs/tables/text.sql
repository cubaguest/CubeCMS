-- phpMyAdmin SQL Dump
-- version 2.11.0
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Čtvrtek 21. srpna 2008, 18:39
-- Verze MySQL: 5.0.45
-- Verze PHP: 5.2.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Databáze: `dev`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_texts`
--

CREATE TABLE `vypecky_texts` (
  `id_text` smallint(4) unsigned NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `text_cs` mediumtext,
  `changed_time` int(11) default NULL,
  `text_en` mediumtext,
  `text_de` mediumtext,
  PRIMARY KEY  (`id_text`),
  UNIQUE KEY `id_article` (`id_item`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Vypisuji data pro tabulku `vypecky_texts`
--

