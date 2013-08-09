-- Default global values
INSERT INTO `cubecms_global_config` 
(`key`, `label`, `value`, `values`, `protected`, `type`, `id_group`, `callback_func`) VALUES
('MODULE_TEAMS_IMGW', 'Modul teams - šířka portrétu', 150, NULL, 0, 'number', 20, NULL)
   ON DUPLICATE KEY UPDATE `value`= 150;

INSERT INTO `cubecms_global_config` 
(`key`, `label`, `value`, `values`, `protected`, `type`, `id_group`, `callback_func`) VALUES
('MODULE_TEAMS_IMGH', 'Modul teams - výška portrétu', 200, NULL, 0, 'number', 20, NULL)
   ON DUPLICATE KEY UPDATE `value`= 200;

INSERT INTO `cubecms_global_config` 
(`key`, `label`, `value`, `values`, `protected`, `type`, `id_group`, `callback_func`) VALUES
('MODULE_TEAMS_CROPING', 'Modul teams - ořez portrétu', 'false', NULL, 0, 'bool', 20, NULL)
   ON DUPLICATE KEY UPDATE `value`= 'false';