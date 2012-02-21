CREATE  TABLE `{PREFIX}teams` (
  `id_category` INT NOT NULL ,
  `id_team` INT NOT NULL AUTO_INCREMENT ,
  `team_name` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL DEFAULT NULL ,
  `team_order` smallint(6) DEFAULT 0 ,
  PRIMARY KEY (`id_team`),
  KEY `id_category` (`id_category`)
) ENGINE=MyISAM  DEFAULT CHARSET = utf8 COLLATE = utf8_general_ci AUTO_INCREMENT=1;


CREATE  TABLE `{PREFIX}teams_persons` (
  `id_person` SMALLINT(6) NOT NULL AUTO_INCREMENT,
  `id_team` SMALLINT(6) NOT NULL,
  `person_name` VARCHAR(50) COLLATE utf8_czech_ci NOT NULL,
  `person_surname` VARCHAR(50) COLLATE utf8_czech_ci NOT NULL,
  `person_degree` VARCHAR(20) COLLATE utf8_czech_ci DEFAULT NULL,
  `person_degree_after` VARCHAR(20) NULL DEFAULT NULL,
  `person_text` TEXT CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `person_text_clear` TEXT CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `person_order` SMALLINT(6) DEFAULT '0',
  `person_deleted` TINYINT(1) DEFAULT '0',
  `person_image` VARCHAR(45) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id_person`),
  KEY `id_team` (`id_team`),
  FULLTEXT KEY `name` (`person_name`),
  FULLTEXT KEY `surname` (`person_surname`),
  FULLTEXT KEY `text_clear` (`person_text_clear`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;


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
