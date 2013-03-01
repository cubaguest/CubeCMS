CREATE TABLE IF NOT EXISTS `{PREFIX}hpslideshow_images` (
  `id_image` int(11) NOT NULL AUTO_INCREMENT,
  `id_category` smallint(6) DEFAULT '0',
  `image_label_cs` varchar(400) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `image_label_en` varchar(400) DEFAULT NULL,
  `image_label_de` varchar(400) DEFAULT NULL,
  `image_label_sk` varchar(400) CHARACTER SET utf8 COLLATE utf8_slovak_ci DEFAULT NULL,
  `image_link_cs` varchar(100) DEFAULT NULL,
  `image_link_en` varchar(100) DEFAULT NULL,
  `image_link_de` varchar(100) DEFAULT NULL,
  `image_link_sk` varchar(100) DEFAULT NULL,
  `image_order` smallint(6) NOT NULL DEFAULT '0',
  `image_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_image`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
