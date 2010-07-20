ALTER TABLE  `{PREFIX}courses` ADD  `keywords` VARCHAR( 200 ) NULL DEFAULT NULL AFTER  `name` ,
ADD  `description` VARCHAR( 300 ) NULL DEFAULT NULL AFTER  `keywords`