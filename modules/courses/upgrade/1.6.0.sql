ALTER TABLE `{PREFIX}courses`
ADD FULLTEXT(`text_clear`),
ADD FULLTEXT(`name`),
CHANGE `time_start` `time_start` TIME NULL DEFAULT NULL;