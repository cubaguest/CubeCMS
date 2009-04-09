--Přejmenování tabulky pro stahované soubory
RENAME TABLE `dev`.`vypecky_userfiles`  TO `dev`.`vypecky_dwfiles` ;

ALTER TABLE `vypecky_dwfiles` CHANGE `id_category` `id_item` SMALLINT UNSIGNED NOT NULL  