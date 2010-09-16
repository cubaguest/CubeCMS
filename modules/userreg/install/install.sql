CREATE  TABLE `{PREFIX}userreg_queue` (
  `id_request` smallint(6) NOT NULL AUTO_INCREMENT ,
  `id_category` smallint(6) NOT NULL ,
  `hash` VARCHAR(45) NULL ,
  `username` VARCHAR(45) NULL ,
  `pass` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL ,
  `surname` VARCHAR(45) NULL ,
  `name` VARCHAR(45) NULL ,
  `mail` VARCHAR(70) NULL ,
  `phone` VARCHAR(45) NULL ,
  `timeadd` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `ipaddress` VARCHAR(15) NULL ,
  PRIMARY KEY (`id_request`) ,
  UNIQUE INDEX `hash_UNIQUE` (`hash` ASC) )
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;
