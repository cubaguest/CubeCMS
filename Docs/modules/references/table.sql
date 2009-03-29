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

CREATE TABLE IF NOT EXISTS `vrubl_web_references` (
  `id_reference` smallint(5) unsigned NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `name_cs` varchar(300) default NULL,
  `label_cs` text,
  `name_en` varchar(300) default NULL,
  `label_en` text,
  `name_de` varchar(300) default NULL,
  `label_de` text,
  `file` varchar(200) default NULL,
  PRIMARY KEY  (`id_reference`),
  KEY `id_item` (`id_item`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
