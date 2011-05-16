Postup vytvoření updatu
=======================

Možné soubory
=============

patch_{revize}.sql
patch_{revize}.php

postup updatu
=============
1. php file
2. sql file


SQL update
==========

Obsahhuje řídící struktury pro provedení v hlavním webu a podwebech. Umožňuje spuštění pouze některých příkazů. Vhodné pro gloablní tabulky, funkce, spouště.

blok:

Hlavní web:

--UPDATE_MAIN_SITE--
ALTER TABLE `{PREFIX}users` ADD COLUMN `main_site_update`;
--END_UPDATE--

Pod web:

--UPDATE_SUB_SITE--
ALTER TABLE `{PREFIX}users` ADD COLUMN `sub_site_update`;
--END_UPDATE--

