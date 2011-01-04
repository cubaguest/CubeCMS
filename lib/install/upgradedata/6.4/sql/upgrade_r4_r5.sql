INSERT INTO `{PREFIX}config` (`key`, `label`, `value`, `protected`, `type`) VALUES
('IMAGE_THUMB_W', 'Výchozí šířka miniatury', '150', NULL, 0, 'number'),
('IMAGE_THUMB_H', 'Výchozí výška miniatury', '150', NULL, 0, 'number'),
('IMAGE_THUMB_CROP', 'Ořezávat miniatury', true, 0, 'bool');

-- tabulka pro sessions
CREATE TABLE `{PREFIX}sessions` (
  `session_key` VARCHAR(32) NOT NULL ,
  `value` BLOB NULL ,
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `updated` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `ip` VARCHAR(55) NULL DEFAULT NULL ,
  `id_user` INT NULL DEFAULT 0 ,
  PRIMARY KEY (`session_key`) ,
  UNIQUE INDEX `ssession_key_UNIQUE` (`session_key` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;