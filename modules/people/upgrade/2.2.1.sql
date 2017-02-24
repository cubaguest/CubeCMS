-- remove deleted records
ALTER TABLE `{PREFIX}people` 
ADD COLUMN `person_linkedin_url` VARCHAR(200) NULL DEFAULT NULL
;
