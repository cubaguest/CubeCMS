-- --------------------------------------------------------

--
-- Struktura tabulky `projects`
--
CREATE TABLE IF NOT EXISTS `{PREFIX}projects` (
  `id_project` int(11) NOT NULL AUTO_INCREMENT,
  `id_project_section` int(11) NOT NULL,
  `id_user` smallint(6) NOT NULL,
  `id_user_last_edit` int(11) NOT NULL DEFAULT '0',
  `project_name` varchar(300) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `project_name_short` varchar(100) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `project_urlkey` varchar(200) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `project_text` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `project_text_clear` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `project_time_add` datetime NOT NULL,
  `project_time_edit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `project_image` varchar(50) DEFAULT NULL,
  `project_thumb` varchar(50) DEFAULT NULL,
  `project_related` varchar(200) DEFAULT NULL,
  `project_weight` smallint(6) DEFAULT '0',
  `project_tpl_params` varchar(500) DEFAULT NULL,
  `project_keywords` varchar(300) DEFAULT NULL,
  `project_desc` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id_project`),
  KEY `id_user` (`id_user`,`project_urlkey`),
  KEY `id_user_last_edit` (`id_user_last_edit`),
  FULLTEXT KEY `name` (`project_name`,`project_text_clear`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Struktura tabulky `projects_sections`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}projects_sections` (
  `id_project_section` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_category` smallint(6) NOT NULL,
  `section_name` varchar(200) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `section_urlkey` varchar(200) NOT NULL,
  `section_text` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `section_text_clear` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `section_time_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `section_weight` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id_project_section`),
  KEY `id_category` (`id_category`,`section_urlkey`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

