ALTER TABLE `{PREFIX}hpslideshow_images` 
ADD COLUMN `valid_from` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN `valid_to` timestamp NULL DEFAULT NULL,
ADD COLUMN `slogan_background` tinyint(1) NOT NULL DEFAULT '0';