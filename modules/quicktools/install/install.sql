CREATE TABLE IF NOT EXISTS `{PREFIX}quicktools` (
  `id_tool` INT NOT NULL ,
  `id_user` SMALLINT NOT NULL ,
  `name` VARCHAR(100) NULL ,
  `url` VARCHAR(300) NULL ,
  `icon` VARCHAR(45) NULL ,
  `order` SMALLINT NULL DEFAULT 0 ,
  PRIMARY KEY (`id_tool`) )
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;
