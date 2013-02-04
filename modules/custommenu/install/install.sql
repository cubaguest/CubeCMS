CREATE TABLE IF NOT EXISTS `{PREFIX}custom_menu_items` (
  `id_custom_menu_item` int(11) NOT NULL AUTO_INCREMENT,
  `id_category` int(11) NOT NULL DEFAULT '0',
  `menu_item_box` varchar(45) NOT NULL,
  `menu_item_name_cs` varchar(50) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `menu_item_name_en` varchar(50) DEFAULT NULL,
  `menu_item_name_de` varchar(50) DEFAULT NULL,
  `menu_item_name_sk` varchar(50) CHARACTER SET utf8 COLLATE utf8_slovak_ci DEFAULT NULL,
  `menu_item_link` varchar(200) CHARACTER SET latin1 DEFAULT NULL,
  `menu_item_new_window` tinyint(1) DEFAULT '0',
  `menu_item_order` int(11) NOT NULL DEFAULT '0',
  `menu_item_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_custom_menu_item`),
  KEY `fk_category` (`id_category`),
  KEY `box` (`menu_item_box`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
