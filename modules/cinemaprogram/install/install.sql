CREATE TABLE IF NOT EXISTS `{PREFIX}cinemaprogram_movies` (
  `id_movie` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `name_orig` varchar(50) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `label` mediumtext CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `label_clear` mediumtext CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `length` int(10) unsigned NOT NULL DEFAULT '0',
  `version` varchar(20) DEFAULT NULL,
  `image` varchar(50) DEFAULT NULL,
  `imdbid` int(10) unsigned DEFAULT NULL,
  `csfdid` int(10) unsigned DEFAULT NULL,
  `critique` varchar(300) DEFAULT NULL,
  `orderlink` varchar(200) DEFAULT NULL,
  `price` smallint(5) unsigned NOT NULL,
  `accessibility` smallint(5) unsigned NOT NULL DEFAULT '0',
  `film_club` tinyint(1) NOT NULL DEFAULT '0',
  `changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_movie`),
  FULLTEXT KEY `name` (`name`),
  FULLTEXT KEY `name_orig` (`name_orig`),
  FULLTEXT KEY `label_clear` (`label_clear`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `{PREFIX}cinemaprogram_time` (
  `id_time` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `id_movie` smallint(5) unsigned NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  PRIMARY KEY (`id_time`),
  KEY `id_movie` (`id_movie`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
