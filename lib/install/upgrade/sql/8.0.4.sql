/* UPDATE_MAIN_SITE */
/* END_UPDATE */
-- podpora adresy, telefonu a zobraziovaní osobních údajů u uživatele
ALTER TABLE `{PREFIX}users` 
ADD COLUMN `user_address` VARCHAR(500) NULL DEFAULT NULL,
ADD COLUMN `user_phone` VARCHAR(15) NULL DEFAULT NULL,
ADD COLUMN `user_info_is_private` TINYINT(1) NULL DEFAULT 0;
