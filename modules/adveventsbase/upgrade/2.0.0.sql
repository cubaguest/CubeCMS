-- fulltext
ALTER TABLE `{PREFIX}svb_events` 
ADD COLUMN `event_text_clear` TEXT NULL DEFAULT NULL AFTER `event_text`,
ADD FULLTEXT INDEX `fulltext_name` (`event_name` ASC),
ADD FULLTEXT INDEX `fulltext_text` (`event_text_clear` ASC);

