/* UPDATE_MAIN_SITE */
INSERT INTO `cubecms_global_config` (`key`, `label`, `value`, `protected`, `type`, `id_group`, `hidden_value`) 
VALUES
('ANALYTICS_DISABLED_HOSTS', 'IP adresy pro které je analýza stránek vypnuta (odělené čárkou)', "127.0.0.1", false, 'string', 11, false),
('VVE_DEFAULT_LANG_SUBSTITUTION', 'Nahrazovat jazyk výchozím jazykem', "false", false, 'bool', 8, false);
/* END_UPDATE */
