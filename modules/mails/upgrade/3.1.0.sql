ALTER TABLE `{PREFIX}mails_send_queue` 
ADD COLUMN `id_user` SMALLINT NOT NULL AFTER `id_mail`, 
ADD COLUMN `mail_data` BLOB NULL  AFTER `undeliverable`, 
ADD INDEX `id_user` (`id_user` ASC);

ALTER TABLE `{PREFIX}mails_sends` 
CHANGE COLUMN `recipients` `recipients` TEXT NULL DEFAULT NULL  ;
