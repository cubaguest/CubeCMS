/* UPDATE_MAIN_SITE */
-- new config values
INSERT INTO `cubecms_global_config` (`key`, `label`, `value`, `protected`, `type`, `id_group`, `hidden_value`) 
VALUES
('MEMCACHE_SERVER', 'MemCache server - adresa', NULL, false, 'string', 3, false),
('MEMCACHE_PORT', 'MemCache server - port', NULL, false, 'number', 3, false);
/* END_UPDATE */

-- tabulky banneru
CREATE TABLE IF NOT EXISTS `{PREFIX}banners` (
  `id_banner` int(11) NOT NULL AUTO_INCREMENT,
  `banner_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `banner_file` varchar(50) NOT NULL,
  `banner_active` tinyint(1) NOT NULL DEFAULT '1',
  `banner_box` varchar(20) DEFAULT NULL,
  `banner_order` smallint(6) NOT NULL DEFAULT '0',
  `banner_url` varchar(200) DEFAULT NULL,
  `banner_text` varchar(300) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `banner_time_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `banner_new_window` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_banner`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{PREFIX}banners_clicks` (
  `id_banner_click` int(11) NOT NULL AUTO_INCREMENT,
  `id_banner` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
  `banner_click_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `banner_click_ip` int(11) DEFAULT '0',
  `banner_click_browser` varchar(200) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`id_banner_click`),
  KEY `banner` (`id_banner`),
  KEY `timebanner` (`id_banner`,`banner_click_time`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


