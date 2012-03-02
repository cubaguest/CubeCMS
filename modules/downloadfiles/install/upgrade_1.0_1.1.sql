ALTER TABLE `{PREFIX}dwfiles` ADD `dwfile_column` smallint(3) CHARACTER SET utf8 DEFAULT '1';

INSERT INTO `cubecms_global_config` 
(`key`, `label`, `value`, `values`, `protected`, `type`, `id_group`, `callback_func`) VALUES
('MODULE_DOWNLOADFILES_COLS', 'Počet sloupců se soubory', '1',     NULL, 0, 'number', 20, NULL)
   ON DUPLICATE KEY UPDATE `callback_func`= NULL;