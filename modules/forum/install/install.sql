CREATE TABLE IF NOT EXISTS `{PREFIX}forum_posts` (
  `id_post` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_topic` smallint(6) NOT NULL,
  `post_email` varchar(50) NOT NULL,
  `post_created_by` varchar(50) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `post_created_by_moderator` tinyint(1) NOT NULL DEFAULT '0',
  `post_id_user` smallint(6) DEFAULT NULL,
  `post_www` varchar(200) DEFAULT NULL,
  `post_name` varchar(200) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `post_text` text CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `post_text_clear` text CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `post_ip_address` varchar(15) NOT NULL,
  `post_date_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `post_censored` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_post`),
  KEY `id_topic` (`id_topic`),
  KEY `post_id_user` (`post_id_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `forum_topics`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}forum_topics` (
 `id_topic` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_category` smallint(6) NOT NULL,
  `email` varchar(50) CHARACTER SET utf8 NOT NULL,
  `created_by` varchar(50) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `id_user` smallint(6) DEFAULT NULL,
  `www` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `name` varchar(200) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `text` text CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `text_clear` text CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `solved` tinyint(1) NOT NULL DEFAULT '0',
  `closed` tinyint(1) NOT NULL DEFAULT '0',
  `ip_address` varchar(15) NOT NULL,
  `date_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `views` int(11) NOT NULL DEFAULT '0',
  `notification_email` text,
  PRIMARY KEY (`id_topic`),
  KEY `id_category` (`id_category`),
  KEY `id_user` (`id_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
