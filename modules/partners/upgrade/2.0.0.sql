CREATE TABLE IF NOT EXISTS `{PREFIX}partners_groups` (
  `id_partners_group` INT NOT NULL AUTO_INCREMENT,
  `id_category` INT NOT NULL,
  `partner_group_name_cs` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL,
  `partner_group_name_en` VARCHAR(50) NULL DEFAULT NULL,
  `partner_group_name_de` VARCHAR(50) NULL DEFAULT NULL,
  `partner_group_name_sk` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_slovak_ci' NULL DEFAULT NULL, 
  `partner_group_order` INT NULL DEFAULT 0, 
  PRIMARY KEY (`id_partners_group`),
  INDEX `idc` (`id_category` ASC))
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

SET @s = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE table_name = '{PREFIX}partners'
        AND table_schema = DATABASE()
        AND column_name = 'id_group'
    ) = 1,
    "SELECT 1",
    "ALTER TABLE `{PREFIX}partners` CHANGE COLUMN `id_partner` `id_partner` INT(11) NOT NULL AUTO_INCREMENT, CHANGE COLUMN `id_category` `id_group` INT(11) NOT NULL;"
));

PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;