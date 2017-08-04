INSERT INTO `cubecms_global_config` (`key`, `label`, `value`, `values`, `protected`, `type`, `id_group`, `callback_func`, `hidden_value`) VALUES
('NEWSLETTER_SEND_MAILS', 'Adresy pro odesílání Newsletteru oddělené středníkem', NULL, NULL, 0, 'string', 6, NULL, 0);

ALTER TABLE `{PREFIX}mails_newsletters` ADD `newsletter_sendmail` VARCHAR(50) NULL DEFAULT NULL AFTER `newsletter_subject`;