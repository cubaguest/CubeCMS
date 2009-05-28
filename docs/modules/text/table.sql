-- ZMĚNIT PREFIX !!!!!!!!!!!!!!!!!

INSERT INTO `dev`.`PREFIX_modules` (
`id_module` ,
`name` ,
`mparams` ,
`datadir` ,
`dbtable1` ,
`dbtable2` ,
`dbtable3`
)
VALUES (
NULL , 'text', NULL , NULL, 'texts', NULL, NULL
);

-- --------------------------------------------------------

--
-- Struktura tabulky `PREFIX_texts`
--

CREATE TABLE IF NOT EXISTS `PREFIX_texts` (
  `id_text` smallint(4) unsigned NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `text_cs` mediumtext,
  `text_en` mediumtext,
  `text_de` mediumtext,
  `changed_time` int(11) default NULL,
  PRIMARY KEY  (`id_text`),
  UNIQUE KEY `id_article` (`id_item`),
  FULLTEXT KEY `text_cs` (`text_cs`),
  FULLTEXT KEY `text_en` (`text_en`),
  FULLTEXT KEY `text_de` (`text_de`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
