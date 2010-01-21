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
NULL , 'photogalery', NULL , 'photogalery', 'photogalery_galeries', 'photogalery_photos', NULL
);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_photogalery_galeries`
--

CREATE TABLE IF NOT EXISTS `vypecky_photogalery_galeries` (
  `id_galery` smallint(6) NOT NULL auto_increment,
  `id_item` smallint(6) NOT NULL,
  `label_cs` varchar(200) default NULL,
  `text_cs` varchar(1000) default NULL,
  `label_en` varchar(200) default NULL,
  `text_en` varchar(1000) default NULL,
  `label_de` varchar(200) default NULL,
  `text_de` varchar(1000) default NULL,
  `time_add` int(11) default NULL,
  `time_edit` int(11) default NULL,
  PRIMARY KEY  (`id_galery`),
  KEY `id_item` (`id_item`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_photogalery_photos`
--

CREATE TABLE IF NOT EXISTS `vypecky_photogalery_photos` (
  `id_photo` smallint(5) unsigned NOT NULL auto_increment,
  `id_galery` smallint(5) unsigned NOT NULL,
  `label_cs` varchar(200) default NULL,
  `label_en` varchar(200) default NULL,
  `label_de` varchar(500) default NULL,
  `file` varchar(200) NOT NULL,
  `time_add` int(11) default NULL,
  PRIMARY KEY  (`id_photo`),
  KEY `id_galery` (`id_galery`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;