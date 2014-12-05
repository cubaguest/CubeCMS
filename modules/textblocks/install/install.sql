CREATE TABLE `{PREFIX}texts_blocks` (
  `id_text_block` INT NOT NULL AUTO_INCREMENT,
  `id_category` INT NOT NULL,
  `block_text_name_cs` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL,
  `block_text_cs` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL,
  `block_text_clear_cs` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL,
  `block_text_name_en` VARCHAR(100) NULL,
  `block_text_en` TEXT NULL,
  `block_text_clear_en` TEXT NULL,
  `block_text_name_de` VARCHAR(100) NULL,
  `block_text_de` TEXT NULL,
  `block_text_clear_de` TEXT NULL,
  `block_order` SMALLINT NOT NULL DEFAULT 0,
  `block_image` VARCHAR(50) NULL DEFAULT NULL,
  PRIMARY KEY (`id_text_block`),
  INDEX `idc` (`id_category` ASC))
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;
