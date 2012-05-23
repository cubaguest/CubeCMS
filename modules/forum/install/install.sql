-- --------------------------------------------------------
--
-- Struktura tabulky `forum_messages`
--

CREATE TABLE `{PREFIX}forum_messages` (
  `id_message` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_topic` smallint(6) NOT NULL,
  `id_parent_message` int(11) DEFAULT '0',
  `message_email` varchar(50) NOT NULL,
  `message_created_by` varchar(50) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `message_created_by_moderator` tinyint(1) NOT NULL DEFAULT '0',
  `message_id_user` smallint(6) DEFAULT NULL,
  `message_www` varchar(200) DEFAULT NULL,
  `message_name` varchar(200) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `message_text` text CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `message_text_clear` text CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `message_ip_address` varchar(15) NOT NULL,
  `message_date_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `message_censored` tinyint(1) DEFAULT '0',
  `message_order` int(11) DEFAULT '0',
  `message_depth` int(11) DEFAULT '0',
  `message_reaction_send_notify` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_message`),
  KEY `id_topic` (`id_topic`),
  KEY `post_id_user` (`message_id_user`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

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
  `notification_email` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id_topic`),
  KEY `id_category` (`id_category`),
  KEY `id_user` (`id_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
