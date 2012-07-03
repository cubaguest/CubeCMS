
-- OPTIMALIZACE

ALTER TABLE `{PREFIX}categories` 
-- sloupce
ADD COLUMN `disable_cs` TINYINT(1) NOT NULL DEFAULT 0  AFTER `urlkey_cs`,
ADD COLUMN `disable_en` TINYINT(1) NOT NULL DEFAULT 0  AFTER `urlkey_en`,
ADD COLUMN `disable_de` TINYINT(1) NOT NULL DEFAULT 0  AFTER `urlkey_de`,
ADD COLUMN `disable_sk` TINYINT(1) NOT NULL DEFAULT 0  AFTER `urlkey_sk`,
 
 
-- indexy 
ADD INDEX `urlkey_disable_cs` (`disable_cs` ASC),
ADD INDEX `urlkey_disable_en` (`disable_en` ASC),
ADD INDEX `urlkey_disable_de` (`disable_de` ASC),
ADD INDEX `urlkey_disable_sk` (`disable_sk` ASC),

ADD INDEX `individual_panel` (`individual_panels` ASC) ;



ALTER TABLE `{PREFIX}rights` 
DROP INDEX `id_category` 
, ADD INDEX `id_cat_grp` (`id_category` ASC, `id_group` ASC) 
, ADD INDEX `id_cat` (`id_category` ASC) ;
