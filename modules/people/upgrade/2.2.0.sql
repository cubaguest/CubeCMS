-- remove deleted records
ALTER TABLE `{PREFIX}people` 
CHANGE COLUMN `person_social_url` `person_social_url` VARCHAR(200) NULL DEFAULT NULL,
ADD COLUMN `person_facebook_url` VARCHAR(200) NULL DEFAULT NULL,
ADD COLUMN `person_twitter_url` VARCHAR(200) NULL DEFAULT NULL,
ADD COLUMN `person_gplus_url` VARCHAR(200) NULL DEFAULT NULL,
ADD COLUMN `person_instagram_url` VARCHAR(200) NULL DEFAULT NULL
;
