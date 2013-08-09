-- AUTORUN

CREATE TABLE IF NOT EXISTS `{PREFIX}autorun` (
  `id_autorun` INT NOT NULL AUTO_INCREMENT ,
  `autorun_module_name` VARCHAR(20) NOT NULL ,
  `autorun_period` VARCHAR(10) NOT NULL DEFAULT 'daily' ,
  `autorun_url` VARCHAR(200) NULL DEFAULT NULL ,
  PRIMARY KEY (`id_autorun`) ,
  INDEX `period` (`autorun_period` ASC) 
) DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;
