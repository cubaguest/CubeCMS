ALTER TABLE `{PREFIX}projects` 
CHANGE `project_name` `project_name_cs` VARCHAR(300) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL, 
CHANGE `project_name_short` `project_name_short_cs` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL, 
CHANGE `project_text` `project_text_cs` TEXT CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL, 
CHANGE `project_text_clear` `project_text_clear_cs` TEXT CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL;