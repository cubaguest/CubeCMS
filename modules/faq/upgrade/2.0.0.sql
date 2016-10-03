-- remove deleted records
ALTER TABLE `{PREFIX}faq` 
CHANGE COLUMN `faq_question` `faq_question_cs` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL,
CHANGE COLUMN `faq_answer` `faq_answer_cs` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL
;
