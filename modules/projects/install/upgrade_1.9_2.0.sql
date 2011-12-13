-- add column for thumbnail, change column image
ALTER TABLE `{PREFIX}projects` 
ADD COLUMN `project_thumb` VARCHAR(50) NULL DEFAULT NULL  AFTER `project_image` , 
ADD COLUMN `project_tpl_params` VARCHAR(500) NULL DEFAULT NULL  AFTER `project_weight` ,
CHANGE COLUMN `project_image` `project_image` VARCHAR(50) NULL DEFAULT NULL;


-- set previous images
UPDATE `{PREFIX}projects` SET `project_image` = 'main.jpg', `project_thumb` = 'main_thum.jpg';