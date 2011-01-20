CREATE TABLE IF NOT EXISTS `{PREFIX}mails_send_queue` (
  `id_mail` INT NOT NULL AUTO_INCREMENT ,
  `mail` VARCHAR(100) NOT NULL ,
  `name` VARCHAR(100) NULL ,
  `undeliverable` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_mail`) ,
  UNIQUE INDEX `id_mail_UNIQUE` (`id_mail` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;
