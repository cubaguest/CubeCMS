/* UPDATE_SHOP */
ALTER TABLE `{PREFIX}shop_zones` ADD `zone_codes` VARCHAR(20) NULL DEFAULT 'CZ';
ALTER TABLE `{PREFIX}shop_products_general` ADD `product_date_edit` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `product_date_add`;
/* END_UPDATE */