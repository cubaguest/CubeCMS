/* UPDATE_MAIN_SITE */
-- new config values
INSERT INTO `cubecms_global_config` (`key`, `label`, `value`, `protected`, `type`, `id_group`, `hidden_value`) 
VALUES
('FCB_ACCESS_TOKEN', 'Access token pro přístup k Facebooku', null, false, 'string', 11, true);
/* END_UPDATE */

-- new config values
INSERT INTO `{PREFIX}config` (`key`, `label`, `value`, `protected`, `type`, `id_group`, `hidden_value`) 
VALUES 
('FCB_ACCESS_TOKEN', 'Access token pro přístup k Facebooku', null, false, 'string', 11, true);
