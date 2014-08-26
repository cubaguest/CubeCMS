-- add column last edit user and time, add column with keywords and desc
ALTER TABLE `{PREFIX}projects` 
ADD COLUMN `project_order` INT NULL DEFAULT 0,
ADD INDEX `base` (`id_project_section` ASC, `project_order` ASC);


ALTER TABLE `{PREFIX}projects_sections` 
DROP INDEX `id_category` ,
ADD INDEX `id_category` (`id_category` ASC),
ADD INDEX `base` (`id_category` ASC, `section_urlkey` ASC);
