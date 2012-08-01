CREATE  TABLE IF NOT EXISTS `{PREFIX}forum_attachments` (
  `id_forum_attachment` int(11) NOT NULL AUTO_INCREMENT,
  `id_topic` int(11) NOT NULL,
  `id_message` int(11) DEFAULT '0',
  `forum_attachment_filename` varchar(100) DEFAULT NULL,
  `forum_attachment_date_add` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `id_user` smallint(6) DEFAULT '0',
  `cube_cms_forum_attachmentscol` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_forum_attachment`),
  KEY `id_topic` (`id_topic`),
  KEY `ind_topic_message` (`id_topic`,`id_message`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
