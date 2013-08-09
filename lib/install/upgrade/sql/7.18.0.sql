-- Update na verzi 7.18

-- update module length
ALTER TABLE `{PREFIX}categories` CHANGE COLUMN `module` `module` VARCHAR(30) NULL DEFAULT NULL  ;


/* UPDATE_MAIN_SITE */
-- tabluka s tokeny pouze u hlavní stránky
CREATE TABLE IF NOT EXISTS `{PREFIX}secure_tokens` (
  `id_secure_token` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `secure_token` varchar(40) NOT NULL,
  `secure_token_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_secure_token`),
  KEY `token_user_time` (`secure_token`,`id_user`,`secure_token_created`)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;

UPDATE `cubecms_global_config` SET `value` = 'db' WHERE `key` = 'TOKENS_STORE';
-- INSERT INTO `cubecms_global_config` (`key`, `label`, `value`, `protected`, `type`, `id_group`, `hidden_value`)
-- VALUES
-- ('ANALYTICS_DISABLED_HOSTS', 'IP adresy pro které je analýza stránek vypnuta (odělené čárkou)', "127.0.0.1", false, 'string', 11, false),
-- ('ENABLE_LANG_AUTODETECTION', 'Zapnutí autodetekce jazyka', "false", false, 'bool', 8, false),
-- ('DEFAULT_LANG_SUBSTITUTION', 'Nahrazovat jazyk výchozím jazykem', "false", false, 'bool', 8, false);
/* END_UPDATE */

/* UPDATE_SHOP */
ALTER TABLE `{PREFIX}shop_attributes`
ADD COLUMN `attribute_order` INT NULL DEFAULT 0 ;

ALTER TABLE `{PREFIX}shop_attributes_groups`
ADD COLUMN `atgroup_order` INT NULL DEFAULT 0 ;

-- add order and rename colums
ALTER TABLE `{PREFIX}shop_products_general`
CHANGE COLUMN `code` `product_code` VARCHAR(100) NULL DEFAULT NULL,
CHANGE COLUMN `name_cs` `product_name_cs` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL  ,
CHANGE COLUMN `urlkey_cs` `product_urlkey_cs` VARCHAR(200) NULL DEFAULT NULL  ,
CHANGE COLUMN `text_short_cs` `product_text_short_cs` TEXT NULL DEFAULT NULL  ,
CHANGE COLUMN `text_cs` `product_text_cs` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL  ,
CHANGE COLUMN `text_clear_cs` `product_text_clear_cs` TEXT NULL DEFAULT NULL  ,
CHANGE COLUMN `keywords_cs` `product_keywords_cs` VARCHAR(300) NULL DEFAULT NULL  ,
CHANGE COLUMN `name_en` `product_name_en` VARCHAR(200) NULL DEFAULT NULL  ,
CHANGE COLUMN `urlkey_en` `product_urlkey_en` VARCHAR(200) NULL DEFAULT NULL  ,
CHANGE COLUMN `text_short_en` `product_text_short_en` TEXT NULL DEFAULT NULL  ,
CHANGE COLUMN `text_en` `product_text_en` TEXT NULL DEFAULT NULL  ,
CHANGE COLUMN `text_clear_en` `product_text_clear_en` TEXT NULL DEFAULT NULL  ,
CHANGE COLUMN `keywords_en` `product_keywords_en` VARCHAR(300) NULL DEFAULT NULL  ,
CHANGE COLUMN `name_de` `product_name_de` VARCHAR(200) NULL DEFAULT NULL  ,
CHANGE COLUMN `urlkey_de` `product_urlkey_de` VARCHAR(200) NULL DEFAULT NULL  ,
CHANGE COLUMN `text_short_de` `product_text_short_de` TEXT NULL DEFAULT NULL  ,
CHANGE COLUMN `text_de` `product_text_de` TEXT NULL DEFAULT NULL  ,
CHANGE COLUMN `text_clear_de` `product_text_clear_de` TEXT NULL DEFAULT NULL  ,
CHANGE COLUMN `keywords_de` `product_keywords_de` VARCHAR(300) NULL DEFAULT NULL  ,
CHANGE COLUMN `name_sk` `product_name_sk` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_slovak_ci' NULL DEFAULT NULL  ,
CHANGE COLUMN `urlkey_sk` `product_urlkey_sk` VARCHAR(200) NULL DEFAULT NULL  ,
CHANGE COLUMN `text_short_sk` `product_text_short_sk` TEXT NULL DEFAULT NULL  ,
CHANGE COLUMN `text_sk` `product_text_sk` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_slovak_ci' NULL DEFAULT NULL  ,
CHANGE COLUMN `text_clear_sk` `product_text_clear_sk` TEXT NULL DEFAULT NULL  ,
CHANGE COLUMN `keywords_sk` `product_keywords_sk` VARCHAR(300) NULL DEFAULT NULL  ,
CHANGE COLUMN `price` `product_price` FLOAT NULL DEFAULT NULL  ,
CHANGE COLUMN `unit` `product_unit` VARCHAR(20) NULL DEFAULT NULL  ,
CHANGE COLUMN `unit_size` `product_unit_size` INT(11) NULL DEFAULT NULL  ,
CHANGE COLUMN `quantity` `product_quantity` INT(11) NULL DEFAULT '-1'  ,
CHANGE COLUMN `weight` `product_weight` FLOAT NULL DEFAULT NULL  ,
CHANGE COLUMN `discount` `product_discount` TINYINT(4) NULL DEFAULT '0'  ,
CHANGE COLUMN `deleted` `product_deleted` TINYINT(4) NULL DEFAULT '0'  ,
CHANGE COLUMN `showed` `product_showed` INT(11) NOT NULL DEFAULT '0'  ,
CHANGE COLUMN `active` `product_active` TINYINT(4) NULL DEFAULT '1'  ,
CHANGE COLUMN `image` `product_image` VARCHAR(100) NULL DEFAULT NULL  ,
CHANGE COLUMN `date_add` `product_date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP  ,
CHANGE COLUMN `personal_pickup_only` `product_personal_pickup_only` TINYINT(1) NULL DEFAULT '0'  ,
CHANGE COLUMN `required_pickup_date` `product_required_pickup_date` TINYINT(1) NULL DEFAULT '0'  ,
CHANGE COLUMN `is_new_to_date` `product_is_new_to_date` DATE NULL DEFAULT NULL  ,
ADD COLUMN `product_order` INT(11) NULL DEFAULT 0 ;
/* END_UPDATE */