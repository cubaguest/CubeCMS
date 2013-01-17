-- VERZE 7.7
-- Nahradit za prefix tabulek

-- nová skupina v konfigu
INSERT INTO `{PREFIX}config_groups` ( `id_group` , `name_cs` , `name_sk` , `name_en` , `name_de` , `desc_cs` , `desc_sk` , `desc_en` , `desc_de` )
VALUES (NULL , 'E-Shop nastavení', NULL , NULL , NULL , 'Nastavení elektronického obchodu. Toto nastavení je lépe upravovat přímo v nastavení obchodu.', NULL , NULL , NULL);

INSERT INTO `{PREFIX}config` (`key` , `label` , `value` , `values` , `protected` , `type` , `id_group` , `callback_func` )
VALUES 
('SHOP', 'Zapnutí podpory pro e-shop', 'true', NULL , 0, 'bool', 10, NULL),
('SHOP_CURRENCY_NAME', 'Náze měny (Kč, $, ...)', 'Kč', NULL , 0, 'string', 10, NULL),
('SHOP_CURRENCY_CODE', 'Kód měny (USD, CZK, ...)', 'CZK', NULL , 0, 'string', 10, NULL), 
('SHOP_FREE_SHIPPING', 'Doprava zdarma od (-1 pro vypnutí)', 2000, NULL , 0, 'number', 10, NULL),
('SHOP_NEWSLETTER_GROUP_ID', 'Id skupiny, kam se mají řadit maily pro newsletter', 17, NULL, 0, 'number', 10, NULL),
('SHOP_ORDER_STATUS', 'Stavy objednávek', 'přijato;odesláno;zrušeno;zaplaceno;vráceno;zabaleno', NULL , '0', 'string', '10', NULL),
('SHOP_ORDER_DEFAULT_STATUS', 'Výchozí status objednávky', 'přijato', NULL , 0, 'string', 10, NULL),
('SHOP_STORE_ADDRESS', 'Adresa obchodu', NULL , NULL , '0', 'string', '10', NULL),
('SHOP_ORDER_MAIL', 'E-Mailová adresa, do které budou přeposílány e-maily o nových objednávkách.', 'jakubmatas@gmail.com', NULL , '0', 'string', '10', NULL),

('SHOP_CUSTOMERS_GROUP_ID', 'ID systémové skupiny se zákazníky.', '0', NULL , '0', 'number', '10', NULL),
('SHOP_CUSTOMERS_DEFAULT_GROUP_ID', 'ID výchozí zákaznická skupina.', '1', NULL , '0', 'number', '10', NULL),

('SHOP_ALLOW_BUY_NOT_IN_STOCK', 'Povolení prodeje pokud zboží není skladem.', 'false', NULL , '0', 'bool', '10', NULL),
('SHOP_PRICE_ROUND_DECIMAL', 'Počet míst zaokrouhlení ceny.', 1, NULL , '0', 'number', '10', NULL),
('SHOP_PRICE_DECIMALS', 'Počet desetiných míst ceny.', 1, NULL , '0', 'number', '10', NULL)
;


