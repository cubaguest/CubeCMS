ALTER TABLE  `{PREFIX}mails_addressbook` ADD  `note` VARCHAR( 400 ) NULL DEFAULT NULL AFTER  `mail`;
ALTER TABLE  `{PREFIX}mails_addressbook` ADD  `id_group` smallint(6) NOT NULL DEFAULT '1' AFTER  `id_mail`;

CREATE TABLE IF NOT EXISTS `{PREFIX}mails_groups` (
  `id_group` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--
-- Vypisuji data pro tabulku `vypecky_mails_groups`
--

INSERT INTO `{PREFIX}mails_groups` (`id_group`, `name`) VALUES (1, 'Adresář');