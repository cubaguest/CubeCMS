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
NULL , 'products', NULL , 'products', 'products', 'products_documents', 'products_photos'
);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_products`
--

CREATE TABLE IF NOT EXISTS `vypecky_products` (
  `id_product` smallint(5) unsigned NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `id_user` smallint(5) unsigned default '1',
  `add_time` int(11) NOT NULL,
  `edit_time` int(11) default NULL,
  `label_cs` varchar(400) default NULL,
  `text_cs` text,
  `label_en` varchar(400) default NULL,
  `text_en` text,
  `lebal_de` varchar(400) default NULL,
  `text_de` text,
  `main_image` varchar(200) default NULL,
  PRIMARY KEY  (`id_article`),
  KEY `id_item` (`id_item`,`id_user`),
  FULLTEXT KEY `label_cs` (`label_cs`),
  FULLTEXT KEY `text_cs` (`text_cs`),
  FULLTEXT KEY `label_en` (`label_en`),
  FULLTEXT KEY `text_en` (`text_en`),
  FULLTEXT KEY `lebal_de` (`lebal_de`),
  FULLTEXT KEY `text_de` (`text_de`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;