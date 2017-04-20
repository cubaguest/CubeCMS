CREATE TABLE IF NOT EXISTS `{PREFIX}dwfiles` (
  `id_dwfile` INT NOT NULL AUTO_INCREMENT,
  `id_dwfile_section` INT NULL DEFAULT 1,
  `id_category` INT NULL DEFAULT 1,
  `id_user` INT NOT NULL,
  `dwfile_name_cs` varchar(200) CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL,
  `dwfile_text_cs` varchar(500) CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL,
  `dwfile_name_en` varchar(200) CHARACTER SET utf8 NULL DEFAULT NULL,
  `dwfile_text_en` varchar(500) CHARACTER SET utf8 NULL DEFAULT NULL,
  `dwfile_name_de` varchar(200) CHARACTER SET utf8 NULL DEFAULT NULL,
  `dwfile_text_de` varchar(500) CHARACTER SET utf8 NULL DEFAULT NULL,
  `dwfile_name_sk` varchar(200) CHARACTER SET utf8 COLLATE utf8_slovak_ci NULL DEFAULT NULL,
  `dwfile_text_sk` varchar(500) CHARACTER SET utf8 COLLATE utf8_slovak_ci NULL DEFAULT NULL,
  `dwfile` varchar(100) CHARACTER SET utf8 NOT NULL,
  `time_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dwfile_column` smallint(3) DEFAULT '1',
  `dwfile_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_dwfile`),
  KEY `id_sec` (`id_dwfile_section`),
  FULLTEXT KEY `dwfile_name_cs` (`dwfile_name_cs`),
  FULLTEXT KEY `dwfile_name_en` (`dwfile_name_en`),
  FULLTEXT KEY `dwfile_name_de` (`dwfile_name_de`),
  FULLTEXT KEY `dwfile_name_sk` (`dwfile_name_sk`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


INSERT INTO `cubecms_global_config`
(`key`, `label`, `value`, `values`, `protected`, `type`, `id_group`, `callback_func`) VALUES
('MODULE_DOWNLOADFILES_COLS', 'Počet sloupců se soubory', '1',     NULL, 0, 'number', 20, NULL)
   ON DUPLICATE KEY UPDATE `callback_func`= NULL;


CREATE TABLE IF NOT EXISTS `{PREFIX}dwfiles_sections` (
  `id_dwfile_section` INT NOT NULL AUTO_INCREMENT,
  `id_category` INT NULL,
  `dwsection_name_cs` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `dwsection_name_en` VARCHAR(50) CHARACTER SET utf8 NULL DEFAULT NULL,
  `dwsection_order` INT NULL DEFAULT 0,
  `dwsection_password` VARCHAR(50) NULL DEFAULT NULL,
  PRIMARY KEY (`id_dwfile_section`),
   INDEX `ord` (`id_category` ASC, `dwsection_order` ASC))
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;
