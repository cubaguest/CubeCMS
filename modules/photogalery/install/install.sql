CREATE TABLE IF NOT EXISTS `{PREFIX}photogalery_images` (
  `id_photo` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `id_article` smallint(5) unsigned DEFAULT NULL,
  `id_category` smallint(5) unsigned NOT NULL,
  `file` varchar(200) NOT NULL,
  `name_cs` varchar(300) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `desc_cs` varchar(1000) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `name_sk` varchar(300) CHARACTER SET utf8 COLLATE utf8_slovak_ci DEFAULT NULL,
  `desc_sk` varchar(1000) CHARACTER SET utf8 COLLATE utf8_slovak_ci DEFAULT NULL,
  `name_en` varchar(300) DEFAULT NULL,
  `desc_en` varchar(1000) DEFAULT NULL,
  `name_de` varchar(300) DEFAULT NULL,
  `desc_de` varchar(1000) DEFAULT NULL,
  `ord` smallint(5) unsigned NOT NULL DEFAULT '0',
  `edit_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_photo`),
  KEY `id_category` (`id_category`),
  KEY `id_article` (`id_article`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
