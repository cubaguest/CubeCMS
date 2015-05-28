CREATE TABLE IF NOT EXISTS `{PREFIX}contact_questions` (
`id_question` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL ,
`mail` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL ,
`subject` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL ,
`phone` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL ,
`text` VARCHAR( 2000 ) CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL ,
`time_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = MYISAM ;