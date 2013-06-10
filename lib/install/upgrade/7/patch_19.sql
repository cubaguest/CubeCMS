-- Update na verzi 7.19

-- aktializace komentářů
ALTER TABLE `{PREFIX}comments` 
CHANGE COLUMN `nick` `comment_nick` VARCHAR(100) NOT NULL, 
CHANGE COLUMN `comment` `comment_comment` VARCHAR(500) NOT NULL, 
CHANGE COLUMN `public` `comment_public` TINYINT(1) NOT NULL DEFAULT '1', 
CHANGE COLUMN `censored` `comment_censored` TINYINT(1) NOT NULL DEFAULT '0',
CHANGE COLUMN `corder` `comment_corder` SMALLINT(6) NOT NULL DEFAULT '1', 
CHANGE COLUMN `level` `comment_level` SMALLINT(6) NOT NULL DEFAULT '0', 
CHANGE COLUMN `time_add` `comment_time_add` DATETIME NOT NULL, 
CHANGE COLUMN `ip_address` `comment_ip_address` VARCHAR(15) NULL DEFAULT NULL, 
ADD COLUMN `comment_confirmed` TINYINT(1) NULL DEFAULT NULL  AFTER `comment_ip_address`, 
ADD COLUMN `comment_mail` VARCHAR(60) NULL DEFAULT NULL  AFTER `comment_confirmed`,
ADD COLUMN `comment_admin_viewed` TINYINT(1) NULL DEFAULT 0  AFTER `comment_mail`,
ADD INDEX `id_article` (`id_article` ASC),
ADD INDEX `order` (`comment_corder` ASC);
