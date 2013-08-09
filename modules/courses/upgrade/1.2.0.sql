ALTER TABLE  `{PREFIX}courses` ADD `rss_feed` BOOLEAN NOT NULL DEFAULT  '0',
ADD  `text_private` TEXT CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL AFTER `text`;

CREATE TABLE IF NOT EXISTS `{PREFIX}courses_has_users` (
  `id_user` smallint(6) NOT NULL,
  `id_course` smallint(6) NOT NULL,
  PRIMARY KEY (`id_user`,`id_course`),
  KEY `fk_tb_users_id_user` (`id_user`),
  KEY `fk_tb_courses_id_course` (`id_course`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;