
CREATE TABLE IF NOT EXISTS `{PREFIX}journals` (
  `id_journal` smallint(6) NOT NULL AUTO_INCREMENT,
  `journal_number` smallint(6) NOT NULL,
  `journal_year` smallint(6) NOT NULL,
  `journal_file` varchar(100) DEFAULT NULL,
  `journal_text` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `journal_viewed` INT NULL DEFAULT '0',
  PRIMARY KEY (`id_journal`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{PREFIX}journals_labels` (
  `id_label` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_journal` smallint(6) NOT NULL,
  `journal_label` varchar(500) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `journal_page` smallint(6) NOT NULL,
  PRIMARY KEY (`id_label`),
  KEY `id_journal` (`id_journal`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
