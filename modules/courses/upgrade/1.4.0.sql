ALTER TABLE  `{PREFIX}courses` ADD `show_in_list` BOOLEAN NOT NULL DEFAULT  '1',
ADD  `type` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_czech_ci NULL DEFAULT 'kurz' AFTER `id_course`;
