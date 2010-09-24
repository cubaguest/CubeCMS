INSERT INTO `{PREFIX}config` (`key`, `label`, `value`, `protected`, `type`) VALUES
('REVISION', 'verze revize', 1, 1, 'number');

/* new column background */
ALTER TABLE `{PREFIX}categories` ADD COLUMN `background` VARCHAR(100) NULL  AFTER `icon` ;

