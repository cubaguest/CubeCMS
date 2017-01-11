ALTER TABLE `{PREFIX}actions` 
CHANGE COLUMN `id_form` `action_id_form` INT(11) NULL DEFAULT NULL  , 
CHANGE COLUMN `form_show_to_date` `action_form_show_to_date` DATETIME NULL DEFAULT NULL  ;