-- --------------------------------------------------------

--
-- Struktura tabulky `projects`
--
CREATE TABLE IF NOT EXISTS `{PREFIX}projects` (
  `id_project` int(11) NOT NULL,
  `id_project_section` int(11) NOT NULL,
  `id_user` smallint(6) NOT NULL,
  `project_name` varchar(200) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `project_urlkey` varchar(200) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `project_text` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `project_text_celar` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `project_time_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `project_image` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  KEY `id_user` (`id_user`,`project_urlkey`),
  FULLTEXT KEY `name` (`project_name`,`project_text_celar`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `projects_sections`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}projects_sections` (
  `id_project_section` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_category` smallint(6) NOT NULL,
  `section_name` varchar(200) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `section_urlkey` varchar(200) CHARACTER SET utf8 NOT NULL,
  `section_text` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `section_text_clear` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `section_time_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_project_section`),
  KEY `id_category` (`id_category`,`section_urlkey`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
