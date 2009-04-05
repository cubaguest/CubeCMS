-- ZMĚNIT PREFIX !!!!!!!!!!!!!!!!!

INSERT INTO `dev`.`vypecky_modules` (
`id_module` ,
`name` ,
`mparams` ,
`datadir` ,
`dbtable1` ,
`dbtable2` ,
`dbtable3`
)
VALUES (
NULL , 'references', NULL , 'references', 'references', 'texts' NULL
);

-- --------------------------------------------------------

--
-- Struktura tabulky `vrubl_web_references`
--

CREATE TABLE IF NOT EXISTS `vypecky_references` (
  `id_reference` smallint(5) unsigned NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `name_cs` varchar(300) default NULL,
  `label_cs` text,
  `name_en` varchar(300) default NULL,
  `label_en` text,
  `name_de` varchar(300) default NULL,
  `label_de` text,
  `file` varchar(200) default NULL,
  `changed_time` int(11) default NULL,
  PRIMARY KEY  (`id_reference`),
  KEY `id_item` (`id_item`),
  FULLTEXT KEY `name_cs` (`name_cs`),
  FULLTEXT KEY `label_cs` (`label_cs`),
  FULLTEXT KEY `name_en` (`name_en`),
  FULLTEXT KEY `label_en` (`label_en`),
  FULLTEXT KEY `name_de` (`name_de`),
  FULLTEXT KEY `label_de` (`label_de`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
