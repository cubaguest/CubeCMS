CREATE TABLE IF NOT EXISTS `{PREFIX}templates` (
  `id_template` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(400) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `content` text,
  `type` varchar(20) NOT NULL DEFAULT 'text',
  `time_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_template`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
