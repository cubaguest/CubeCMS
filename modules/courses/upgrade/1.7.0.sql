ALTER TABLE  `{PREFIX}courses_registrations`
ADD COLUMN `org_dic` varchar(15) COLLATE utf8_czech_ci DEFAULT NULL AFTER `org_ico`;