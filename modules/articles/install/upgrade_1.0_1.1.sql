ALTER TABLE  `vypecky_articles` ADD  `annotation_cs` VARCHAR( 1000 ) NULL DEFAULT NULL AFTER  `text_cs`;
ALTER TABLE  `vypecky_articles` ADD  `annotation_sk` VARCHAR( 1000 ) NULL DEFAULT NULL AFTER  `text_sk`;
ALTER TABLE  `vypecky_articles` ADD  `annotation_en` VARCHAR( 1000 ) NULL DEFAULT NULL AFTER  `text_en`;
ALTER TABLE  `vypecky_articles` ADD  `annotation_de` VARCHAR( 1000 ) NULL DEFAULT NULL AFTER  `text_de`;