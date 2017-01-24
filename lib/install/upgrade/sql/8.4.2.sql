/* UPDATE_MAIN_SITE */
/* END_UPDATE */
-- CREATE TABLE `{PREFIX}custom_menu` ( 
-- `id_custom_menu` INT NOT NULL AUTO_INCREMENT , 
-- `custom_menu_name_cs` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL , 
-- `custom_menu_name_en` VARCHAR(50) NULL DEFAULT NULL , 
-- `custom_menu_name_sk` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_slovak_ci NULL DEFAULT NULL , 
-- `static_box` VARCHAR(20) NULL DEFAULT NULL , 
-- PRIMARY KEY (`id_custom_menu`), INDEX (`static_box`)) 
-- DEFAULT CHARACTER SET = utf8
-- COLLATE = utf8_general_ci;
-- 
ALTER TABLE `{PREFIX}custom_menu_items` 
ADD `id_lft` INT NOT NULL DEFAULT 0, 
ADD `id_rgt` INT NOT NULL DEFAULT 0, 
ADD `level` INT NOT NULL DEFAULT 1,
ADD `is_tpl_menu` TINYINT(1) NOT NULL DEFAULT '0',
ADD INDEX (`id_lft`, `id_rgt`),
ADD INDEX (`level`);