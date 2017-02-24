ALTER TABLE `{PREFIX}hpslideshow_images` CHANGE `image_label_cs` `image_label_cs` TEXT CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT NULL;
ALTER TABLE `{PREFIX}hpslideshow_images` CHANGE `image_label_en` `image_label_en` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

SET @s = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE table_name = '{PREFIX}hpslideshow_images'
        AND table_schema = DATABASE()
        AND column_name = 'image_label_sk'
    ) = 0,
    "ALTER TABLE `{PREFIX}hpslideshow_images` CHANGE `image_label_sk` `image_label_sk` TEXT CHARACTER SET utf8 COLLATE utf8_slovak_ci NULL DEFAULT NULL;",
    "SELECT 1"
));
SET @s = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE table_name = '{PREFIX}hpslideshow_images'
        AND table_schema = DATABASE()
        AND column_name = 'image_label_de'
    ) = 0,
    "ALTER TABLE `{PREFIX}hpslideshow_images` CHANGE `image_label_de` `image_label_de` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;",
    "SELECT 1"
));

SET @s = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE table_name = '{PREFIX}hpslideshow_images'
        AND table_schema = DATABASE()
        AND column_name = 'image_label_ru'
    ) = 0,
    "ALTER TABLE `{PREFIX}hpslideshow_images` CHANGE `image_label_ru` `image_label_ru` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;",
    "SELECT 1"
));
SET @s = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE table_name = '{PREFIX}hpslideshow_images'
        AND table_schema = DATABASE()
        AND column_name = 'image_label_pl'
    ) = 0,
    "ALTER TABLE `{PREFIX}hpslideshow_images` CHANGE `image_label_pl` `image_label_pl` TEXT CHARACTER SET utf8 COLLATE utf8_polish_ci NULL DEFAULT NULL;",
    "SELECT 1"
));

SET @s = (SELECT IF(
    (SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE table_name = '{PREFIX}hpslideshow_images'
        AND table_schema = DATABASE()
        AND column_name = 'image_label_es'
    ) = 0,
    "ALTER TABLE `{PREFIX}hpslideshow_images` CHANGE `image_label_es` `image_label_es` TEXT CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL DEFAULT NULL;",
    "SELECT 1"
));