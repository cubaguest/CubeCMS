-- vynucené globální panely
ALTER TABLE `{PREFIX}panels` 
ADD COLUMN `panel_force_global` TINYINT(1) NULL DEFAULT 0;