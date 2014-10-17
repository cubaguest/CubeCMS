/* UPDATE_MAIN_SITE */
INSERT INTO `cubecms_global_config` ( `key` , `label` , `value` , `values` , `protected` , `type` , `id_group` , `callback_func` , `hidden_value` )
VALUES ('MAX_FAILED_LOGINS', 'Maximální počet neúspěšných přihlášení před zablokováním účtu', 10 , NULL , '0', 'number', '3', NULL , '0');

-- ip blocator
CREATE TABLE IF NOT EXISTS `cubecms_global_ipblock` (
  `ip_address` VARBINARY(16) NOT NULL,
  `time_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ip_address`),
  UNIQUE INDEX `ip_address_UNIQUE` (`ip_address` ASC)
);

--   `id_login_attempt` int(10) NOT NULL AUTO_INCREMENT,  AUTO_INCREMENT=1
CREATE TABLE `cubecms_global_login_attempts` (
  `id_user` int(11) NOT NULL,
  `time_login` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_login_attempt`)
) ENGINE=InnoDB;



/* END_UPDATE */
