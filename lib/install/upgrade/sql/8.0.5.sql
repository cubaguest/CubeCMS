/* UPDATE_MAIN_SITE */
INSERT INTO `cubecms_global_config` ( `key` , `label` , `value` , `values` , `protected` , `type` , `id_group` , `callback_func` , `hidden_value` )
VALUES ('GOOGLE_PLUS_PAGE_URL', 'Adresa stránky na Google+', NULL , NULL , '0', 'string', '11', NULL , '0');
/* END_UPDATE */

ALTER TABLE `{PREFIX}categories` ADD INDEX `module` (`module` ASC);
ALTER TABLE `{PREFIX}modules_instaled` ADD UNIQUE INDEX `name_UNIQUE` (`name` ASC);
