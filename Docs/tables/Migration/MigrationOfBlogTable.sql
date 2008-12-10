

-- Přidání sloupců
ALTER TABLE `vypecky_blog` ADD `id_item` SMALLINT UNSIGNED NULL AFTER `id_blog` ;

ALTER TABLE `vypecky_blog` ADD INDEX ( `id_item` ) ;

-- Uprava existujících sloupců
ALTER TABLE `vypecky_blog` CHANGE `label` `label_cs` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,CHANGE `text` `text_cs` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

-- Přidání ostatních sloupců
ALTER TABLE `vypecky_blog` ADD `label_en` VARCHAR( 200 ) NULL AFTER `text_cs` ;
ALTER TABLE `vypecky_blog` ADD `text_en` TEXT NULL AFTER `label_en` ;

ALTER TABLE `vypecky_blog` ADD `label_de` VARCHAR( 200 ) NULL AFTER `text_en` ;
ALTER TABLE `vypecky_blog` ADD `text_de` TEXT NULL AFTER `label_de` ;

-- Smazané blogy
ALTER TABLE `vypecky_blog` ADD `deleted` BOOL NOT NULL DEFAULT '0';
ALTER TABLE `vypecky_blog` ADD `deleted_by_id_user` BOOL NOT NULL DEFAULT '0';

-- Přidání tabulky se sekcema
 CREATE TABLE `dev`.`vypecky_blog_sections` (
`id_section` SMALLINT UNSIGNED NOT NULL ,
`id_item` SMALLINT UNSIGNED NOT NULL ,
`urlkey` VARCHAR( 50 ) NOT NULL ,
`label_cs` VARCHAR( 200 ) NULL ,
`label_en` VARCHAR( 200 ) NULL ,
`label_de` VARCHAR( 200 ) NULL ,
`time` INT NOT NULL ,
`id_user` SMALLINT UNSIGNED NULL ,
`deleted` BOOL NOT NULL DEFAULT '0',
`deleted_by_id_user` SMALLINT UNSIGNED NULL ,
PRIMARY KEY ( `id_section` ) ,
INDEX ( `id_item` , `urlkey` , `id_user` , `deleted_by_id_user` ) ,
FULLTEXT (
`label_cs` ,
`label_en` ,
`label_de`
)
) ENGINE = MYISAM 

--ještě změna názvu tabulek
RENAME TABLE `dev`.`vypecky_blog`  TO `dev`.`vypecky_blogs` ;
RENAME TABLE `dev`.`vypecky_blog_sections`  TO `dev`.`vypecky_blogs_sections` ;


