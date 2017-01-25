ALTER TABLE  `{PREFIX}articles` ADD  `annotation_cs` VARCHAR( 1000 ) NULL DEFAULT NULL AFTER  `text_cs`;
ALTER TABLE  `{PREFIX}articles` ADD  `annotation_en` VARCHAR( 1000 ) NULL DEFAULT NULL AFTER  `text_en`;

SET @s = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE table_name = '{PREFIX}articles'
        AND table_schema = DATABASE()
        AND column_name = 'text_sk'
    ) = 0,
    "SELECT 1",
    "ALTER TABLE  `{PREFIX}articles` 
      ADD  `annotation_sk` VARCHAR( 1000 ) NULL DEFAULT NULL AFTER  `text_sk`;"
));
SET @s = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE table_name = '{PREFIX}articles'
        AND table_schema = DATABASE()
        AND column_name = 'text_de'
    ) = 0,
    "SELECT 1",
    "ALTER TABLE  `{PREFIX}articles` 
      ADD  `annotation_de` VARCHAR( 1000 ) NULL DEFAULT NULL AFTER  `text_de`;"
));