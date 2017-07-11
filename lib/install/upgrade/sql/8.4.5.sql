ALTER TABLE `{PREFIX}groups` CHANGE `default_right` `default_right` varchar(3) COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'r--';

/* UPDATE_SHOP */
INSERT INTO `{PREFIX}config` 
      ( `key` ,                 `label` ,                 `value` , `values` , `protected` , `type` , `id_group` , `callback_func` , `hidden_value` ) 
VALUES ('SHOP_ORDER_CAT_TERMS', 'ID stránky s podmínkami', 0 ,       NULL ,     0,           'number',    10,          NULL ,            0);

ALTER TABLE `{PREFIX}shop_customers` ADD `newsletter` TINYINT(1) NOT NULL DEFAULT '0';


ALTER TABLE `{PREFIX}shop_products_combinations` CHANGE `product_combination_quantity` `product_combination_quantity` DECIMAL(11,4) NULL DEFAULT 0;
ALTER TABLE `{PREFIX}shop_products_general` CHANGE `product_unit_size` `product_unit_size` DECIMAL(11,4) NULL DEFAULT NULL;
ALTER TABLE `{PREFIX}shop_products_general` CHANGE `product_quantity` `product_quantity` DECIMAL(11,4) NULL DEFAULT NULL;
ALTER TABLE `{PREFIX}shop_cart_items` CHANGE `cart_item_qty` `cart_item_qty` DECIMAL(11,4) NOT NULL DEFAULT 1;
ALTER TABLE `{PREFIX}shop_order_items` CHANGE `order_product_quantity` `order_product_quantity` DECIMAL(11,4) NULL DEFAULT 1;

/* END_UPDATE */

ALTER TABLE `{PREFIX}users` CHANGE `username` `username` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Uzivatelske jmeno';

