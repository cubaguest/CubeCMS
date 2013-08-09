
-- forms module
INSERT INTO `{PREFIX}modules_instaled` (`name`, `version_major`, `version_minor`) VALUES ('forms', 1, 0);

CREATE TABLE IF NOT EXISTS `{PREFIX}forms` (
  `id_form` int(11) NOT NULL AUTO_INCREMENT,
  `form_name` varchar(200) DEFAULT NULL,
  `form_message` varchar(1000) DEFAULT NULL,
  `form_send_to_mails` varchar(500) DEFAULT NULL,
  `form_send_to_users` varchar(100) DEFAULT NULL,
  `form_sended` int(11) DEFAULT '0',
  `form_time_add` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `form_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_form`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `{PREFIX}forms_elements` (
  `id_form_element` int(11) NOT NULL AUTO_INCREMENT,
  `id_form` int(11) NOT NULL,
  `form_element_name` varchar(50) NOT NULL,
  `form_element_label` varchar(50) NOT NULL,
  `form_element_type` varchar(20) NOT NULL DEFAULT 'text',
  `form_element_value` varchar(200) DEFAULT NULL,
  `form_element_required` tinyint(1) DEFAULT '0',
  `form_element_options` varchar(1000) DEFAULT NULL,
  `form_element_order` smallint(6) DEFAULT '1',
  `form_element_validator` varchar(50) DEFAULT NULL,
  `form_element_ismultiple` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_form_element`),
  KEY `order` (`id_form`,`form_element_order`),
  KEY `id_form` (`id_form`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE = utf8_general_ci;
