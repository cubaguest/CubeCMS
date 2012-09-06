CREATE  TABLE IF NOT EXISTS `{PREFIX}mails_newsletters_templates` (
  `id_newsletter_template` INT NOT NULL ,
  `newsletter_template_name` VARCHAR(100) NOT NULL ,
  `newsletter_template_deleted` TINYINT NULL DEFAULT 0 ,
  `newsletter_template_text` TEXT NULL DEFAULT NULL ,
  `newsletter_template_html` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`id_newsletter_template`) 
) ENGINE = MyISAM DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `{PREFIX}mails_newsletters_queue` (
  `id_newsletter_queue` INT NOT NULL ,
  `id_newsletter` INT NOT NULL ,
  `newsletter_queue_mail` VARCHAR(45) NULL ,
  `newsletter_queue_sended` VARCHAR(45) NULL ,
  PRIMARY KEY (`id_newsletter_queue`, `id_newsletter`) ,
  INDEX `fk_newsletter` (`id_newsletter` ASC) 
) ENGINE = MyISAM DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `{PREFIX}mails_newsletters` (
  `id_newsletter` INT NOT NULL ,
  `id_newsletter_template` INT NOT NULL ,
  `id_user` INT NOT NULL ,
  `newsletter_subject` VARCHAR(100) NULL ,
  `newsletter_date_send` DATE NULL ,
  `newsletter_deleted` TINYINT NULL DEFAULT 0 ,
  `newsletter_active` TINYINT NULL DEFAULT 0 ,
  `newsletter_content_text` TEXT NULL ,
  `newsletter_content_html` TEXT NULL ,
  PRIMARY KEY (`id_newsletter`) ,
  INDEX `fk_users` (`id_user` ASC) ,
  INDEX `fk_newsletters_templates` (`id_newsletter_template` ASC) 
) ENGINE = MyISAM DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;