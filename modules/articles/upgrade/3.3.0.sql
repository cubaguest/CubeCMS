-- sloupec s fotogalerií - id článku
ALTER TABLE `{PREFIX}articles` 
ADD COLUMN `id_photogallery` INT NOT NULL DEFAULT 0 AFTER `id_user`;

