-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb1
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Stř 06. bře 2013, 09:17
-- Verze MySQL: 5.5.29
-- Verze PHP: 5.4.6-1ubuntu1.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Databáze: `cube_cms`
--

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
  `attribute_order` int(11) DEFAULT '0',
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
  `atgroup_order` int(11) DEFAULT '0',
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
-- Struktura tabulky `{PREFIX}shop_customers`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}shop_customers` (
  `id_customer` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_customer_group` int(11) NOT NULL,
  `customer_phone` varchar(15) DEFAULT NULL,
  `customer_company` varchar(70) DEFAULT NULL,
  `customer_street` varchar(70) DEFAULT NULL,
  `customer_city` varchar(50) DEFAULT NULL,
  `customer_psc` varchar(6) DEFAULT NULL,
  `id_country` int(11) DEFAULT '0',
  `customer_ic` varchar(20) DEFAULT NULL,
  `customer_dic` varchar(20) DEFAULT NULL,
  `customer_delivery_name` varchar(70) DEFAULT NULL,
  `customer_delivery_street` varchar(70) DEFAULT NULL,
  `customer_delivery_city` varchar(50) DEFAULT NULL,
  `customer_delivery_psc` varchar(6) DEFAULT NULL,
  `id_delivery_country` int(11) DEFAULT '0',
  PRIMARY KEY (`id_customer`),
  KEY `fk_user` (`id_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}shop_customers_groups`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}shop_customers_groups` (
  `id_customer_group` int(11) NOT NULL AUTO_INCREMENT,
  `customer_group_name` varchar(50) NOT NULL,
  `customer_group_reduction` smallint(6) DEFAULT '0',
  `customer_group_deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_customer_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Vypisuji data pro tabulku `{PREFIX}shop_customers_groups`
--

INSERT INTO `{PREFIX}shop_customers_groups` (`id_customer_group`, `customer_group_name`, `customer_group_reduction`, `customer_group_deleted`) VALUES
(1, 'Základní', 0, 0),
(2, 'Stálý', 5, 0);

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

-- --------------------------------------------------------

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Vypisuji data pro tabulku `{PREFIX}shop_payments`
--

INSERT INTO `{PREFIX}shop_payments` (`id_payment`, `payment_class`, `payment_name_cs`, `payment_text_cs`, `payment_name_sk`, `payment_text_sk`, `payment_name_en`, `payment_text_en`, `payment_name_de`, `payment_text_de`, `payment_settings`, `price_add`) VALUES
(1, NULL, 'Hotově', 'Platba při převzení', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0'),
(2, NULL, 'Dobírka', 'Platba za zboží bude provedena při převzení zboží od doručovací firmy', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '30'),
(3, NULL, 'Platba na účet předem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0'),
(4, NULL, 'Platba kartou', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '3%');

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}shop_products_combinations`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}shop_products_combinations` (
  `id_product_combination` int(11) NOT NULL AUTO_INCREMENT,
  `id_product` int(11) NOT NULL,
  `product_combination_quantity` int(11) DEFAULT '0',
  `product_combination_is_default` tinyint(1) DEFAULT '0',
  `product_combination_price_add` float DEFAULT '0',
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
  `product_code` varchar(100) DEFAULT NULL,
  `product_name_cs` varchar(200) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `product_urlkey_cs` varchar(200) DEFAULT NULL,
  `product_text_short_cs` text,
  `product_text_cs` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `product_text_clear_cs` text,
  `product_keywords_cs` varchar(300) DEFAULT NULL,
  `product_name_en` varchar(200) DEFAULT NULL,
  `product_urlkey_en` varchar(200) DEFAULT NULL,
  `product_text_short_en` text,
  `product_text_en` text,
  `product_text_clear_en` text,
  `product_keywords_en` varchar(300) DEFAULT NULL,
  `product_name_de` varchar(200) DEFAULT NULL,
  `product_urlkey_de` varchar(200) DEFAULT NULL,
  `product_text_short_de` text,
  `product_text_de` text,
  `product_text_clear_de` text,
  `product_keywords_de` varchar(300) DEFAULT NULL,
  `product_name_sk` varchar(200) CHARACTER SET utf8 COLLATE utf8_slovak_ci DEFAULT NULL,
  `product_urlkey_sk` varchar(200) DEFAULT NULL,
  `product_text_short_sk` text,
  `product_text_sk` text CHARACTER SET utf8 COLLATE utf8_slovak_ci,
  `product_text_clear_sk` text,
  `product_keywords_sk` varchar(300) DEFAULT NULL,
  `product_price` float DEFAULT NULL,
  `product_unit` varchar(20) DEFAULT NULL,
  `product_unit_size` int(11) DEFAULT NULL,
  `product_quantity` int(11) DEFAULT '-1',
  `product_stock` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Povolení skladu',
  `product_weight` float DEFAULT NULL,
  `product_discount` tinyint(4) DEFAULT '0',
  `product_deleted` tinyint(4) DEFAULT '0',
  `product_showed` int(11) NOT NULL DEFAULT '0',
  `product_active` tinyint(4) DEFAULT '1',
  `product_image` varchar(100) DEFAULT NULL,
  `product_date_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `product_personal_pickup_only` tinyint(1) DEFAULT '0',
  `product_required_pickup_date` tinyint(1) DEFAULT '0',
  `product_is_new_to_date` date DEFAULT NULL,
  `product_order` int(11) DEFAULT '0',
  PRIMARY KEY (`id_product`),
  KEY `fk_id_category` (`id_category`),
  KEY `fk_id_tax` (`id_tax`),
  KEY `urlkey_cs` (`product_urlkey_cs`),
  KEY `urlkey_en` (`product_urlkey_en`),
  KEY `urlkey_de` (`product_urlkey_de`),
  KEY `urlkey_sk` (`product_urlkey_sk`),
  FULLTEXT KEY `text_cs_fulltext` (`product_text_clear_cs`),
  FULLTEXT KEY `text_en_fulltext` (`product_text_clear_en`),
  FULLTEXT KEY `text_sk_fulltext` (`product_text_clear_sk`),
  FULLTEXT KEY `text_de_fulltext` (`product_text_clear_de`),
  FULLTEXT KEY `name_cs_fulltext` (`product_name_cs`),
  FULLTEXT KEY `name_en_fulltext` (`product_name_en`),
  FULLTEXT KEY `name_sk_fulltext` (`product_name_sk`),
  FULLTEXT KEY `name_de_fulltext` (`product_name_de`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Vypisuji data pro tabulku `{PREFIX}shop_shippings`
--

INSERT INTO `{PREFIX}shop_shippings` (`id_shipping`, `id_zone`, `shipping_name_cs`, `shipping_name_en`, `shipping_name_de`, `shipping_name_sk`, `shipping_price`, `payments_disallowed`, `shipping_text_cs`, `shipping_text_en`, `shipping_text_sk`, `shipping_text_de`, `shipping_is_personal_pickup`) VALUES
(1, 0, 'Osobní odběr', 'Osobní odběr', NULL, NULL, '0', '2;3;5', NULL, NULL, NULL, NULL, 1),
(2, 0, 'Česká pošta', 'Česká pošta', NULL, NULL, '80', '1', NULL, NULL, NULL, NULL, 0),
(3, 0, 'PPL obchodní balík', 'PPL obchodní balík', NULL, NULL, '60', '1', NULL, NULL, NULL, NULL, 0),
(4, 0, 'Vlastní doprava', NULL, NULL, NULL, '5%', '1', NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}shop_tax`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}shop_tax` (
  `id_tax` smallint(6) NOT NULL AUTO_INCREMENT,
  `tax_name` varchar(50) NOT NULL,
  `tax_value` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_tax`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Vypisuji data pro tabulku `{PREFIX}shop_tax`
--

INSERT INTO `{PREFIX}shop_tax` (`id_tax`, `tax_name`, `tax_value`) VALUES
(1, 'Bez daně', 0),
(2, 'DPH 20%', 20);

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
