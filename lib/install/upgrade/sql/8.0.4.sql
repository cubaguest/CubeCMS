/* UPDATE_MAIN_SITE */
/* END_UPDATE */
-- podpora adresy a telefonu u uživatele
ALTER TABLE `{PREFIX}users` 
ADD COLUMN `user_address` VARCHAR(500) NULL DEFAULT NULL,
ADD COLUMN `user_phone` VARCHAR(15) NULL DEFAULT NULL;
