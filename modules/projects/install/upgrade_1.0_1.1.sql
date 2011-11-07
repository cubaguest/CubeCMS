ALTER TABLE `{PREFIX}projects_sections` ADD COLUMN `section_weight` SMALLINT NULL DEFAULT 0  AFTER `section_time_add`;
ALTER TABLE `{PREFIX}projects` 
ADD COLUMN `project_weight` SMALLINT NULL DEFAULT 0  AFTER `project_related`,
ADD COLUMN `project_name_short` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL  AFTER `project_name` , 
CHANGE COLUMN `project_name` `project_name` VARCHAR(300) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NOT NULL;
