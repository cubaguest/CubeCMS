/* UPDATE_MAIN_SITE */
-- new config values
INSERT INTO `cubecms_global_config` (`key`, `label`, `value`, `protected`, `type`, `id_group`) 
VALUES
('IMAGE_COMPRESS_QUALITY', 'kvalita komprese obrázků', 90, false, 'number', 7)
;

UPDATE `cubecms_global_config` SET `values` = 'left;right;bottom;top;center' WHERE `cubecms_global_config`.`key` = 'PANEL_TYPES';

INSERT INTO `cubecms_global_config_groups` (`id_group`, `name_cs`, `name_sk`, `name_en`, `name_de`, `desc_cs`, `desc_sk`, `desc_en`, `desc_de`) VALUES
(20, 'Moduly', 'Moduly', 'Modules', 'Module', 'Nastavení modulů', 'Nastavenie modulov', 'Modules Settings', NULL);

-- deprecated
DELETE FROM `cubecms_global_config` WHERE `cubecms_global_config`.`key` = 'HEADLINE_SEPARATOR';
/* END_UPDATE */

-- Sloupec pro povolení zobrazení v mobilním zařízení
ALTER TABLE `{PREFIX}categories` ADD COLUMN `allow_handle_access` tinyint(1) DEFAULT 0 ;

