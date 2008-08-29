-- phpMyAdmin SQL Dump
-- version 2.11.7
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Pátek 22. srpna 2008, 19:22
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
-- Struktura tabulky `vypecky_users`
--

CREATE TABLE IF NOT EXISTS `vypecky_users` (
  `id_user` smallint(5) unsigned NOT NULL auto_increment COMMENT 'ID uzivatele',
  `username` varchar(20) NOT NULL COMMENT 'Uzivatelske jmeno',
  `password` varchar(40) default NULL COMMENT 'Heslo',
  `id_group` smallint(3) unsigned default '3',
  `name` varchar(30) NOT NULL,
  `surname` varchar(30) NOT NULL,
  `mail` varchar(50) NOT NULL,
  `note` varchar(500) default NULL,
  `blocked` tinyint(1) NOT NULL default '0',
  `foto_file` varchar(30) default NULL,
  `deleted` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id_user`,`username`),
  KEY `id_group` (`id_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Vypisuji data pro tabulku `vypecky_users`
--

INSERT INTO `vypecky_users` (`id_user`, `username`, `password`, `id_group`, `name`, `surname`, `mail`, `note`, `blocked`, `foto_file`, `deleted`) VALUES
(1, 'admin', '084e0343a0486ff05530df6c705c8bb4', 1, 'Jakub', 'Matas', 'jakubmatas@gmail.com', 'administrátor', 0, NULL, 0),
(2, 'guest', 'guest', 2, 'host', 'host', '', 'host systému', 0, NULL, 0),
(3, 'cuba', '084e0343a0486ff05530df6c705c8bb4', 1, 'Jakub', 'Matas', 'jakubmatas@gmail.com', 'Normální uživatel', 0, 'cuba1.jpg', 0),
(4, 'delamancha', 'delamancha', 4, 'Jakub', 'Vémola', 'j.vemola@gmail.com', 'Jack', 0, 'jack.jpg', 0),
(13, 'slávka', 'SK1Tl7jq', 3, 'Iva', 'Korgerová', 'j.vemola@gmail.com', '', 0, NULL, 0),
(9, 'drobek', 'drobek', 3, 'Pavlík', 'Rybecký', 'ppavelrybecky@tiscali.cz', '', 0, '33-kubaakuba-dvojnik.jpg', 0),
(10, 'jeni013', 'jenicek8', 3, 'Honza', 'Liebel', 'honza.liebel@centrum.cz', '', 0, 'krtecek.jpg', 0),
(11, 'BSBVB', 'oligo', 3, 'Johnie', 'BSBVB', 'drimalt@seznam.cz', 'drimalt@sezman.cz\r\n - když já už sem si zvykl ten mejl dávat dycky dvakrát..', 0, NULL, 0),
(12, 'arivederci', 'h3d27GYQ', 3, 'Kateřina', 'Pardubová', 'katerina.pardubova@gmail.com', '', 0, NULL, 0),
(14, 'Šalvěj', 'dwXgzUFs', 3, 'Pavel', 'Daněk', 'paveldanek@seznam.cz', '', 0, NULL, 0),
(15, 'Šajtr', 'ville', 3, 'Pavel', 'Schreier', 'PavSch@seznam.cz', '', 0, NULL, 0),
(16, 'Zdenda benda', 'cepaj8or', 3, 'Zdenda', 'Kozák', 'zdenda.kozak@seznam.cz', '', 0, NULL, 0),
(17, 'usual_moron', 'usual_moron', 3, 'Michal', 'Čarnický', '', NULL, 0, 'images1.jpg', 0),
(19, 'Mikimyš', 'q4SMvs2f', 3, 'Kateřina', 'Novotná', 'katerinati@seznam.cz', 'Zdravím Výpečky! Lužánecká rulez!', 0, NULL, 0);
