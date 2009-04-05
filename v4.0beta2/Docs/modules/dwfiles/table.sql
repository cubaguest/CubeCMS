-- ZMĚNIT PREFIX !!!!!!!!!!!!!!!!!

INSERT INTO `dev`.`vypecky_modules` (
`id_module` ,
`name` ,
`params` ,
`datadir` ,
`dbtable1` ,
`dbtable2` ,
`dbtable3`
)
VALUES (
NULL , 'dwfiles', NULL , 'dwfiles', 'dwfiles', NULL, NULL
);


--
-- Struktura tabulky `vrubl_web_dwfiles`
--

CREATE TABLE IF NOT EXISTS `vrubl_web_dwfiles` (
  `id_file` smallint(5) unsigned NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `label_cs` varchar(500) default NULL,
  `label_en` varchar(500) default NULL,
  `label_de` varchar(500) default NULL,
  `file` varchar(300) NOT NULL,
  PRIMARY KEY  (`id_file`),
  KEY `id_item` (`id_item`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
