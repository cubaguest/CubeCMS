ALTER TABLE `{PREFIX}actions`
CHANGE COLUMN `name_cs` `name_cs` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL  ,
CHANGE COLUMN `note_cs` `note_cs` VARCHAR(500) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL  ,
CHANGE COLUMN `name_en` `name_en` VARCHAR(200) NULL DEFAULT NULL  ,
CHANGE COLUMN `text_cs` `text_cs` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL;

SET @s = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE table_name = '{PREFIX}actions'
        AND table_schema = DATABASE()
        AND column_name = 'name_sk'
    ) = 0,
    "SELECT 1",
    "ALTER TABLE `{PREFIX}actions`
            CHANGE COLUMN `name_sk` `name_sk` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_slovak_ci' NULL DEFAULT NULL,
            CHANGE COLUMN `text_sk` `text_sk` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_slovak_ci' NULL DEFAULT NULL ;"
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
            CCHANGE COLUMN `name_de` `name_de` VARCHAR(200) NULL DEFAULT NULL;"
));

PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;