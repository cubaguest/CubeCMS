CREATE TABLE `{PREFIX}people` (
  `id_person` int(11) NOT NULL AUTO_INCREMENT,
  `id_category` int(11) NOT NULL,
  `person_name` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `person_surname` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `person_degree` varchar(10) COLLATE utf8_czech_ci DEFAULT NULL,
  `person_degree_after` varchar(10) COLLATE utf8_czech_ci DEFAULT NULL,
  `person_text` text COLLATE utf8_czech_ci,
  `person_text_clear` text COLLATE utf8_czech_ci,
  `person_order` smallint(6) DEFAULT '0',
  `person_image` varchar(45) COLLATE utf8_czech_ci DEFAULT NULL,
  `person_age` int(11) DEFAULT NULL,
  `person_label` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id_person`),
  KEY `id_category` (`id_category`),
  FULLTEXT KEY `name` (`person_name`),
  FULLTEXT KEY `surname` (`person_surname`),
  FULLTEXT KEY `text_clear` (`person_text_clear`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
