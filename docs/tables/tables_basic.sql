-- phpMyAdmin SQL Dump
-- version 3.1.2
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Čtvrtek 07. května 2009, 18:26
-- Verze MySQL: 5.0.76
-- Verze PHP: 5.2.9-pl2-gentoo

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Databáze: `dev`
--

-- --------------------------------------------------------
-- !!!!!!!!!!!! POZOR PŘEPSAT PREFIXY TABULEK !!!!!!!!!!!!!
-- --------------------------------------------------------

--
-- Struktura tabulky `PREFIX_categories`
--

CREATE TABLE IF NOT EXISTS `PREFIX_categories` (
  `id_category` smallint(3) NOT NULL auto_increment,
  `id_section` smallint(3) NOT NULL,
  `label_cs` varchar(50) default NULL,
  `alt_cs` varchar(200) default NULL,
  `label_en` varchar(50) default NULL,
  `alt_en` varchar(200) default NULL,
  `label_de` varchar(50) default NULL,
  `alt_de` varchar(200) default NULL,
  `cparams` varchar(200) default NULL,
  `protected` tinyint(1) NOT NULL default '0',
  `priority` smallint(2) NOT NULL default '0',
  `active` tinyint(1) NOT NULL default '1' COMMENT 'je-li kategorie aktivní',
  `left_panel` tinyint(1) NOT NULL default '1' COMMENT 'Je li zobrazen levý panel',
  `right_panel` tinyint(1) NOT NULL default '1' COMMENT 'Ja li zobrazen pravý panel',
  `sitemap_changefreq` enum('always','hourly','daily','weekly','monthly','yearly','never') NOT NULL default 'yearly',
  `sitemap_priority` float NOT NULL default '0.1',
  `show_in_menu` tinyint(1) NOT NULL default '1' COMMENT 'Má li se položka zobrazit v menu',
  `show_when_login_only` tinyint(1) NOT NULL default '0' COMMENT 'Jstli má bát položka zobrazena po přihlášení',
  PRIMARY KEY  (`id_category`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `_groups`
--

CREATE TABLE IF NOT EXISTS `PREFIX_groups` (
  `id_group` smallint(3) unsigned NOT NULL auto_increment COMMENT 'ID skupiny',
  `name` varchar(15) default NULL COMMENT 'Nazev skupiny',
  `label` varchar(50) default NULL,
  `used` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Vypisuji data pro tabulku `PREFIX_groups`
--

INSERT INTO `PREFIX_groups` (`id_group`, `name`, `label`, `used`) VALUES
(1, 'admin', 'Administrátor', 1),
(2, 'guest', 'Host', 1),
(3, 'user', 'Uživatel', 1),
(4, 'poweruser', 'uživatel s většími právy', 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `PREFIX_items`
--

CREATE TABLE IF NOT EXISTS `PREFIX_items` (
  `id_item` smallint(6) NOT NULL auto_increment,
  `label_cs` varchar(100) default NULL,
  `alt_cs` varchar(500) default NULL,
  `label_en` varchar(100) default NULL,
  `alt_en` varchar(500) default NULL,
  `label_de` varchar(100) default NULL,
  `alt_de` varchar(500) default NULL,
  `group_admin` enum('r--','rw-','rwc','r-c','-wc','--c','-w-','---') default 'rwc',
  `group_user` enum('r--','rw-','rwc','r-c','-wc','--c','-w-','---') default 'rw-',
  `group_guest` enum('r--','rw-','rwc','r-c','-wc','--c','-w-','---') default 'r--',
  `group_poweruser` enum('r--','rw-','rwc','r-c','-wc','--c','-w-','---') default 'rwc',
  `params` varchar(500) default NULL COMMENT 'parametry pro daný modul itemu - jsouv popsány v docs',
  `priority` smallint(6) NOT NULL default '0',
  `id_category` smallint(6) NOT NULL,
  `id_module` smallint(5) unsigned NOT NULL,
  PRIMARY KEY  (`id_item`),
  KEY `id_category` (`id_category`),
  KEY `id_module` (`id_module`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--

-- --------------------------------------------------------

--
-- Struktura tabulky `PREFIX_modules`
--

CREATE TABLE IF NOT EXISTS `PREFIX_modules` (
  `id_module` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  `mparams` varchar(100) default NULL,
  `datadir` varchar(100) default NULL,
  `dbtable1` varchar(50) default NULL,
  `dbtable2` varchar(50) default NULL,
  `dbtable3` varchar(50) default NULL,
  PRIMARY KEY  (`id_module`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Vypisuji data pro tabulku `PREFIX_modules`
--

INSERT INTO `PREFIX_modules` (`id_module`, `name`, `mparams`, `datadir`, `dbtable1`, `dbtable2`, `dbtable3`) VALUES
(2, 'text', NULL, NULL, 'texts', NULL, NULL),
(1, 'login', NULL, NULL, 'users', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `PREFIX_panels`
--

CREATE TABLE IF NOT EXISTS `PREFIX_panels` (
  `id_panel` smallint(3) NOT NULL auto_increment,
  `priority` smallint(2) NOT NULL default '0',
  `label` varchar(30) NOT NULL,
  `id_item` smallint(5) unsigned default NULL,
  `position` enum('left','right') NOT NULL default 'left',
  `enable` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id_panel`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Struktura tabulky `PREFIX_sections`
--

CREATE TABLE IF NOT EXISTS `PREFIX_sections` (
  `id_section` smallint(3) NOT NULL auto_increment,
  `label_cs` varchar(50) default NULL,
  `alt_cs` varchar(200) default NULL,
  `label_en` varchar(50) default NULL,
  `alt_en` varchar(200) default NULL,
  `label_de` varchar(50) default NULL,
  `alt_de` varchar(200) default NULL,
  `priority` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`id_section`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `PREFIX_users`
--

CREATE TABLE IF NOT EXISTS `PREFIX_users` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Vypisuji data pro tabulku `PREFIX_users`
--

INSERT INTO `PREFIX_users` (`id_user`, `username`, `password`, `id_group`, `name`, `surname`, `mail`, `note`, `blocked`, `foto_file`, `deleted`) VALUES
(1, 'admin', '084e0343a0486ff05530df6c705c8bb4', 1, 'Jakub', 'Matas', 'jakubmatas@gmail.com', 'administrátor', 0, NULL, 0),
(2, 'guest', 'guest', 2, 'host', 'host', '', 'host systému', 0, NULL, 0),
(3, 'cuba', '084e0343a0486ff05530df6c705c8bb4', 1, 'Jakub', 'Matas', 'jakubmatas@gmail.com', 'Normální uživatel', 0, 'cuba1.jpg', 0);
