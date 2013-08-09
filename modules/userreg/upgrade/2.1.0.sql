-- autorun
ALTER TABLE `{PREFIX}userreg_queue` 
ADD COLUMN `note` VARCHAR(500) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL DEFAULT NULL  AFTER `ipaddress` ;
