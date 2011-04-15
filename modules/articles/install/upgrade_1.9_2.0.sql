-- public na koncept
ALTER TABLE  `{PREFIX}articles` CHANGE  `public`  `concept` TINYINT( 1 ) NOT NULL DEFAULT  '0';
UPDATE `{PREFIX}articles` SET concept = IF(concept=1,0,1)