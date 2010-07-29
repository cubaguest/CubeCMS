CREATE TABLE IF NOT EXISTS `{PREFIX}mails_addressbook` (
  `id_mail` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `surname` varchar(30) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `mail` varchar(100) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `note` varchar(400) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id_mail`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------
--
-- Struktura tabulky `mails_sends`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}mails_sends` (
  `id_mail` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_user` smallint(6) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `recipients` varchar(2000) CHARACTER SET utf8 DEFAULT NULL,
  `subject` varchar(500) CHARACTER SET utf8 DEFAULT NULL,
  `content` text CHARACTER SET utf8,
  `attachments` varchar(500) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id_mail`),
  KEY `id_user` (`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;