-- Update na verzi 8.0.1
CREATE TABLE `{PREFIX}users_settings` (
  `id_user_setting` INT NOT NULL AUTO_INCREMENT,
  `id_user` INT NOT NULL,
  `setting_name` VARCHAR(50) NULL DEFAULT NULL,
  `setting_value` VARCHAR(500) NULL,
  PRIMARY KEY (`id_user_setting`),
  INDEX `id_user` (`id_user` ASC),
  INDEX `user_setting` (`setting_name` ASC, `id_user` ASC));
