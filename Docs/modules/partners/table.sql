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
NULL , 'partners', NULL , 'partners', 'partners', NULL, NULL
);

-- --------------------------------------------------------

--
-- Struktura tabulky `valmez_partners`
--

CREATE TABLE IF NOT EXISTS `valmez_partners` (
  `id_partner` smallint(5) unsigned NOT NULL auto_increment,
  `id_item` smallint(6) NOT NULL,
  `name` varchar(100) NOT NULL,
  `label_cs` varchar(1000) default NULL,
  `label_en` varchar(1000) default NULL,
  `label_de` varchar(1000) default NULL,
  `url` varchar(100) default NULL,
  `priority` smallint(6) NOT NULL default '0' COMMENT 'Priorita pořadí',
  `logo_file` varchar(100) default NULL,
  `logo_type` enum('flash','image') default NULL,
  `logo_width` smallint(6) default NULL,
  `logo_height` smallint(6) default NULL,
  PRIMARY KEY  (`id_partner`),
  KEY `id_item` (`id_item`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
