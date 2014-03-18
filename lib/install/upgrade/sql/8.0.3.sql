-- doplnění callback funkcé pro konfigurace
UPDATE `cubecms_global_config` SET `values`='cs;en;de;ru;sk;au;us;da;es;pl;lv;is;sl;et;lt;hu;sv', `callback_func`='Install_Core::updateInstalledLangs' WHERE `key` = 'APP_LANGS';
UPDATE `cubecms_global_config` SET `values`='cs;en;de;ru;sk;au;us;da;es;pl;lv;is;sl;et;lt;hu;sv' WHERE `key` = 'DEFAULT_APP_LANG';

