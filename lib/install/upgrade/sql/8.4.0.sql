/* UPDATE_MAIN_SITE */
/* END_UPDATE */

/* UPDATE_SHOP */
-- Obrázky produktů
CREATE TABLE IF NOT EXISTS `{PREFIX}shop_products_images` ( 
`id_product_image` INT NOT NULL AUTO_INCREMENT , 
`id_product` INT NOT NULL , 
`image_order` INT NOT NULL DEFAULT '0' , 
`image_name_cs` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL , 
`image_name_en` VARCHAR(100) NULL DEFAULT NULL , 
`image_name_sk` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_slovak_ci' NULL DEFAULT NULL , 
`image_name_de` VARCHAR(100) NULL DEFAULT NULL , 
`image_type` VARCHAR(100) NULL DEFAULT 'jpg', 
`is_title` TINYINT NOT NULL DEFAULT '0' , 
PRIMARY KEY (`id_product_image`), 
INDEX `idproduct` (`id_product`, `image_order`) ) 
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

-- Parametry produktů
CREATE TABLE IF NOT EXISTS `{PREFIX}shop_products_params` ( 
`id_product_param` INT NOT NULL AUTO_INCREMENT , 
`param_name_cs` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL  , 
`param_name_en` VARCHAR(100) NULL , 
`param_name_sk` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_slovak_ci' NULL DEFAULT NULL , 
`param_name_de` VARCHAR(100) NULL , 
`param_default_value` VARCHAR(100) NULL ,
 PRIMARY KEY (`id_product_param`) ) 
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

-- Hodnoty parametrů u produktů
CREATE TABLE IF NOT EXISTS `{PREFIX}shop_products_params_values` ( 
`id_product_param_value` INT NOT NULL AUTO_INCREMENT , 
`id_product_param` INT NOT NULL , 
`id_product` INT NOT NULL , 
`product_param_value_cs` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL , 
`product_param_value_en` VARCHAR(100) NULL , 
`product_param_value_sk` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_slovak_ci' NULL DEFAULT NULL , 
`product_param_value_de` VARCHAR(100) NULL , 
`product_param_order` INT NOT NULL DEFAULT '0' , 
PRIMARY KEY (`id_product_param_value`), 
INDEX (`id_product`, `id_product_param`, `product_param_order`)) 
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

ALTER TABLE `{PREFIX}shop_attributes` ADD `attribute_code` VARCHAR(50) NULL DEFAULT NULL;

/* END_UPDATE */