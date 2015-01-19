-- sloupec s adresářem
ALTER TABLE `{PREFIX}articles` 
ADD COLUMN `article_datadir` VARCHAR(50) NULL DEFAULT NULL;

