CREATE TABLE IF NOT EXISTS `{PREFIX}people` (
  `id_person` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_category` smallint(6) NOT NULL,
  `name` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `surname` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `degree` varchar(10) COLLATE utf8_czech_ci DEFAULT NULL,
  `degree_after` VARCHAR(10) NULL DEFAULT NULL,
  `text` text CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `text_clear` text CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `order` smallint(6) DEFAULT '0',
  `deleted` tinyint(1) DEFAULT '0',
  `image` varchar(45) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id_person`),
  KEY `id_category` (`id_category`),
  FULLTEXT KEY `name` (`name`),
  FULLTEXT KEY `surname` (`surname`),
  FULLTEXT KEY `text_clear` (`text_clear`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;