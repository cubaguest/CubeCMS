
/* UPDATE_MAIN_SITE */
INSERT INTO `cubecms_global_config_groups` (
`id_group` ,`name_cs` ,`name_sk` ,`name_en` ,`name_de` ,`desc_cs` ,`desc_sk` ,`desc_en` ,`desc_de`)
VALUES 
(11 , 'Soc. sítě/analýza', 'Soc. sítě/analýza', 'Soc. Networks/Analysis', NULL , 'Nastavení sociálních sítí a analytických nástrojů. (např. Facebook, Google Analytics,...)', NULL , NULL , NULL);


INSERT INTO `cubecms_global_config` (`key`, `label`, `value`, `protected`, `type`, `id_group`) 
VALUES
('FCB_APP_ID', 'Facebook App ID (pokud nějáká existuje)', NULL, false, 'string', 11),
('FCB_PAGE_URL', 'Adresa stránky/skupiny na Facebooku', NULL, false, 'string', 11),
('FCB_ADMINS', 'Facebook administrátoři komentářů (ID uživatelů oddělené čárkou)', NULL, false, 'string', 11),
('FCB_SHOW_LIKE_THIS_BUTTON', 'Zobrazit tlačítko "Like this" Facebooku', true, false, 'bool', 11),
('GOOGLE_ANALYTICS_CODE', 'Kód pro Google Analytics', NULL, false, 'string', 11),
('GOOGLE_SHOW_PLUS_BUTTON', 'Zobrazit tlačítko Google +1', true, false, 'bool', 11),
('SHARE_TOOLS_BUTTON_SHOW', 'Zobrazit tlačítko sdílení pomocí ostatních služeb', true, false, 'bool', 11)
;
/* END_UPDATE */

/* UPDATE_SHOP */
-- další parametry produktů
ALTER TABLE `{PREFIX}shop_products_general` 
ADD COLUMN `personal_pickup_only` TINYINT(1)  NULL DEFAULT 0  AFTER `date_add` , 
ADD COLUMN `required_pickup_date` TINYINT(1)  NULL DEFAULT 0  AFTER `personal_pickup_only` ;

-- datum odběru položky
ALTER TABLE `{PREFIX}shop_orders` 
ADD COLUMN `order_pickup_date` DATE NULL DEFAULT NULL  AFTER `order_is_new` ;

-- nastavení atributů
ALTER TABLE `{PREFIX}shop_order_items` 
ADD COLUMN `order_product_attributes` VARCHAR(500) NULL DEFAULT NULL  AFTER `order_product_note` ;

-- přidání volby osobního odběru
ALTER TABLE `{PREFIX}shop_shippings` ADD COLUMN `shipping_is_personal_pickup` TINYINT(1)  NULL DEFAULT 0  AFTER `shipping_text_de` ;


-- skupiny atributů
CREATE  TABLE IF NOT EXISTS `{PREFIX}shop_attributes_groups` (
  `id_attribute_group` INT NOT NULL ,
  `atgroup_name_cs` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL ,
  `atgroup_name_en` VARCHAR(100) NULL DEFAULT NULL ,
  `atgroup_name_sk` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_slovak_ci' NULL DEFAULT NULL ,
  `atgroup_name_de` VARCHAR(100) NULL DEFAULT NULL ,
  PRIMARY KEY (`id_attribute_group`) )
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

-- tabulka atributů
CREATE  TABLE IF NOT EXISTS `{PREFIX}shop_attributes` (
  `id_attribute` INT NOT NULL ,
  `id_attribute_group` INT NOT NULL ,
  `attribute_name_cs` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL ,
  `attribute_name_en` VARCHAR(100) NULL DEFAULT NULL ,
  `attribute_name_sk` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_slovak_ci' NULL DEFAULT NULL ,
  `attribute_name_de` VARCHAR(100) NULL DEFAULT NULL ,
  PRIMARY KEY (`id_attribute`) ,
  INDEX `group` (`id_attribute_group` ASC) )
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

-- propojení produktů s atributy
CREATE  TABLE IF NOT EXISTS `{PREFIX}shop_products_variants` (
  `id_variant` INT NOT NULL ,
  `id_product` INT NOT NULL ,
  `id_attribute` INT NOT NULL ,
  `variant_price_add` FLOAT NULL DEFAULT 0 ,
  `variant_product_code` VARCHAR(100) NULL DEFAULT NULL ,
  PRIMARY KEY (`id_variant`) ,
  INDEX `fkeys` (`id_product` ASC, `id_attribute` ASC) )
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

/* END_UPDATE */