-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}shop_attributes`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}shop_attributes` (
  `id_attribute` int(11) NOT NULL AUTO_INCREMENT,
  `id_attribute_group` int(11) NOT NULL,
  `attribute_name_cs` varchar(100) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `attribute_name_en` varchar(100) DEFAULT NULL,
  `attribute_name_sk` varchar(100) CHARACTER SET utf8 COLLATE utf8_slovak_ci DEFAULT NULL,
  `attribute_name_de` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_attribute`),
  KEY `group` (`id_attribute_group`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}shop_attributes_groups`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}shop_attributes_groups` (
  `id_attribute_group` int(11) NOT NULL AUTO_INCREMENT,
  `atgroup_name_cs` varchar(100) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `atgroup_name_en` varchar(100) DEFAULT NULL,
  `atgroup_name_sk` varchar(100) CHARACTER SET utf8 COLLATE utf8_slovak_ci DEFAULT NULL,
  `atgroup_name_de` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_attribute_group`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}shop_cart_items`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}shop_cart_items` (
  `id_cart_item` int(11) NOT NULL AUTO_INCREMENT,
  `id_product` int(11) NOT NULL,
  `id_product_combination` int(11) DEFAULT '0',
  `id_user` int(11) NOT NULL DEFAULT '0',
  `id_session` varchar(32) DEFAULT NULL,
  `cart_item_qty` int(11) NOT NULL DEFAULT '1',
  `cart_item_date_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cart_item_variant_label` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id_cart_item`),
  KEY `fk_users` (`id_user`),
  KEY `fk_product` (`id_product`),
  KEY `fk_comb` (`id_product_combination`),
  KEY `index_search` (`id_session`,`id_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}shop_orders`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}shop_orders` (
  `id_order` int(11) NOT NULL AUTO_INCREMENT,
  `id_customer` int(11) NOT NULL DEFAULT '0',
  `order_shipping_method` varchar(45) DEFAULT NULL,
  `order_shipping_price` int(11) DEFAULT '0',
  `order_shipping_id` smallint(6) NOT NULL DEFAULT '0',
  `order_payment_method` varchar(45) DEFAULT NULL,
  `order_payment_price` int(11) DEFAULT '0',
  `order_payment_id` smallint(6) NOT NULL,
  `order_total` int(11) DEFAULT '0',
  `order_tax` int(11) DEFAULT '0',
  `time_add` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_modified` datetime DEFAULT NULL,
  `ip_address` varchar(15) DEFAULT NULL,
  `order_customer_name` varchar(100) DEFAULT NULL,
  `order_customer_phone` varchar(15) DEFAULT NULL,
  `order_customer_email` varchar(100) DEFAULT NULL,
  `order_customer_street` varchar(100) DEFAULT NULL,
  `order_customer_city` varchar(100) DEFAULT NULL,
  `order_customer_post_code` int(11) DEFAULT NULL,
  `order_customer_country` varchar(50) DEFAULT NULL,
  `order_customer_company` varchar(100) DEFAULT NULL,
  `order_customer_company_dic` varchar(15) DEFAULT NULL,
  `order_customer_company_ic` varchar(15) DEFAULT NULL,
  `order_delivery_name` varchar(100) DEFAULT NULL,
  `order_delivery_street` varchar(100) DEFAULT NULL,
  `order_delivery_city` varchar(100) DEFAULT NULL,
  `order_delivery_post_code` int(11) DEFAULT NULL,
  `order_delivery_country` varchar(50) DEFAULT NULL,
  `order_note` varchar(500) DEFAULT NULL,
  `order_is_new` tinyint(1) NOT NULL DEFAULT '1',
  `order_pickup_date` date DEFAULT NULL,
  PRIMARY KEY (`id_order`),
  KEY `fk_shop_orders_shop_users1` (`id_customer`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}shop_order_items`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}shop_order_items` (
  `id_order_item` int(11) NOT NULL AUTO_INCREMENT,
  `id_order` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `order_product_name` varchar(500) DEFAULT NULL,
  `order_product_quantity` int(11) DEFAULT '1',
  `order_product_price` int(11) DEFAULT '0',
  `order_product_tax` int(11) DEFAULT '0',
  `order_product_code` varchar(100) DEFAULT NULL,
  `order_product_unit` varchar(5) DEFAULT 'ks',
  `order_product_note` varchar(500) DEFAULT NULL,
  `order_product_attributes` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id_order_item`),
  KEY `fk_orders` (`id_order`),
  KEY `fk_products` (`id_product`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


--
-- Struktura tabulky `{PREFIX}shop_order_status`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}shop_order_status` (
  `id_order_status` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_order` smallint(6) NOT NULL,
  `order_status_name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `order_status_note` varchar(500) CHARACTER SET utf8 DEFAULT NULL,
  `order_status_time_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_order_status`),
  KEY `id_order` (`id_order`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}shop_payments`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}shop_payments` (
  `id_payment` int(11) NOT NULL AUTO_INCREMENT,
  `payment_class` varchar(45) DEFAULT NULL,
  `payment_name_cs` varchar(45) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `payment_text_cs` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `payment_name_sk` varchar(45) CHARACTER SET utf8 COLLATE utf8_slovak_ci DEFAULT NULL,
  `payment_text_sk` text CHARACTER SET utf8 COLLATE utf8_slovak_ci,
  `payment_name_en` varchar(45) DEFAULT NULL,
  `payment_text_en` text,
  `payment_name_de` varchar(45) DEFAULT NULL,
  `payment_text_de` text,
  `payment_settings` blob,
  `price_add` varchar(1000) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_payment`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Vypisuji data pro tabulku `{PREFIX}shop_payments`
--

