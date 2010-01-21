CREATE TABLE `dev`.`vypecky_eplugin_sendmails` (
`id_mail` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`id_item` SMALLINT UNSIGNED NOT NULL ,
`id_article` SMALLINT UNSIGNED NOT NULL ,
`mail` VARCHAR( 200 ) NOT NULL ,
INDEX ( `id_item` , `id_article` )
) ENGINE = MYISAM ;

CREATE TABLE `dev`.`vypecky_eplugin_sendmailstexts` (
`id_text` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`id_item` SMALLINT UNSIGNED NOT NULL ,
`id_article` SMALLINT UNSIGNED NOT NULL ,
`subject` VARCHAR( 500 ) NOT NULL ,
`text` TEXT NOT NULL ,
INDEX ( `id_item` , `id_article` )
) ENGINE = MYISAM ;