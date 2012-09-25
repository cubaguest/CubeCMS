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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
  PRIMARY KEY (`id_shipping`),
  KEY `id_zone` (`id_zone`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}shop_tax`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}shop_tax` (
  `id_tax` smallint(6) NOT NULL AUTO_INCREMENT,
  `tax_name` varchar(50) NOT NULL,
  `tax_value` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_tax`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `{PREFIX}shop_zones`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}shop_zones` (
  `id_zone` smallint(6) NOT NULL AUTO_INCREMENT,
  `zone_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id_zone`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `{PREFIX}shop_zones` (`id_zone`, `zone_name`) VALUES
(1, 'Česká Republika');