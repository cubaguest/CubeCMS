/* UPDATE_MAIN_SITE */
-- doplnění callback funkcé pro konfigurace
UPDATE `cubecms_global_config` SET `values`='cs;en;de;ru;sk;au;us;da;es;pl;lv;is;sl;et;lt;hu;sv;fr', `callback_func`='Install_Core::updateInstalledLangs' WHERE `key` = 'APP_LANGS';
UPDATE `cubecms_global_config` SET `values`='cs;en;de;ru;sk;au;us;da;es;pl;lv;is;sl;et;lt;hu;sv;fr' WHERE `key` = 'DEFAULT_APP_LANG';
/* END_UPDATE */
-- delší uživatelská jména
ALTER TABLE `{PREFIX}users` CHANGE `username` `username` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Uzivatelske jmeno';