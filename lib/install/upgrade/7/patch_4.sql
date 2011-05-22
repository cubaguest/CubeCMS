/* remove old admin cats */
SELECT * FROM `{PREFIX}categories` WHERE SUBSTRING(urlkey_cs,1,5) = 'admin';

/* This is BAD */
DELETE FROM `{PREFIX}rights` WHERE `id_category` = 1 OR `id_category` = 3 OR
`id_category` = 4 OR `id_category` = 5 OR `id_category` = 6 OR `id_category` = 7 OR
`id_category` = 8 OR `id_category` = 9 OR `id_category` = 10 OR `id_category` = 11 OR
`id_category` = 12 OR `id_category` = 13 OR `id_category` = 1 OR `id_category` = 1 OR
`id_category` = 1 OR `id_category` = 1 OR `id_category` = 1 OR `id_category` = 1;