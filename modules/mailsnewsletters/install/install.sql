CREATE TABLE `{PREFIX}mails_newsletters` (
  `id_newsletter` int(11) NOT NULL AUTO_INCREMENT,
  `id_newsletter_template` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `newsletter_subject` varchar(100) DEFAULT NULL,
  `newsletter_date_send` date DEFAULT NULL,
  `newsletter_deleted` tinyint(4) DEFAULT '0',
  `newsletter_active` tinyint(4) DEFAULT '0',
  `newsletter_content` text,
  `newsletter_groups_ids` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id_newsletter`),
  KEY `fk_users` (`id_user`),
  KEY `fk_newsletters_templates` (`id_newsletter_template`)
) ENGINE = MyISAM DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;

CREATE TABLE `{PREFIX}mails_newsletters_queue` (
  `id_newsletter_queue` int(11) NOT NULL AUTO_INCREMENT,
  `id_newsletter` int(11) NOT NULL,
  `newsletter_queue_mail` varchar(100) NOT NULL,
  `newsletter_queue_name` varchar(100) DEFAULT NULL,
  `newsletter_queue_surname` varchar(100) DEFAULT NULL,
  `newsletter_queue_date_send` date NOT NULL,
  PRIMARY KEY (`id_newsletter_queue`,`id_newsletter`),
  KEY `fk_newsletter` (`id_newsletter`)
) ENGINE = MyISAM DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;

CREATE TABLE `{PREFIX}mails_newsletters_templates` (
  `id_newsletter_template` int(11) NOT NULL AUTO_INCREMENT,
  `newsletter_template_name` varchar(100) NOT NULL,
  `newsletter_template_deleted` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id_newsletter_template`)
) ENGINE = MyISAM DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;

INSERT INTO `{PREFIX}autorun` (`autorun_module_name`, `autorun_period`, `autorun_url`) 
VALUES ('mailsnewsletters', 'hourly', NULL);