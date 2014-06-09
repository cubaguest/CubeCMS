CREATE TABLE IF NOT EXISTS `{PREFIX}links_list` (
  `id_link` int(11) NOT NULL AUTO_INCREMENT,
  `id_category` int(11) DEFAULT NULL,
  `link_title` varchar(100) NOT NULL,
  `link_target` varchar(200) DEFAULT NULL,
  `link_order` smallint(6) DEFAULT '1',
  `link_category` int(11) DEFAULT '0',
  `link_external` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id_link`),
  KEY `idc` (`id_category`),
  KEY `cat` (`link_category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
