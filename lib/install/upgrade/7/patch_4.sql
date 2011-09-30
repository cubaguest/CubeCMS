/* Update Cube CMS v 7.4 */

/* UPDATE_MAIN_SITE */
INSERT INTO `cubecms_global_config` (`key`, `label`, `value`, `protected`, `type`, `id_group`) VALUES
('ARTICLES_IN_LIST', 'Výchozí počet článků na jednu stránku', 5, false, 'number', 4);
/* END_UPDATE */


ALTER TABLE  `{PREFIX}sessions` ENGINE = MYISAM