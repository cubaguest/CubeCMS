CREATE TABLE IF NOT EXISTS `{PREFIX}teams` (
  `id_category` int(11) NOT NULL,
  `id_team` int(11) NOT NULL AUTO_INCREMENT,
  `team_name` varchar(100) DEFAULT NULL,
  `team_order` smallint(6) DEFAULT '0',
  `team_name_cs` varchar(100) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `team_name_en` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id_team`),
  KEY `id_category` (`id_category`)
) ENGINE=MyISAM  DEFAULT CHARSET = utf8 COLLATE = utf8_general_ci AUTO_INCREMENT=1;

ALTER TABLE `{PREFIX}teams` CHANGE `team_name_cs` `team_name_cs` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL, 
CHANGE `team_name_en` `team_name_en` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

CREATE TABLE IF NOT EXISTS `{PREFIX}teams_persons` (
  `id_person` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_team` smallint(6) NOT NULL,
  `person_name` varchar(50) COLLATE utf8_general_ci NOT NULL,
  `person_surname` varchar(50) COLLATE utf8_general_ci NOT NULL,
  `person_degree` varchar(20) COLLATE utf8_general_ci DEFAULT NULL,
  `person_degree_after` varchar(20) COLLATE utf8_general_ci DEFAULT NULL,
  `person_text` text COLLATE utf8_czech_ci,
  `person_text_clear` text COLLATE utf8_czech_ci,
  `person_order` smallint(6) DEFAULT '0',
  `person_deleted` tinyint(1) DEFAULT '0',
  `person_image` varchar(100) COLLATE utf8_general_ci DEFAULT NULL,
  `person_link` varchar(300) COLLATE utf8_general_ci DEFAULT NULL,
  `person_work` varchar(50) COLLATE utf8_general_ci DEFAULT NULL,
  `person_text_cs` text COLLATE utf8_czech_ci,
  `person_text_clear_cs` text COLLATE utf8_czech_ci,
  `person_text_en` text COLLATE utf8_general_ci,
  `person_text_clear_en` text COLLATE utf8_general_ci,
  `person_phone` varchar(20) COLLATE utf8_general_ci DEFAULT NULL,
  `person_email` varchar(50) COLLATE utf8_general_ci DEFAULT NULL,
  `person_social_url` varchar(200) COLLATE utf8_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_person`),
  KEY `id_team` (`id_team`),
  FULLTEXT KEY `name` (`person_name`),
  FULLTEXT KEY `surname` (`person_surname`),
  FULLTEXT KEY `text_clear` (`person_text_clear`),
  FULLTEXT KEY `person_text_clear_cs` (`person_text_clear_cs`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

ALTER TABLE `{PREFIX}teams_persons` CHANGE `person_surname` `person_surname` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

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
('MODULE_TEAMS_CROPING', 'Modul teams - ořez portrétu', 0, NULL, 0, 'bool', 20, NULL)
   ON DUPLICATE KEY UPDATE `value`= 0;
