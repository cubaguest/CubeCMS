-- HOW USE
-- 1. Replace {PREFIX} with system prefix
-- 2. Replace {LANG} with language (example cs, en)
-- 3. Run specific part in SQL editor


ALTER TABLE `{PREFIX}articles` 
ADD COLUMN `name_{LANG}` VARCHAR( 400 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `description_de` ,
ADD COLUMN `text_{LANG}` TEXT NULL DEFAULT NULL AFTER `name_{LANG}` ,
ADD COLUMN `annotation_{LANG}` VARCHAR( 1000 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `text_{LANG}` ,
ADD COLUMN `text_clear_{LANG}` TEXT NULL DEFAULT NULL AFTER `annotation_{LANG}` ,
ADD COLUMN `urlkey_{LANG}` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `text_clear_{LANG}` ,
ADD COLUMN `text_private_{LANG}` TEXT NULL DEFAULT NULL AFTER `urlkey_{LANG}` ,
ADD COLUMN `keywords_{LANG}` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `text_private_{LANG}` ,
ADD COLUMN `description_{LANG}` VARCHAR( 300 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `keywords_{LANG}` ,
ADD INDEX ( `urlkey_{LANG}` ) ,
ADD FULLTEXT (`text_clear_{LANG}` , `name_{LANG}`)
;

ALTER TABLE  `{PREFIX}categories` 
ADD COLUMN  `urlkey_{LANG}` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER  `description_sk` ,
ADD COLUMN  `label_{LANG}` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER  `urlkey_{LANG}` ,
ADD COLUMN  `alt_{LANG}` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER  `label_{LANG}` ,
ADD COLUMN  `keywords_{LANG}` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER  `alt_{LANG}` ,
ADD COLUMN  `description_{LANG}` VARCHAR( 500 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER  `keywords_{LANG}` ,
ADD INDEX (  `urlkey_{LANG}` ) ,
ADD FULLTEXT ( `label_{LANG}` , `description_{LANG}` );

-- not aplied --

ALTER TABLE `{PREFIX}config_groups` 
ADD COLUMN `name_{LANG}` VARCHAR(45) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `name_de` , 
ADD COLUMN `desc_{LANG}` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `desc_de` ;

ALTER TABLE `{PREFIX}panels` 
ADD COLUMN `pname_{LANG}` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `pname_sk` ;

ALTER TABLE `{PREFIX}photogalery_images` 
ADD COLUMN `name_{LANG}` VARCHAR(300) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL DEFAULT NULL AFTER `desc_de` , 
ADD COLUMN `desc_{LANG}` VARCHAR(1000) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL DEFAULT NULL  AFTER `name_{LANG}` ;

ALTER TABLE `{PREFIX}texts` 
ADD COLUMN `label_{LANG}` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL DEFAULT NULL  AFTER `text_clear_sk` , 
ADD COLUMN `text_{LANG}` MEDIUMTEXT CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL DEFAULT NULL  AFTER `label_{LANG}` , 
ADD COLUMN `text_clear_{LANG}` MEDIUMTEXT CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL DEFAULT NULL  AFTER `text_{LANG}` , 
ADD INDEX `label_{LANG}` (`label_{LANG}` ASC) , 
ADD FULLTEXT INDEX `text_clear_{LANG}` (`text_clear_{LANG}` ASC) ;


-- MAIN SITE CONFIG -- 
ALTER TABLE `cubecms_global_config_groups` 
ADD COLUMN `name_{LANG}` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL DEFAULT NULL  AFTER `name_de` , 
ADD COLUMN `desc_{LANG}` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL DEFAULT NULL  AFTER `desc_de` ;



-- SOF SHOP --
ALTER TABLE `{PREFIX}shop_products_general` 
ADD COLUMN `name_{LANG}` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL DEFAULT NULL  AFTER `keywords_sk` , 
ADD COLUMN `urlkey_{LANG}` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL DEFAULT NULL  AFTER `name_{LANG}` , 
ADD COLUMN `text_short_{LANG}` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL DEFAULT NULL  AFTER `urlkey_{LANG}` , 
ADD COLUMN `text_{LANG}` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL DEFAULT NULL  AFTER `text_short_{LANG}` , 
ADD COLUMN `text_clear_{LANG}` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL DEFAULT NULL  AFTER `text_{LANG}` , 
ADD COLUMN `keywords_{LANG}` VARCHAR(300) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL DEFAULT NULL  AFTER `text_clear_{LANG}`, 
ADD INDEX `urlkey_{LANG}` (`urlkey_{LANG}` ASC) , 
ADD FULLTEXT INDEX `text_{LANG}_fulltext` (`text_clear_{LANG}` ASC) , 
ADD FULLTEXT INDEX `name_{LANG}_fulltext` (`name_{LANG}` ASC) ;

ALTER TABLE `{PREFIX}shop_shippings` 
ADD COLUMN `shipping_name_{LANG}` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL DEFAULT NULL  AFTER `shipping_name_sk` , 
ADD COLUMN `shipping_text_{LANG}` VARCHAR(500) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL DEFAULT NULL  AFTER `shipping_text_de` ;
-- EOF SHOP --

