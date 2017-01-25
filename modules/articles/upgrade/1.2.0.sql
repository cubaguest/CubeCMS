ALTER TABLE  `{PREFIX}articles` ADD  `text_private_cs` TEXT CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL AFTER  `urlkey_cs` ,
ADD  `keywords_cs` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL AFTER  `text_private_cs` ,
ADD  `description_cs` VARCHAR( 300 ) CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL AFTER  `keywords_cs`;

ALTER TABLE  `{PREFIX}articles` ADD  `text_private_en` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER  `urlkey_en` ,
ADD  `keywords_en` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER  `text_private_en` ,
ADD  `description_en` VARCHAR( 300 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER  `keywords_en`;

SET @s = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE table_name = '{PREFIX}articles'
        AND table_schema = DATABASE()
        AND column_name = 'urlkey_sk'
    ) = 0,
    "SELECT 1",
    "ALTER TABLE  `{PREFIX}articles` 
      ADD  `text_private_sk` TEXT CHARACTER SET utf8 COLLATE utf8_slovak_ci NULL DEFAULT NULL AFTER  `urlkey_sk` ,
      ADD  `keywords_sk` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_slovak_ci NULL DEFAULT NULL AFTER  `text_private_sk` ,
      ADD  `description_sk` VARCHAR( 300 ) CHARACTER SET utf8 COLLATE utf8_slovak_ci NULL DEFAULT NULL AFTER  `keywords_sk`;"
));

PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @s = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE table_name = '{PREFIX}articles'
        AND table_schema = DATABASE()
        AND column_name = 'urlkey_de'
    ) = 0,
    "SELECT 1",
    "ALTER TABLE  `{PREFIX}articles` 
      ADD  `text_private_de` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER  `urlkey_de` ,
      ADD  `keywords_de` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER  `text_private_de` ,
      ADD  `description_de` VARCHAR( 300 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER  `keywords_de`;"
));

PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS `{PREFIX}articles_has_private_users` (
  `id_user` smallint(6) NOT NULL,
  `id_article` smallint(6) NOT NULL,
  PRIMARY KEY (`id_user`,`id_article`),
  KEY `fk_tb_users_id_user` (`id_user`),
  KEY `fk_tb_articles_id_article` (`id_article`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;