-- Nahradit {PREFIX} za prefix tabulek

-- nová skupina v konfigu
INSERT INTO `cube_cms`.`vypecky_config_groups` ( `id_group` , `name_cs` , `name_sk` , `name_en` , `name_de` , `desc_cs` , `desc_sk` , `desc_en` , `desc_de` )
VALUES (NULL , 'E-Shop nastavení', NULL , NULL , NULL , 'Nastavení elektronického obchodu. Toto nastavení je lépe upravovat přímo v nastavení obchodu.', NULL , NULL , NULL);

INSERT INTO `cube_cms`.`vypecky_config` (`key` , `label` , `value` , `values` , `protected` , `type` , `id_group` , `callback_func` )
VALUES 
('SHOP', 'Zapnutí podpory pro e-shop', 0, NULL , 0, 'bool', 10, NULL),
('SHOP_CURRENCY_NAME', 'Náze měny (Kč, $, ...)', 'Kč', NULL , 0, 'string', 10, NULL),
('SHOP_CURRENCY_CODE', 'Kód měny (USD, CZK, ...)', 'CZK', NULL , 0, 'string', 10, NULL), 
('SHOP_FREE_SHIPPING', 'Doprava zdarma od (-1 pro vypnutí)', 2000, NULL , 0, 'number', 10, NULL),
('SHOP_NEWSLETTER_GROUP_ID', 'Id skupiny, kam se mají řadit maily pro newsletter', 17, NULL, 0, 'number', 10, NULL),
('SHOP_ORDER_DEFAULT_STATUS', 'Výchozí status objednávky', 'přijato', NULL , 0, 'string', 10, NULL),
('SHOP_STORE_INFO', 'Informace o obchodu (adresa, ...)', NULL , NULL , '0', 'string', '10', NULL),
('SHOP_ORDER_MAIL', 'E-Mailová adresa, do které budou přeposílány e-maily o nových objednávkách.', 'jakubmatas@gmail.com', NULL , '0', 'string', '10', NULL)
;


CREATE TABLE IF NOT EXISTS `{PREFIX}shop_tax` (
  `id_tax` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `value` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_tax`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE  TABLE IF NOT EXISTS `{PREFIX}shop_products_general` (
  `id_product` INT NOT NULL AUTO_INCREMENT ,
  `id_category` SMALLINT NOT NULL ,
  `id_tax` INT NOT NULL ,
  `code` VARCHAR(100) NULL ,
  `name_cs` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL ,
  `urlkey_cs` VARCHAR(200) NULL ,
  `text_short_cs` TEXT NULL ,
  `text_cs` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL ,
  `text_clear_cs` TEXT NULL ,
  `keywords_cs` VARCHAR(300) NULL ,
  `name_en` VARCHAR(200) NULL ,
  `urlkey_en` VARCHAR(200) NULL ,
  `text_short_en` TEXT NULL ,
  `text_en` TEXT NULL ,
  `text_clear_en` TEXT NULL ,
  `keywords_en` VARCHAR(300) NULL ,
  `name_de` VARCHAR(200) NULL ,
  `urlkey_de` VARCHAR(200) NULL ,
  `text_short_de` TEXT NULL ,
  `text_de` TEXT NULL ,
  `text_clear_de` TEXT NULL ,
  `keywords_de` VARCHAR(300) NULL ,
  `name_sk` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_slovak_ci' NULL ,
  `urlkey_sk` VARCHAR(200) NULL ,
  `text_short_sk` TEXT NULL ,
  `text_sk` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_slovak_ci' NULL ,
  `text_clear_sk` TEXT NULL ,
  `keywords_sk` VARCHAR(300) NULL ,
  `price` INT NULL ,
  `unit` VARCHAR(20) NULL ,
  `unit_size` INT NULL ,
  `quantity` SMALLINT NULL DEFAULT -1 ,
  `weight` float DEFAULT NULL,
  `discount` TINYINT NULL DEFAULT 0 ,
  `deleted` TINYINT NULL DEFAULT 0 ,
  `active` TINYINT NULL DEFAULT 1 ,
  `image` VARCHAR(100) NULL DEFAULT NULL ,
  PRIMARY KEY (`id_product`) ,
  INDEX `fk_id_category` (`id_category` ASC) ,
  INDEX `fk_id_tax` (`id_tax` ASC) ,
  INDEX `urlkey_cs` (`urlkey_cs` ASC) ,
  INDEX `urlkey_en` (`urlkey_en` ASC) ,
  INDEX `urlkey_de` (`urlkey_de` ASC) ,
  INDEX `urlkey_sk` (`urlkey_sk` ASC) ,
  FULLTEXT INDEX `text_cs_fulltext` (`text_clear_cs` ASC) ,
  FULLTEXT INDEX `text_en_fulltext` (`text_clear_en` ASC) ,
  FULLTEXT INDEX `text_sk_fulltext` (`text_clear_sk` ASC) ,
  FULLTEXT INDEX `text_de_fulltext` (`text_clear_de` ASC) ,
  FULLTEXT INDEX `name_cs_fulltext` (`name_cs` ASC) ,
  FULLTEXT INDEX `name_en_fulltext` (`name_en` ASC) ,
  FULLTEXT INDEX `name_sk_fulltext` (`name_sk` ASC) ,
  FULLTEXT INDEX `name_de_fulltext` (`name_de` ASC)
)
ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;