-- Update na verzi 8.0.0

DELETE FROM `{PREFIX}config` WHERE `key` = 'RELEASE';

-- tabulka pro redirecty
CREATE TABLE IF NOT EXISTS `{PREFIX}category_redirect` (
  `id_category_redirect` INT NOT NULL AUTO_INCREMENT,
  `id_category` INT NULL,
  `lang` VARCHAR(2) NULL,
  `redirect_from` VARCHAR(100) NULL,
  PRIMARY KEY (`id_category_redirect`),
  INDEX `id_cat` (`id_category` ASC),
  INDEX `lang_id_cat` (`id_category` ASC, `lang` ASC)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;


-- pridani verze do tabulky modulu
ALTER TABLE `{PREFIX}modules_instaled`
ADD COLUMN `version` VARCHAR(5) NOT NULL DEFAULT '1.0.0';

UPDATE `{PREFIX}modules_instaled` SET `version` = CONCAT(`version_major`, '.', `version_minor`, '.0');