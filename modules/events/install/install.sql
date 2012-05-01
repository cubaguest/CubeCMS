CREATE TABLE `{PREFIX}events_cats` (
  `id_events_cat` int(11) NOT NULL AUTO_INCREMENT,
  `id_category` int(11) NOT NULL,
  `event_cat_name` varchar(100) NOT NULL,
  `event_cat_note` varchar(500) DEFAULT NULL,
  `event_cat_image` varchar(100) DEFAULT NULL,
  `event_cat_www` varchar(100) DEFAULT NULL,
  `event_cat_contact` varchar(200) DEFAULT NULL,
  `event_cat_is_public` tinyint(1) NOT NULL DEFAULT '0',
  `event_cat_access_token` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_events_cat`),
  KEY `id_cat` (`id_category`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `{PREFIX}events` (
  `id_event` int(11) NOT NULL AUTO_INCREMENT,
  `id_events_cat` int(11) NOT NULL,
  `event_name` varchar(200) NOT NULL,
  `event_note` varchar(200) DEFAULT NULL,
  `event_text` varchar(500) DEFAULT NULL,
  `event_place` varchar(200) DEFAULT NULL,
  `event_price` int(11) DEFAULT '0',
  `event_contact` varchar(500) DEFAULT NULL,
  `event_date_from` date DEFAULT NULL,
  `event_date_to` date DEFAULT NULL,
  `event_time_from` time DEFAULT NULL,
  `event_time_to` time DEFAULT NULL,
  `event_public` tinyint(1) DEFAULT '1',
  `event_ip_add` int(11) DEFAULT NULL,
  `event_every_day` tinyint(4) DEFAULT NULL,
  `event_date_add` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `event_public_add` tinyint(1) DEFAULT '1',
  `event_is_recommended` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_event`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
