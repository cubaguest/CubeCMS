-- phpMyAdmin SQL Dump
-- version 2.11.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 05, 2008 at 05:47 PM
-- Server version: 5.0.60
-- PHP Version: 5.2.6-pl2-gentoo

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `dev`
--

-- --------------------------------------------------------

--
-- Table structure for table `karasinfo_changes`
--

CREATE TABLE IF NOT EXISTS `vypecky_changes` (
  `id_change` smallint(5) unsigned NOT NULL auto_increment,
  `id_user` smallint(5) unsigned NOT NULL,
  `id_item` smallint(5) unsigned NOT NULL,
  `id_article` smallint(5) unsigned NOT NULL,
  `label` varchar(500) default NULL,
  `time` int(11) default NULL,
  PRIMARY KEY  (`id_change`),
  KEY `id_user` (`id_user`,`id_item`,`id_article`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=305 ;
