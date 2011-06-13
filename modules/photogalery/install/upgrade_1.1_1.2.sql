UPDATE `{PREFIX}texts` AS t_t
LEFT JOIN `{PREFIX}categories` AS t_c ON `t_t`.`id_item` = `t_c`.`id_category`
SET `t_t`.`subkey` = 'main'
WHERE `t_c`.`module` = 'photogalery' AND ( `t_t`.`subkey` IS NULL OR `t_t`.`subkey` = 'nokey')