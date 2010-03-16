CREATE TABLE IF NOT EXISTS `{PREFIX}polls` (
  `id_poll` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_category` smallint(6) NOT NULL,
  `question` varchar(200) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `is_multi` tinyint(1) NOT NULL DEFAULT '0',
  `data` varbinary(1000) NOT NULL,
  `votes` smallint(6) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_poll`),
  KEY `id_cat` (`id_category`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
