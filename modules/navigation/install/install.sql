CREATE TABLE IF NOT EXISTS `{PREFIX}texts` (
  `id_text` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `id_item` smallint(5) unsigned NOT NULL,
  `subkey` varchar(30) NOT NULL DEFAULT 'nokey',
  `label_cs` varchar(200) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `text_cs` mediumtext CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `text_clear_cs` mediumtext CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `label_sk` varchar(200) CHARACTER SET utf8 COLLATE utf8_slovak_ci DEFAULT NULL,
  `text_sk` mediumtext CHARACTER SET utf8 COLLATE utf8_slovak_ci,
  `text_clear_sk` mediumtext CHARACTER SET utf8 COLLATE utf8_slovak_ci,
  `label_en` varchar(200) DEFAULT NULL,
  `text_en` mediumtext,
  `text_clear_en` mediumtext,
  `label_de` varchar(200) DEFAULT NULL,
  `text_de` mediumtext,
  `text_clear_de` mediumtext,
  `changed` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_text`),
  UNIQUE KEY `id_article` (`id_item`),
  FULLTEXT KEY `label_cs` (`label_cs`),
  FULLTEXT KEY `label_en` (`label_en`),
  FULLTEXT KEY `label_de` (`label_de`),
  FULLTEXT KEY `text_clear_de` (`text_clear_de`),
  FULLTEXT KEY `text_clear_en` (`text_clear_en`),
  FULLTEXT KEY `text_clear_cs` (`text_clear_cs`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{PREFIX}texts_has_private_users` (
  `id_user` smallint(6) NOT NULL,
  `id_text` smallint(6) NOT NULL,
  PRIMARY KEY (`id_user`,`id_article`),
  KEY `fk_tb_users_id_user` (`id_user`),
  KEY `fk_tb_texts_id_text` (`id_text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;