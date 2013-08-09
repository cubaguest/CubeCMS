ALTER TABLE `{PREFIX}lecturers`
ADD FULLTEXT (`name`),
ADD FULLTEXT (`surname`),
ADD FULLTEXT (`text_clear`);
