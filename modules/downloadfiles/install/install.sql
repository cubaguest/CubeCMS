CREATE TABLE IF NOT EXISTS `{PREFIX}dwfiles` (
  `id_dwfile` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_category` smallint(6) NOT NULL,
  `id_user` smallint(6) NOT NULL,
  `dwfile_name_cs` varchar(200) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `dwfile_text_cs` varchar(500) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `dwfile_name_en` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `dwfile_text_en` varchar(500) CHARACTER SET utf8 DEFAULT NULL,
  `dwfile_name_de` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `dwfile_text_de` varchar(500) CHARACTER SET utf8 DEFAULT NULL,
  `dwfile_name_sk` varchar(200) CHARACTER SET utf8 COLLATE utf8_slovak_ci DEFAULT NULL,
  `dwfile_text_sk` varchar(500) CHARACTER SET utf8 COLLATE utf8_slovak_ci DEFAULT NULL,
  `dwfile` varchar(100) CHARACTER SET utf8 NOT NULL,
  `time_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_dwfile`),
  KEY `id_category` (`id_category`),
  FULLTEXT KEY `dwfile_name_cs` (`dwfile_name_cs`),
  FULLTEXT KEY `dwfile_name_en` (`dwfile_name_en`),
  FULLTEXT KEY `dwfile_name_de` (`dwfile_name_de`),
  FULLTEXT KEY `dwfile_name_sk` (`dwfile_name_sk`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;