INSERT INTO `{PREFIX}shop_payments` (`id_payment`, `payment_class`, `payment_name_cs`, `payment_text_cs`, `payment_name_sk`, `payment_text_sk`, `payment_name_en`, `payment_text_en`, `payment_name_de`, `payment_text_de`, `payment_settings`, `price_add`) VALUES
(1, NULL, 'Hotově', 'Platba při převzení', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0'),
(2, NULL, 'Dobírka', 'Platba za zboží bude provedena při převzení zboží od doručovací firmy', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '30'),
(3, NULL, 'Platba na účet předem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0');

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}shop_products_combinations`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}shop_products_combinations` (
  `id_product_combination` int(11) NOT NULL AUTO_INCREMENT,
  `id_product` int(11) NOT NULL,
  `product_combination_quantity` int(11) DEFAULT '0',
  `product_combination_price_add` FLOAT NULL DEFAULT 0,
  `product_combination_is_default` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_product_combination`),
  KEY `fk_product` (`id_product`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}shop_products_combinations_variants`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}shop_products_combinations_variants` (
  `id_product_combination` int(11) NOT NULL,
  `id_variant` int(11) NOT NULL,
  KEY `fk_combination` (`id_product_combination`),
  KEY `fk_variant` (`id_variant`),
  KEY `fk_combvariant` (`id_product_combination`,`id_variant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}shop_products_general`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}shop_products_general` (
  `id_product` int(11) NOT NULL AUTO_INCREMENT,
  `id_category` smallint(6) NOT NULL,
  `id_tax` int(11) NOT NULL,
  `code` varchar(100) DEFAULT NULL,
  `name_cs` varchar(200) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `urlkey_cs` varchar(200) DEFAULT NULL,
  `text_short_cs` text,
  `text_cs` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `text_clear_cs` text,
  `keywords_cs` varchar(300) DEFAULT NULL,
  `name_en` varchar(200) DEFAULT NULL,
  `urlkey_en` varchar(200) DEFAULT NULL,
  `text_short_en` text,
  `text_en` text,
  `text_clear_en` text,
  `keywords_en` varchar(300) DEFAULT NULL,
  `name_de` varchar(200) DEFAULT NULL,
  `urlkey_de` varchar(200) DEFAULT NULL,
  `text_short_de` text,
  `text_de` text,
  `text_clear_de` text,
  `keywords_de` varchar(300) DEFAULT NULL,
  `name_sk` varchar(200) CHARACTER SET utf8 COLLATE utf8_slovak_ci DEFAULT NULL,
  `urlkey_sk` varchar(200) DEFAULT NULL,
  `text_short_sk` text,
  `text_sk` text CHARACTER SET utf8 COLLATE utf8_slovak_ci,
  `text_clear_sk` text,
  `keywords_sk` varchar(300) DEFAULT NULL,
  `price` float DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL,
  `unit_size` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT '-1',
  `product_stock` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Povolení skladu',
  `weight` float DEFAULT NULL,
  `discount` tinyint(4) DEFAULT '0',
  `deleted` tinyint(4) DEFAULT '0',
  `showed` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(4) DEFAULT '1',
  `image` varchar(100) DEFAULT NULL,
  `date_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `personal_pickup_only` tinyint(1) DEFAULT '0',
  `required_pickup_date` tinyint(1) DEFAULT '0',
  `is_new_to_date` date DEFAULT NULL,
  PRIMARY KEY (`id_product`),
  KEY `fk_id_category` (`id_category`),
  KEY `fk_id_tax` (`id_tax`),
  KEY `urlkey_cs` (`urlkey_cs`),
  KEY `urlkey_en` (`urlkey_en`),
  KEY `urlkey_de` (`urlkey_de`),
  KEY `urlkey_sk` (`urlkey_sk`),
  FULLTEXT KEY `text_cs_fulltext` (`text_clear_cs`),
  FULLTEXT KEY `text_en_fulltext` (`text_clear_en`),
  FULLTEXT KEY `text_sk_fulltext` (`text_clear_sk`),
  FULLTEXT KEY `text_de_fulltext` (`text_clear_de`),
  FULLTEXT KEY `name_cs_fulltext` (`name_cs`),
  FULLTEXT KEY `name_en_fulltext` (`name_en`),
  FULLTEXT KEY `name_sk_fulltext` (`name_sk`),
  FULLTEXT KEY `name_de_fulltext` (`name_de`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}shop_products_variants`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}shop_products_variants` (
  `id_variant` int(11) NOT NULL AUTO_INCREMENT,
  `id_product` int(11) NOT NULL,
  `id_attribute` int(11) NOT NULL,
  `variant_price_add` float DEFAULT '0',
  `variant_code_add` varchar(100) DEFAULT NULL,
  `variant_weight_add` float DEFAULT '0',
  `variant_is_default` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_variant`),
  KEY `fkeys` (`id_product`,`id_attribute`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}shop_shippings`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}shop_shippings` (
  `id_shipping` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_zone` smallint(6) NOT NULL,
  `shipping_name_cs` varchar(200) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `shipping_name_en` varchar(100) DEFAULT NULL,
  `shipping_name_de` varchar(100) DEFAULT NULL,
  `shipping_name_sk` varchar(100) CHARACTER SET utf8 COLLATE utf8_slovak_ci DEFAULT NULL,
  `shipping_price` varchar(20) NOT NULL DEFAULT '0',
  `payments_disallowed` varchar(50) DEFAULT NULL,
  `shipping_text_cs` varchar(500) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `shipping_text_en` varchar(500) DEFAULT NULL,
  `shipping_text_sk` varchar(500) CHARACTER SET ucs2 COLLATE ucs2_slovenian_ci DEFAULT NULL,
  `shipping_text_de` varchar(500) DEFAULT NULL,
  `shipping_is_personal_pickup` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_shipping`),
  KEY `id_zone` (`id_zone`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Vypisuji data pro tabulku `{PREFIX}shop_shippings`
--

INSERT INTO `{PREFIX}shop_shippings` (`id_shipping`, `id_zone`, `shipping_name_cs`, `shipping_name_en`, `shipping_name_de`, `shipping_name_sk`, `shipping_price`, `payments_disallowed`, `shipping_text_cs`, `shipping_text_en`, `shipping_text_sk`, `shipping_text_de`, `shipping_is_personal_pickup`) VALUES
(1, 0, 'Osobní odběr', 'Osobní odběr', NULL, NULL, '0', '2;3', NULL, NULL, NULL, NULL, 1),
(2, 0, 'Česká pošta', 'Česká pošta', NULL, NULL, '100', '1', NULL, NULL, NULL, NULL, 0),
(3, 0, 'PPL obchodní balík', 'PPL obchodní balík', NULL, NULL, '120', '1', NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}shop_tax`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}shop_tax` (
  `id_tax` smallint(6) NOT NULL AUTO_INCREMENT,
  `tax_name` varchar(50) NOT NULL,
  `tax_value` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_tax`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Vypisuji data pro tabulku `{PREFIX}shop_tax`
--

INSERT INTO `{PREFIX}shop_tax` (`id_tax`, `tax_name`, `tax_value`) VALUES
(1, 'Bez daně', 0),
(2, 'DPH 20%', 20),
(3, 'DPH 14%', 14);

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}shop_zones`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}shop_zones` (
  `id_zone` smallint(6) NOT NULL AUTO_INCREMENT,
  `zone_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id_zone`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Vypisuji data pro tabulku `{PREFIX}shop_zones`
--

INSERT INTO `{PREFIX}shop_zones` (`id_zone`, `zone_name`) VALUES
(1, 'Česká Republika');


CREATE TABLE IF NOT EXISTS `{PREFIX}shop_customers` (
  `id_customer` INT NOT NULL AUTO_INCREMENT ,
  `id_user` INT NOT NULL ,
  `id_customer_group` INT NOT NULL ,
  `customer_phone` VARCHAR(15) NULL DEFAULT NULL ,
  `customer_company` varchar(70) DEFAULT NULL,
  `customer_street` varchar(70) DEFAULT NULL,
  `customer_city` varchar(50) DEFAULT NULL,
  `customer_psc` varchar(6) DEFAULT NULL,
  `customer_ic` varchar(20) DEFAULT NULL,
  `customer_dic` varchar(20) DEFAULT NULL,
  `customer_delivery_name` varchar(70) DEFAULT NULL,
  `customer_delivery_street` varchar(70) DEFAULT NULL,
  `customer_delivery_city` varchar(50) DEFAULT NULL,
  `customer_delivery_psc` varchar(6) DEFAULT NULL,
  PRIMARY KEY (`id_customer`) ,
  INDEX `fk_user` (`id_user` ASC)
) ENGINE = MyISAM DEFAULT CHARSET=utf8 COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `{PREFIX}shop_customers_groups` (
  `id_customer_group` INT NOT NULL AUTO_INCREMENT ,
  `customer_group_name` VARCHAR(50) NOT NULL ,
  `customer_group_reduction` SMALLINT NULL DEFAULT 0 ,
  `customer_group_deleted` TINYINT(1) NULL DEFAULT 0 ,
  PRIMARY KEY (`id_customer_group`)
)ENGINE = MyISAM DEFAULT CHARSET = utf8 COLLATE = utf8_general_ci;

INSERT INTO `{PREFIX}shop_customers_groups` (`customer_group_name`, `customer_group_reduction`) VALUES ('Základní', '0');


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
