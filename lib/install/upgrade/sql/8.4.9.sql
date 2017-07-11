/* UPDATE_MAIN_SITE */
INSERT INTO `cubecms_global_config` ( `key` , `label` , `value` , `values` , `protected` , `type` , `id_group` , `callback_func` , `hidden_value` )
VALUES 
('DEFAULT_MODULE', 'Výchozí modul kateogrie', 'text' , NULL , '0', 'string', '3', NULL , '0'),
('SHOW_CATEGORY_AFTER_CREATE', 'Přejít do kateogrie po jejím uložení', 0 , NULL , '0', 'bool', '3', NULL , '0')
;

INSERT INTO `cubecms_global_config` (`key`, `label`, `value`, `values`, `protected`, `type`, `id_group`, `callback_func`, `hidden_value`) VALUES
('INSTAGRAM_PAGE_URL', 'Adresa stránky/skupiny na Instagramu', NULL, NULL, 0, 'string', 11, NULL, 0);

/* END_UPDATE */
