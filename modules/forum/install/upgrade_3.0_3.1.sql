ALTER TABLE `{PREFIX}forum_messages`
ADD COLUMN `message_vote` INT NULL DEFAULT 0,
ADD COLUMN `message_spam_vote` INT NULL DEFAULT 0,
CHANGE COLUMN `message_created_by` `message_author` VARCHAR(50) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NOT NULL DEFAULT '';
