-- remove deleted records
ALTER TABLE `{PREFIX}people` 
ADD COLUMN `person_email` VARCHAR(50) NULL DEFAULT NULL,
ADD COLUMN `person_phone` VARCHAR(20) NULL DEFAULT NULL,
ADD COLUMN `person_social_url` VARCHAR(100) NULL DEFAULT NULL;
