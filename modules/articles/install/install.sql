--
-- Struktura tabulky `{PREFIX}articles`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}articles` (
  `id_article` int unsigned NOT NULL AUTO_INCREMENT,
  `id_cat` int unsigned NOT NULL,
  `id_user` int unsigned DEFAULT 1,
  `id_photogallery` int unsigned DEFAULT 0,
  `add_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `edit_time` datetime DEFAULT NULL,
  `is_user_last_edit` int DEFAULT NULL,
  `viewed` int NOT NULL DEFAULT 0,
  `name_cs` varchar(400) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `text_cs` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `annotation_cs` varchar(1000) DEFAULT NULL,
  `text_clear_cs` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `urlkey_cs` varchar(100) DEFAULT NULL,
  `text_private_cs` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `keywords_cs` varchar(200) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `description_cs` varchar(300) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `name_sk` varchar(400) CHARACTER SET utf8 COLLATE utf8_slovak_ci DEFAULT NULL,
  `text_sk` text CHARACTER SET utf8 COLLATE utf8_slovak_ci,
  `annotation_sk` varchar(1000) DEFAULT NULL,
  `text_clear_sk` text CHARACTER SET utf8 COLLATE utf8_slovak_ci,
  `urlkey_sk` varchar(100) DEFAULT NULL,
  `text_private_sk` text CHARACTER SET utf8 COLLATE utf8_slovak_ci,
  `keywords_sk` varchar(200) CHARACTER SET utf8 COLLATE utf8_slovak_ci DEFAULT NULL,
  `description_sk` varchar(300) CHARACTER SET utf8 COLLATE utf8_slovak_ci DEFAULT NULL,
  `name_en` varchar(400) DEFAULT NULL,
  `text_en` text,
  `annotation_en` varchar(1000) DEFAULT NULL,
  `text_clear_en` text,
  `urlkey_en` varchar(100) DEFAULT NULL,
  `text_private_en` text,
  `keywords_en` varchar(200) DEFAULT NULL,
  `description_en` varchar(300) DEFAULT NULL,
  `name_de` varchar(400) DEFAULT NULL,
  `text_de` text,
  `annotation_de` varchar(1000) DEFAULT NULL,
  `text_clear_de` text,
  `urlkey_de` varchar(100) DEFAULT NULL,
  `text_private_de` text,
  `keywords_de` varchar(200) DEFAULT NULL,
  `description_de` varchar(300) DEFAULT NULL,
  `concept` tinyint(1) NOT NULL DEFAULT '0',
  `title_image` varchar(100) DEFAULT NULL,
  `author` varchar(100) DEFAULT NULL,
  `article_priority` int(11) DEFAULT '0',
  `article_priority_end_date` date DEFAULT NULL,
  `article_place` VARCHAR(50) NULL DEFAULT NULL,
  `article_datadir` VARCHAR(200) NULL DEFAULT NULL,
  PRIMARY KEY (`id_article`),
  KEY `urlkey_cs` (`urlkey_cs`),
  KEY `urlkey_en` (`urlkey_en`),
  KEY `urlkey_de` (`urlkey_de`),
  KEY `urlkey_sk` (`urlkey_sk`),
  KEY `urlkey_cs_id_cat` (`id_cat`,`urlkey_cs`),
  KEY `urlkey_en_id_cat` (`id_cat`,`urlkey_en`),
  KEY `urlkey_de_id_cat` (`id_cat`,`urlkey_de`),
  KEY `urlkey_sk_id_cat` (`id_cat`,`urlkey_sk`),
  FULLTEXT KEY `label_cs` (`name_cs`),
  FULLTEXT KEY `label_en` (`name_en`),
  FULLTEXT KEY `lebal_de` (`name_de`),
  FULLTEXT KEY `lebal_sk` (`name_sk`),
  FULLTEXT KEY `text_clear_cs` (`text_clear_cs`),
  FULLTEXT KEY `text_clear_en` (`text_clear_en`),
  FULLTEXT KEY `text_clear_de` (`text_clear_de`),
  FULLTEXT KEY `text_clear_sk` (`text_clear_sk`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}articles_has_private_users`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}articles_has_private_users` (
  `id_user` smallint(6) NOT NULL,
  `id_article` smallint(6) NOT NULL,
  PRIMARY KEY (`id_user`,`id_article`),
  KEY `fk_tb_users_id_user` (`id_user`),
  KEY `fk_tb_articles_id_article` (`id_article`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `{PREFIX}articles_tags` (
  `id_article_tag` INT NOT NULL  AUTO_INCREMENT,
  `article_tag_name` VARCHAR(20) NOT NULL ,
  `article_tag_counter` INT NULL DEFAULT 0 ,
  PRIMARY KEY (`id_article_tag`)
) ENGINE = MyISAM DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `{PREFIX}articles_tags_has_articles` (
  `articles_tags_id_article_tag` INT NOT NULL ,
  `articles_id_article` SMALLINT(5) UNSIGNED NOT NULL ,
  PRIMARY KEY (`articles_tags_id_article_tag`, `articles_id_article`) ,
  INDEX `fk_articles_tags_has_articles_arti1` (`articles_id_article` ASC) ,
  INDEX `fk_articles_tags_has_articles_arti` (`articles_tags_id_article_tag` ASC) 
) ENGINE = MyISAM DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;

INSERT INTO `{PREFIX}autorun` (`autorun_module_name`, `autorun_period`, `autorun_url`) 
VALUES ('articles', 'daily', NULL);