/* UPDATE_MAIN_SITE */
/* END_UPDATE */

ALTER TABLE `{PREFIX}panels` 
ADD COLUMN `is_admin_cat` TINYINT(1) NULL DEFAULT 0,
CHANGE `id_panel` `id_panel` INT UNSIGNED NOT NULL AUTO_INCREMENT, 
CHANGE `id_cat` `id_cat` INT UNSIGNED NOT NULL DEFAULT '0' COMMENT 'id kategorie panelu', 
CHANGE `id_show_cat` `id_show_cat` INT UNSIGNED NULL DEFAULT '0' COMMENT 'id kategorie ve které se má daný panel zobrazit';