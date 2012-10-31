CREATE TABLE IF NOT EXISTS `{PREFIX}mails_addressbook` (
  `id_mail` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_group` smallint(6) NOT NULL DEFAULT '1',
  `name` varchar(30) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `surname` varchar(30) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `mail` varchar(100) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `note` varchar(400) DEFAULT NULL,
  PRIMARY KEY (`id_mail`),
  KEY `GROUP` (`id_group`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `{PREFIX}mails_groups` (
  `id_group` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `note` varchar(400) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id_group`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT INTO `{PREFIX}mails_groups` (`id_group`, `name`, `note`)
VALUES (1,'Základní','Základní skupina');

INSERT INTO `{PREFIX}mails_groups` (`name`, `note`)
VALUES ('Newsletter','Skupina newsletteru');


CREATE TABLE IF NOT EXISTS `{PREFIX}mails_sends` (
  `id_mail` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_user` smallint(6) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `recipients` varchar(2000) CHARACTER SET utf8 DEFAULT NULL,
  `subject` varchar(500) CHARACTER SET utf8 DEFAULT NULL,
  `content` text CHARACTER SET utf8,
  `attachments` varchar(500) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id_mail`),
  KEY `id_user` (`id_user`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
