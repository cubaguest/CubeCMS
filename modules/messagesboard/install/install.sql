--
-- Struktura tabulky `{PREFIX}messagesboard`
--
CREATE TABLE IF NOT EXISTS `{PREFIX}messagesboard` (
  `id_message` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_category` smallint(6) NOT NULL,
  `id_user` smallint(6) NOT NULL,
  `text` varchar(1000) DEFAULT NULL,
  `text_clear` varchar(1000) DEFAULT NULL,
  `time_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(15) DEFAULT NULL,
  `color` varchar(7) DEFAULT NULL,
  PRIMARY KEY (`id_message`),
  KEY `id_category` (`id_category`,`id_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
