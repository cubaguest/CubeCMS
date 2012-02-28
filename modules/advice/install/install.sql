--
-- Struktura tabulky `advice`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}advice` (
  `id_advice_question` int(11) NOT NULL AUTO_INCREMENT,
  `id_category` smallint(6) NOT NULL,
  `advice_name` varchar(200) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `advice_question` text CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `advice_answer` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `advice_color` varchar(6) DEFAULT NULL,
  `advice_public` tinyint(1) DEFAULT '0',
  `advice_public_allow` tinyint(1) DEFAULT '0',
  `advice_is_common` tinyint(1) DEFAULT '0',
  `advice_date_add` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `advice_date_answer` datetime DEFAULT NULL,
  `advice_questioner_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `advice_questioner_gender` varchar(1) DEFAULT NULL,
  `advice_questioner_age` smallint(6) DEFAULT NULL,
  `advice_questioner_city` varchar(30) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `advice_questioner_email` varchar(30) DEFAULT NULL,
  `advice_questioner_regular_user` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_advice_question`),
  KEY `id_category` (`id_category`),
  FULLTEXT KEY `fulltext_n` (`advice_name`),
  FULLTEXT KEY `fulltext_a` (`advice_answer`),
  FULLTEXT KEY `fulltext_q` (`advice_question`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `advice_cats`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}advice_cats` (
  `id_advice_cat` int(11) NOT NULL AUTO_INCREMENT,
  `advice_cat_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `advice_cat_is_drug` tinyint(1) DEFAULT '0',
  `advice_cat_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_advice_cat`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `advice_connections`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}advice_connections` (
  `id_advice_question` int(11) NOT NULL,
  `id_advice_cat` int(11) NOT NULL,
  KEY `fkeys` (`id_advice_question`,`id_advice_cat`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
