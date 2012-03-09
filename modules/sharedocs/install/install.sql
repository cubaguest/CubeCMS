CREATE TABLE IF NOT EXISTS `{PREFIX}sharedoc_directories` (
  `id_sharedoc_directory` int(11) NOT NULL AUTO_INCREMENT,
  `id_category` int(11) NOT NULL DEFAULT '0',
  `dir_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `dir_title` varchar(500) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `dir_date_add` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `dir_date_last_change` datetime DEFAULT NULL,
  `dir_is_public` tinyint(1) NOT NULL DEFAULT '0',
  `dir_is_public_write` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_sharedoc_directory`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}sharedoc_directory_access_groups`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}sharedoc_directory_access_groups` (
  `id_sharedoc_access_group_conn` int(11) NOT NULL AUTO_INCREMENT,
  `id_sharedoc_directory` int(11) NOT NULL,
  `id_group` int(11) NOT NULL,
  `group_read_only` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id_sharedoc_access_group_conn`),
  KEY `index_groups` (`id_sharedoc_directory`,`id_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}sharedoc_directory_access_users`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}sharedoc_directory_access_users` (
  `id_sharedoc_access_user_conn` int(11) NOT NULL AUTO_INCREMENT,
  `id_sharedoc_directory` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `user_read_only` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id_sharedoc_access_user_conn`),
  KEY `index_users` (`id_sharedoc_directory`,`id_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}sharedoc_files`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}sharedoc_files` (
  `id_sharedoc_file` int(11) NOT NULL AUTO_INCREMENT,
  `id_sharedoc_directory` varchar(45) DEFAULT NULL,
  `file_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `file_title` varchar(500) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `locked` tinyint(4) DEFAULT '0',
  `locked_by_id_user` int(11) NOT NULL DEFAULT '0',
  `file_date_add` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_sharedoc_file`),
  KEY `index_directory` (`id_sharedoc_directory`),
  KEY `index_userlock` (`locked_by_id_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}sharedoc_files_download_tokens`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}sharedoc_files_download_tokens` (
  `id_sharedoc_file_token` int(11) NOT NULL AUTO_INCREMENT,
  `id_sharedoc_file` int(11) DEFAULT NULL,
  `token` varchar(50) NOT NULL,
  `token_date_add` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_sharedoc_file_token`),
  KEY `index_file` (`id_sharedoc_file`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}sharedoc_files_revisions`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}sharedoc_files_revisions` (
  `id_sharedoc_file_rev` int(11) NOT NULL AUTO_INCREMENT,
  `id_sharedoc_file` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `rev_filename` varchar(100) DEFAULT NULL,
  `rev_original_filename` varchar(100) DEFAULT NULL,
  `rev_note` varchar(100) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `rev_date_add` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `rev_number` int(11) DEFAULT '1',
  PRIMARY KEY (`id_sharedoc_file_rev`),
  KEY `index_directory` (`id_sharedoc_file`),
  KEY `index_user` (`id_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;