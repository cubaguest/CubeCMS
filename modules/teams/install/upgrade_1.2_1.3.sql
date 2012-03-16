-- Default global values
ALTER TABLE `{PREFIX}teams_persons` 
ADD COLUMN `person_work` VARCHAR(50) NULL DEFAULT NULL  AFTER `person_link` ;