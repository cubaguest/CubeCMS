CREATE TABLE IF NOT EXISTS `{PREFIX}lecturers` (
  `id_lecturer` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE utf8_czech_ci NOT NULL,
  `surname` varchar(20) COLLATE utf8_czech_ci NOT NULL,
  `degree` varchar(10) COLLATE utf8_czech_ci DEFAULT NULL,
  `degree_after` varchar(10) COLLATE utf8_czech_ci DEFAULT NULL,
  `text` text COLLATE utf8_czech_ci,
  `text_clear` text COLLATE utf8_czech_ci,
  `deleted` tinyint(1) DEFAULT '0',
  `image` varchar(45) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id_lecturer`),
  FULLTEXT KEY `name` (`name`),
  FULLTEXT KEY `surname` (`surname`),
  FULLTEXT KEY `text_clear` (`text_clear`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci ;