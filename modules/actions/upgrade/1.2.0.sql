ALTER TABLE `{PREFIX}actions` 
CHANGE COLUMN `name_cs` `action_name_cs` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NOT NULL , 
CHANGE COLUMN `subname_cs` `action_subname_cs` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL  , 
CHANGE COLUMN `text_cs` `action_text_cs` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NOT NULL  , 
CHANGE COLUMN `text_clear_cs` `action_text_clear_cs` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL  , 
CHANGE COLUMN `urlkey_cs` `action_urlkey_cs` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL  , 
CHANGE COLUMN `note_cs` `action_note_cs` VARCHAR(500) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL  , 
CHANGE COLUMN `name_en` `action_name_en` VARCHAR(200) NULL DEFAULT NULL  , 
CHANGE COLUMN `subname_en` `action_subname_en` VARCHAR(200) NULL DEFAULT NULL  , 
CHANGE COLUMN `text_en` `action_text_en` TEXT NULL DEFAULT NULL  , 
CHANGE COLUMN `text_clear_en` `action_text_clear_en` TEXT NULL DEFAULT NULL  , 
CHANGE COLUMN `urlkey_en` `action_urlkey_en` VARCHAR(200) NULL DEFAULT NULL  , 
CHANGE COLUMN `note_en` `action_note_en` VARCHAR(500) NULL DEFAULT NULL  , 
CHANGE COLUMN `author` `action_author` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL  , 
CHANGE COLUMN `start_date` `action_start_date` DATE NOT NULL  , 
CHANGE COLUMN `stop_date` `action_stop_date` DATE NULL DEFAULT NULL  , 
CHANGE COLUMN `image` `action_image` VARCHAR(200) NULL DEFAULT NULL  , 
CHANGE COLUMN `time` `action_time` TIME NULL DEFAULT NULL  , 
CHANGE COLUMN `price` `action_price` INT(11) NULL DEFAULT NULL  , 
CHANGE COLUMN `preprice` `action_preprice` SMALLINT(6) NULL DEFAULT NULL  , 
CHANGE COLUMN `place` `action_place` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL  , 
CHANGE COLUMN `public` `action_public` TINYINT(1) NOT NULL DEFAULT '1'  , 
CHANGE COLUMN `time_add` `action_time_add` DATETIME NOT NULL;

SET @s = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE table_name = '{PREFIX}actions'
        AND table_schema = DATABASE()
        AND column_name = 'action_changed'
    ) > 0,
    "SELECT 1",
    "ALTER TABLE `{PREFIX}actions`
            ADD COLUMN `action_changed` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;"
));

SET @s = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE table_name = '{PREFIX}actions'
        AND table_schema = DATABASE()
        AND column_name = 'name_sk'
    ) = 0,
    "SELECT 1",
    "ALTER TABLE `{PREFIX}actions`
            CHANGE COLUMN `name_sk` `action_name_sk` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_slovak_ci' NOT NULL  , 
            CHANGE COLUMN `subname_sk` `action_subname_sk` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_slovak_ci' NULL DEFAULT NULL  , 
            CHANGE COLUMN `text_sk` `action_text_sk` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_slovak_ci' NOT NULL  , 
            CHANGE COLUMN `text_clear_sk` `action_text_clear_sk` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_slovak_ci' NULL DEFAULT NULL  , 
            CHANGE COLUMN `urlkey_sk` `action_urlkey_sk` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_slovak_ci' NULL DEFAULT NULL  , 
            CHANGE COLUMN `note_sk` `action_note_sk` VARCHAR(500) CHARACTER SET 'utf8' COLLATE 'utf8_slovak_ci' NULL DEFAULT NULL;"
));

PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @s = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE table_name = '{PREFIX}actions'
        AND table_schema = DATABASE()
        AND column_name = 'name_de'
    ) = 0,
    "SELECT 1",
    "ALTER TABLE `{PREFIX}actions`
            CHANGE COLUMN `name_de` `action_name_de` VARCHAR(200) NULL DEFAULT NULL  , 
            CHANGE COLUMN `subname_de` `action_subname_de` VARCHAR(200) NULL DEFAULT NULL  , 
            CHANGE COLUMN `text_de` `action_text_de` TEXT NULL DEFAULT NULL  , 
            CHANGE COLUMN `text_clear_de` `action_text_clear_de` TEXT NULL DEFAULT NULL  , 
            CHANGE COLUMN `urlkey_de` `action_urlkey_de` VARCHAR(200) NULL DEFAULT NULL  , 
            CHANGE COLUMN `note_de` `action_note_de` VARCHAR(500) NULL DEFAULT NULL ;"
));

PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;