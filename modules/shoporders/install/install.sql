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
