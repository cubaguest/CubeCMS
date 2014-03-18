-- sloupec s indetifikátorem uživatele pro autentizační mechanismy
ALTER TABLE `{PREFIX}users` 
ADD COLUMN `external_auth_id` VARCHAR(200) NULL DEFAULT NULL,
ADD COLUMN `authenticator` VARCHAR(20) NULL DEFAULT 'internal';

