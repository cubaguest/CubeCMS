CREATE TABLE IF NOT EXISTS `{PREFIX}lecturers` (
  `id_lecturer` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE utf8_czech_ci NOT NULL,
  `surname` varchar(20) COLLATE utf8_czech_ci NOT NULL,
  `degree` varchar(10) COLLATE utf8_czech_ci DEFAULT NULL,
  `text` text CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `text_clear` text CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  `image` varchar(45) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id_lecturer`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;