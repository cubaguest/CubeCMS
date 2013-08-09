-- add column last edit user and time, add column with keywords and desc
ALTER TABLE `{PREFIX}projects` 
ADD COLUMN `id_user_last_edit` SMALLINT NOT NULL DEFAULT 0  AFTER `id_user` , 
ADD COLUMN `project_time_edit` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP  AFTER `project_time_add` , 
ADD COLUMN `project_keywords` VARCHAR(300) NULL DEFAULT NULL  AFTER `project_tpl_params` , 
ADD COLUMN `project_desc` VARCHAR(500) NULL DEFAULT NULL  AFTER `project_keywords` , 
CHANGE COLUMN `project_time_add` `project_time_add` DATETIME NOT NULL  ;

-- update timestamp
UPDATE `{PREFIX}projects` SET `project_time_edit` = `project_time_add`;