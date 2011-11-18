CREATE  TABLE `{PREFIX}daymenu` (
  `id_daymenu` INT NOT NULL AUTO_INCREMENT ,
  `daymenu_date` DATE NOT NULL ,
  `daymenu_text` TEXT NULL DEFAULT NULL ,
  `daymenu_text_panel` TEXT NULL DEFAULT NULL ,
  `daymenu_text_clear` TEXT NULL DEFAULT NULL ,
  `daymenu_concept` TINYINT(1)  NULL DEFAULT 0 ,
  PRIMARY KEY (`id_daymenu`) ,
  FULLTEXT INDEX `fulltext` (`daymenu_text_clear` ASC) ,
  INDEX `date` (`daymenu_date` DESC) )
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_czech_ci;
