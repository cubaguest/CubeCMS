ALTER TABLE `{PREFIX}dwfiles` 
ADD COLUMN `id_dwfile_section` INT NULL DEFAULT 1,
CHANGE  `id_dwfile`  `id_dwfile` INT NOT NULL AUTO_INCREMENT,
CHANGE  `id_category`  `id_category` INT NULL DEFAULT 0,
CHANGE  `id_user`  `id_user` INT NULL DEFAULT 0
;

ALTER TABLE `{PREFIX}dwfiles` 
DROP INDEX `id_category` ,
ADD INDEX `id_sec` (`id_dwfile_section` ASC)  COMMENT '';


CREATE TABLE IF NOT EXISTS `{PREFIX}dwfiles_sections` (
  `id_dwfile_section` INT NOT NULL AUTO_INCREMENT,
  `id_category` INT NULL,
  `dwsection_name_cs` VARCHAR(45) NULL DEFAULT NULL,
  `dwsection_name_en` VARCHAR(45) NULL DEFAULT NULL,
  `dwsection_order` INT NULL DEFAULT 0,
  `dwsection_password` VARCHAR(50) NULL DEFAULT NULL,
  PRIMARY KEY (`id_dwfile_section`),
   INDEX `ord` (`id_category` ASC, `dwsection_order` ASC))
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;
