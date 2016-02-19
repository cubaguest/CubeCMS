CREATE TABLE `{PREFIX}custom_blocks_files` (
  `id_block_item` int(11) NOT NULL AUTO_INCREMENT,
  `id_custom_block` int(11) NOT NULL,
  `block_item_index` int(11) NOT NULL DEFAULT '0',
  `block_file_filename` varchar(200) NOT NULL,
  PRIMARY KEY (`id_block_item`),
  KEY `index_id_custom_block` (`id_custom_block`),
  KEY `index_block_item_index` (`block_item_index`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;