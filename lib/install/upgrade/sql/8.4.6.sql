/* UPDATE_SHOP */
INSERT INTO `{PREFIX}config` 
      ( `key` ,                 `label` ,                 `value` , `values` , `protected` , `type` , `id_group` , `callback_func` , `hidden_value` ) 
VALUES ('SHOP_USER_REG_TPL_ID', 'ID šablokńy s geristrací uživateleami', 0 ,       NULL ,     0,           'number',    10,          NULL ,            0);

ALTER TABLE `{PREFIX}shop_products_general` ADD `product_manufacturer` VARCHAR(100) NULL DEFAULT NULL;
ALTER TABLE `{PREFIX}shop_products_general` ADD `product_warranty_years` SMALLINT NULL DEFAULT 2;
ALTER TABLE `{PREFIX}shop_products_general` ADD `product_heureka_cat` VARCHAR(100) NULL DEFAULT NULL;
ALTER TABLE `{PREFIX}shop_products_general` ADD `product_google_cat` VARCHAR(100) NULL DEFAULT NULL;

ALTER TABLE `{PREFIX}shop_shippings` ADD `shipping_min_days` SMALLINT NULL DEFAULT 1;
ALTER TABLE `{PREFIX}shop_shippings` ADD `shipping_max_days` SMALLINT NULL DEFAULT 2;
ALTER TABLE `{PREFIX}shop_shippings` ADD `shipping_heureka_code` VARCHAR(20) NULL DEFAULT NULL;

ALTER TABLE `{PREFIX}shop_payments` ADD `payment_is_cod` TINYINT(1) NULL DEFAULT 0;

/* END_UPDATE */