/* UPDATE_MAIN_SITE */
-- new config values
INSERT INTO `cubecms_global_config` (`key`, `label`, `value`, `protected`, `type`, `id_group`, `hidden_value`) 
VALUES
('ARTICLE_TITLE_IMG_C', 'Ořezávat titulní obrázky', "true", false, 'bool', 7, false),
('CACHE_TEXT_IMAGES', 'Zapnutí kešování obrázků v textu', "true", false, 'bool', 7, false),
('CACHE_TEXT_IMAGES_CROP', 'Ořezání kešovaného obrázku při zadání obou rozměrů', "false", false, 'bool', 7, false);
/* END_UPDATE */