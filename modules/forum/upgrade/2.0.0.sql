ALTER TABLE `{PREFIX}forum_posts` 
CHANGE COLUMN `id_post` `id_message` SMALLINT(6) NOT NULL AUTO_INCREMENT  , 
CHANGE COLUMN `post_email` `message_email` VARCHAR(50) NOT NULL  , 
CHANGE COLUMN `post_created_by` `message_created_by` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NOT NULL  , 
CHANGE COLUMN `post_created_by_moderator` `message_created_by_moderator` TINYINT(1) NOT NULL DEFAULT '0'  , 
CHANGE COLUMN `post_id_user` `message_id_user` SMALLINT(6) NULL DEFAULT NULL  , 
CHANGE COLUMN `post_www` `message_www` VARCHAR(200) NULL DEFAULT NULL  , 
CHANGE COLUMN `post_name` `message_name` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL  , 
CHANGE COLUMN `post_text` `message_text` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NOT NULL  , 
CHANGE COLUMN `post_text_clear` `message_text_clear` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NOT NULL  , 
CHANGE COLUMN `post_ip_address` `message_ip_address` VARCHAR(15) NOT NULL  , 
CHANGE COLUMN `post_date_add` `message_date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP  , 
CHANGE COLUMN `post_censored` `message_censored` TINYINT(1) NULL DEFAULT '0'  , 
ADD COLUMN `id_parent_message` INT NULL DEFAULT 0  AFTER `id_topic` ,
ADD COLUMN `message_order` INT NULL DEFAULT 0 , 
ADD COLUMN `message_depth` INT NULL DEFAULT 0 ,
ADD COLUMN `message_reaction_send_notify` TINYINT(1) NULL DEFAULT 0 , 
RENAME TO  `{PREFIX}forum_messages` ;


