/* UPDATE_MAIN_SITE */
-- users logins
CREATE TABLE IF NOT EXISTS `{PREFIX}users_logins` (
  `id_user_login` INT NOT NULL AUTO_INCREMENT ,
  `id_user` INT NOT NULL DEFAULT 0 ,
  `user_login_ip` VARCHAR(15) NULL DEFAULT NULL ,
  `user_login_browser` VARCHAR(200) NULL DEFAULT NULL ,
  `user_login_time` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  PRIMARY KEY (`id_user_login`) ,
  INDEX `idu_by_time` (`id_user` ASC, `user_login_time` DESC) 
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;

/* END_UPDATE */
