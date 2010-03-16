CREATE TABLE IF NOT EXISTS `{PREFIX}guestbook` (
  `id_book` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `id_category` smallint(5) unsigned NOT NULL,
  `email` varchar(100) NOT NULL,
  `www` varchar(100) DEFAULT NULL,
  `text` varchar(1100) NOT NULL,
  `nick` varchar(100) NOT NULL,
  `ip_address` varchar(15) DEFAULT NULL,
  `client` varchar(200) DEFAULT NULL,
  `date_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_book`),
  KEY `id_category` (`id_category`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;