CREATE TABLE IF NOT EXISTS `{PREFIX}courses` (
  `id_course` smallint(6) NOT NULL AUTO_INCREMENT,
  `type` varchar(15) COLLATE utf8_czech_ci DEFAULT 'kurz',
  `url_key` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `keywords` varchar(200) COLLATE utf8_czech_ci DEFAULT NULL,
  `description` text COLLATE utf8_czech_ci,
  `text_short` text COLLATE utf8_czech_ci,
  `text` text COLLATE utf8_czech_ci,
  `text_private` text COLLATE utf8_czech_ci,
  `text_clear` text COLLATE utf8_czech_ci,
  `date_start` date DEFAULT NULL,
  `date_stop` date DEFAULT NULL,
  `time_add` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time_edit` datetime DEFAULT NULL,
  `price` smallint(6) DEFAULT NULL,
  `hours_lenght` smallint(6) DEFAULT NULL,
  `seats` smallint(6) DEFAULT '0',
  `seats_blocked` smallint(6) DEFAULT '0',
  `place` text COLLATE utf8_czech_ci,
  `image` varchar(65) COLLATE utf8_czech_ci DEFAULT NULL,
  `is_new` tinyint(1) DEFAULT '0',
  `deleted` tinyint(1) DEFAULT '0',
  `id_user` smallint(5) unsigned NOT NULL DEFAULT '0',
  `allow_registration` tinyint(1) DEFAULT '0',
  `rss_feed` tinyint(1) NOT NULL DEFAULT '0',
  `show_in_list` tinyint(1) NOT NULL DEFAULT '1',
  `akreditace_mpsv` varchar(45) COLLATE utf8_czech_ci DEFAULT NULL,
  `akreditace_msmt` varchar(45) COLLATE utf8_czech_ci DEFAULT NULL,
  `target_groups` varchar(45) COLLATE utf8_czech_ci DEFAULT NULL,
  `time_start` time DEFAULT NULL,
  PRIMARY KEY (`id_course`,`id_user`),
  UNIQUE KEY `url_key` (`url_key`),
  KEY `fk_tb_users_id_user` (`id_user`),
  FULLTEXT KEY `name` (`name`),
  FULLTEXT KEY `text_clear` (`text_clear`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}courses_has_users`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}courses_has_users` (
  `id_user` smallint(6) NOT NULL,
  `id_course` smallint(6) NOT NULL,
  PRIMARY KEY (`id_user`,`id_course`),
  KEY `fk_tb_users_id_user` (`id_user`),
  KEY `fk_tb_courses_id_course` (`id_course`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}courses_places`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}courses_places` (
  `id_place` int(11) NOT NULL AUTO_INCREMENT,
  `place` varchar(300) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id_place`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}courses_registrations`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}courses_registrations` (
  `id_registration` int(11) NOT NULL AUTO_INCREMENT,
  `id_course` smallint(6) NOT NULL,
  `name` varchar(45) COLLATE utf8_czech_ci NOT NULL,
  `surname` varchar(45) COLLATE utf8_czech_ci NOT NULL,
  `degree` varchar(3) COLLATE utf8_czech_ci DEFAULT NULL,
  `grade` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Pracovní zařazení',
  `practice_lenght` varchar(10) COLLATE utf8_czech_ci DEFAULT NULL,
  `phone` varchar(16) COLLATE utf8_czech_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `note` varchar(400) COLLATE utf8_czech_ci DEFAULT NULL,
  `pay_type` varchar(15) COLLATE utf8_czech_ci DEFAULT 'organisation',
  `org_name` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `org_address` varchar(400) COLLATE utf8_czech_ci DEFAULT NULL,
  `org_ico` varchar(15) COLLATE utf8_czech_ci DEFAULT NULL,
  `org_phone` varchar(15) COLLATE utf8_czech_ci DEFAULT NULL,
  `private_address` varchar(400) COLLATE utf8_czech_ci DEFAULT NULL,
  `time_add` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(15) COLLATE utf8_czech_ci DEFAULT NULL,
  `canceled` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_registration`,`id_course`),
  KEY `fk_tb_courses_registrations_tb_courses` (`id_course`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}lecturers_has_courses`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}lecturers_has_courses` (
  `id_lecturer` smallint(6) NOT NULL,
  `id_course` smallint(6) NOT NULL,
  PRIMARY KEY (`id_lecturer`,`id_course`),
  KEY `fk_tb_lecturers_id_lecturer` (`id_lecturer`),
  KEY `fk_tb_courses_id_course` (`id_course`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;