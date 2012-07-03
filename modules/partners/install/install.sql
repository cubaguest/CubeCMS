CREATE TABLE IF NOT EXISTS `{PREFIX}partners` (
  `id_partner` int(11) NOT NULL AUTO_INCREMENT ,
  `id_category` int(11) NOT NULL ,
  `partner_name` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NOT NULL ,
  `partner_note` VARCHAR(1000) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL ,
  `partner_text` VARCHAR(1000) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL ,
  `partner_url` VARCHAR(500) NULL DEFAULT NULL ,
  `partner_image` VARCHAR(100) NULL DEFAULT NULL ,
  `partner_order` SMALLINT NULL DEFAULT 0 ,
  `partner_disabled` TINYINT(1) NULL DEFAULT 0 ,
  PRIMARY KEY (`id_partner`) ,
  INDEX `id_category` (`id_category` ASC) 
) ENGINE=MyISAM DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;
