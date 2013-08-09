/* UPDATE_MAIN_SITE */
INSERT INTO `cubecms_global_config` (`key`, `label`, `value`, `protected`, `type`, `id_group`, `hidden_value`) 
VALUES
('ANALYTICS_DISABLED_HOSTS', 'IP adresy pro které je analýza stránek vypnuta (odělené čárkou)', "127.0.0.1", false, 'string', 11, false),
('ENABLE_LANG_AUTODETECTION', 'Zapnutí autodetekce jazyka', "false", false, 'bool', 8, false),
('DEFAULT_LANG_SUBSTITUTION', 'Nahrazovat jazyk výchozím jazykem', "false", false, 'bool', 8, false);
/* END_UPDATE */

ALTER TABLE `{PREFIX}panels` 
ADD COLUMN `panel_force_global` TINYINT(1) NULL DEFAULT 0;

/* UPDATE_SHOP */
ALTER TABLE `{PREFIX}shop_products_variants`
ADD COLUMN `variant_weight_add` SMALLINT NULL DEFAULT 0 ,
ADD COLUMN `variant_quantity` SMALLINT NULL DEFAULT -1 ,
CHANGE COLUMN `id_variant` `id_variant` INT(11) NOT NULL AUTO_INCREMENT ,
CHANGE COLUMN `variant_product_code` `variant_code_add` VARCHAR(100) NULL DEFAULT NULL  ;

ALTER TABLE `{PREFIX}shop_attributes_groups`
CHANGE COLUMN `id_attribute_group` `id_attribute_group` INT(11) NOT NULL AUTO_INCREMENT  ;

ALTER TABLE `{PREFIX}shop_attributes`
CHANGE COLUMN `id_attribute` `id_attribute` INT(11) NOT NULL AUTO_INCREMENT  ;

ALTER TABLE `{PREFIX}shop_products_variants`
CHANGE COLUMN `id_variant` `id_variant` INT(11) NOT NULL AUTO_INCREMENT  ;

-- rename basket to cart
ALTER TABLE `{PREFIX}shop_basket`
CHANGE COLUMN `id_basket_item` `id_cart_item` INT(11) NOT NULL AUTO_INCREMENT  ,
CHANGE COLUMN `basket_qty` `cart_item_qty` INT(11) NOT NULL DEFAULT '1'  ,
CHANGE COLUMN `basket_date_add` `cart_item_date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP  ,
CHANGE COLUMN `basket_variant_label` `cart_item_variant_label` VARCHAR(300) NULL DEFAULT NULL  ,
RENAME TO  `{PREFIX}shop_cart_items` ;


/* END_UPDATE */