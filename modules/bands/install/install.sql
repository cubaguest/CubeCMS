CREATE TABLE IF NOT EXISTS `{PREFIX}bands` (
  `id_band` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `add_time` datetime NOT NULL,
  `edit_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `id_user_last_edit` smallint(6) DEFAULT NULL,
  `viewed` smallint(6) NOT NULL DEFAULT '0',
  `name` varchar(400) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `text` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `image` varchar(100) DEFAULT NULL,
  `text_clear` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `urlkey` varchar(100) DEFAULT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_band`),
  UNIQUE KEY `urlkey` (`urlkey`),
  FULLTEXT KEY `name` (`name`),
  FULLTEXT KEY `text_clear` (`text_clear`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;