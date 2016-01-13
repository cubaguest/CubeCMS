-- remove deleted records
ALTER TABLE `{PREFIX}people` 
CHANGE COLUMN `person_text` `person_text_cs` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL ,
CHANGE COLUMN `person_text_clear` `person_text_clear_cs` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL;
;
