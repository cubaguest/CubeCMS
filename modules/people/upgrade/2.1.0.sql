-- remove deleted records
ALTER TABLE `{PREFIX}people` 
CHANGE COLUMN `person_label` `person_label_cs` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL
;
