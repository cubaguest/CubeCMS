ALTER TABLE `{PREFIX}articles`
DROP INDEX `urlkey_cs`
, ADD INDEX `urlkey_cs` (`urlkey_cs` ASC)
, DROP INDEX `urlkey_en`
, ADD INDEX `urlkey_en` (`urlkey_en` ASC)
, DROP INDEX `urlkey_de`
, ADD INDEX `urlkey_de` (`urlkey_de` ASC);
