CREATE TABLE `{PREFIX}titlepage_items` (
  `id_item` smallint(6) NOT NULL AUTO_INCREMENT,
  `type` varchar(10) NOT NULL DEFAULT 'text',
  `order` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `columns` tinyint(4) unsigned NOT NULL DEFAULT '1',
  `name` varchar(45) DEFAULT NULL,
  `data` varchar(5000) DEFAULT NULL,
  `id_category` smallint(5) unsigned NOT NULL,
  `id_external` smallint(5) unsigned DEFAULT '0',
  `image` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`id_item`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8