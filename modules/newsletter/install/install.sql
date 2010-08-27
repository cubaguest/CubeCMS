--
-- Struktura tabulky modulu newsletters `newsletter_mails`
--
--  `id_category` smallint(6) NOT NULL,

CREATE TABLE IF NOT EXISTS `{PREFIX}newsletter_mails` (
  `id_mail` smallint(6) NOT NULL AUTO_INCREMENT,
  `mail` varchar(100) NOT NULL,
  `ip_address` varchar(15) DEFAULT NULL,
  `group` varchar(10) DEFAULT NULL,
  `date_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `blocked` boolean NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_mail`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;