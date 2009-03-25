-- phpMyAdmin SQL Dump
-- version 3.1.2
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Středa 25. března 2009, 14:16
-- Verze MySQL: 5.0.70
-- Verze PHP: 5.2.8-pl2-gentoo

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databáze: `dev`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_categories`
--

CREATE TABLE IF NOT EXISTS `vypecky_categories` (
  `id_category` smallint(3) NOT NULL auto_increment,
  `id_section` smallint(3) NOT NULL,
  `urlkey` varchar(50) NOT NULL,
  `label_cs` varchar(50) default NULL,
  `alt_cs` varchar(200) default NULL,
  `label_en` varchar(50) default NULL,
  `alt_en` varchar(200) default NULL,
  `label_de` varchar(50) default NULL,
  `alt_de` varchar(200) default NULL,
  `cparams` varchar(200) default NULL,
  `protected` tinyint(1) NOT NULL default '0',
  `priority` smallint(2) NOT NULL default '0',
  `active` tinyint(1) NOT NULL default '1' COMMENT 'je-li kategorie aktivní',
  `left_panel` tinyint(1) NOT NULL default '1' COMMENT 'Je li zobrazen levý panel',
  `right_panel` tinyint(1) NOT NULL default '1' COMMENT 'Ja li zobrazen pravý panel',
  `sitemap_changefreq` enum('always','hourly','daily','weekly','monthly','yearly','never') NOT NULL default 'yearly',
  `sitemap_priority` float NOT NULL default '0.1',
  `show_in_menu` tinyint(1) NOT NULL default '1' COMMENT 'Má li se položka zobrazit v menu',
  `show_when_login_only` tinyint(1) NOT NULL default '0' COMMENT 'Jstli má bát položka zobrazena po přihlášení',
  PRIMARY KEY  (`id_category`),
  KEY `key` (`urlkey`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Vypisuji data pro tabulku `vypecky_categories`
--

INSERT INTO `vypecky_categories` (`id_category`, `id_section`, `urlkey`, `label_cs`, `alt_cs`, `label_en`, `alt_en`, `label_de`, `alt_de`, `cparams`, `protected`, `priority`, `active`, `left_panel`, `right_panel`, `sitemap_changefreq`, `sitemap_priority`, `show_in_menu`, `show_when_login_only`) VALUES
(2, 1, '', 'text pouze s obrázky', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 1, 1, 'monthly', 0.8, 1, 0),
(1, 1, '', 'text s obrázky a soubory', NULL, NULL, NULL, NULL, NULL, NULL, 0, 10, 1, 1, 1, 'monthly', 0.9, 1, 0),
(3, 2, '', 'Novinky', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 1, 1, 'weekly', 0.7, 1, 0),
(4, 5, '', 'Login', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 1, 1, 'never', 0.1, 1, 0),
(5, 1, '', 'text s obrázky a soubory - FULL', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 1, 1, 'hourly', 0.5, 1, 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_groups`
--

CREATE TABLE IF NOT EXISTS `vypecky_groups` (
  `id_group` smallint(3) unsigned NOT NULL auto_increment COMMENT 'ID skupiny',
  `name` varchar(15) default NULL COMMENT 'Nazev skupiny',
  `label` varchar(20) default NULL,
  `used` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Vypisuji data pro tabulku `vypecky_groups`
--

INSERT INTO `vypecky_groups` (`id_group`, `name`, `label`, `used`) VALUES
(1, 'admin', 'Administrátor', 1),
(2, 'guest', 'Host', 1),
(3, 'user', 'Uživatel', 1),
(4, 'poweruser', 'uživatel s většími p', 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_items`
--

CREATE TABLE IF NOT EXISTS `vypecky_items` (
  `id_item` smallint(6) NOT NULL auto_increment,
  `label_cs` varchar(100) default NULL,
  `alt_cs` varchar(500) default NULL,
  `label_en` varchar(100) default NULL,
  `alt_en` varchar(500) default NULL,
  `label_de` varchar(100) default NULL,
  `alt_de` varchar(500) default NULL,
  `group_admin` enum('r--','rw-','rwc','r-c','-wc','--c','-w-','---') default 'rwc',
  `group_user` enum('r--','rw-','rwc','r-c','-wc','--c','-w-','---') default 'rw-',
  `group_guest` enum('r--','rw-','rwc','r-c','-wc','--c','-w-','---') default 'r--',
  `group_poweruser` enum('r--','rw-','rwc','r-c','-wc','--c','-w-','---') default 'rwc',
  `params` varchar(500) default NULL COMMENT 'parametry pro daný modul itemu - jsouv popsány v docs',
  `priority` smallint(6) NOT NULL default '0',
  `id_category` smallint(6) NOT NULL,
  `id_module` smallint(5) unsigned NOT NULL,
  PRIMARY KEY  (`id_item`),
  KEY `id_category` (`id_category`),
  KEY `id_module` (`id_module`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Vypisuji data pro tabulku `vypecky_items`
--

INSERT INTO `vypecky_items` (`id_item`, `label_cs`, `alt_cs`, `label_en`, `alt_en`, `label_de`, `alt_de`, `group_admin`, `group_user`, `group_guest`, `group_poweruser`, `params`, `priority`, `id_category`, `id_module`) VALUES
(1, 'text s obrázky a soubory', NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', 'files=true;images=true;theme=advanced', 0, 1, 1),
(2, 'text pouze s obrázky', NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', 'files=true;images=false;theme=simple', 0, 2, 1),
(3, 'Novinky', NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', 'scroll=10;scrollpanel=5', 0, 3, 2),
(4, 'Login', NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', NULL, 0, 4, 4),
(5, 'text s obrázky a soubory - FULL', NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', NULL, 0, 5, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_modules`
--

CREATE TABLE IF NOT EXISTS `vypecky_modules` (
  `id_module` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  `mparams` varchar(100) default NULL,
  `datadir` varchar(100) default NULL,
  `dbtable1` varchar(50) default NULL,
  `dbtable2` varchar(50) default NULL,
  `dbtable3` varchar(50) default NULL,
  PRIMARY KEY  (`id_module`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

--
-- Vypisuji data pro tabulku `vypecky_modules`
--

INSERT INTO `vypecky_modules` (`id_module`, `name`, `mparams`, `datadir`, `dbtable1`, `dbtable2`, `dbtable3`) VALUES
(1, 'text', NULL, NULL, 'texts', NULL, NULL),
(2, 'news', NULL, NULL, 'news', NULL, NULL),
(3, 'dwfiles', NULL, 'dwfiles', 'dwfiles', NULL, NULL),
(4, 'login', NULL, NULL, 'users', NULL, NULL),
(5, 'minigalery', NULL, 'minigalery', 'minigalery', NULL, NULL),
(6, 'workers', NULL, 'workers', 'workers', NULL, NULL),
(7, 'sendmail', NULL, 'sendmail', 'sendmails', NULL, NULL),
(8, 'photogalerymax', 'photosingalerylist=3', 'photogalery', 'photos', 'photo_galeries', 'photo_sections'),
(9, 'guestbook', NULL, '', 'guestbook', NULL, NULL),
(10, 'iframe', NULL, NULL, 'iframe_targets', NULL, NULL),
(11, 'blog', NULL, NULL, 'blogs', 'blogs_sections', NULL),
(12, 'flashpage', '', 'flashpages', NULL, NULL, NULL),
(13, 'comics', NULL, 'comics', 'comics', NULL, NULL),
(14, 'links', NULL, 'links', 'link_sections', NULL, NULL),
(15, 'errors', NULL, NULL, 'errors', NULL, NULL),
(16, 'partners', NULL, 'partners', 'partners', NULL, NULL),
(17, 'users', NULL, 'users', 'users', 'groups', NULL),
(18, 'photogalery', NULL, 'photogalery', 'photogalery_galeries', 'photogalery_photos', NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_news`
--

CREATE TABLE IF NOT EXISTS `vypecky_news` (
  `id_new` smallint(6) NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `id_user` smallint(6) NOT NULL,
  `label_cs` varchar(50) NOT NULL,
  `text_cs` varchar(500) NOT NULL,
  `label_en` varchar(50) default NULL,
  `text_en` varchar(500) default NULL,
  `label_de` varchar(50) default NULL,
  `text_de` varchar(500) default NULL,
  `time` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id_new`),
  KEY `id_user` (`id_user`),
  FULLTEXT KEY `label_cs` (`label_cs`),
  FULLTEXT KEY `text_cs` (`text_cs`),
  FULLTEXT KEY `label_en` (`label_en`),
  FULLTEXT KEY `text_en` (`text_en`),
  FULLTEXT KEY `label_de` (`label_de`),
  FULLTEXT KEY `text_de` (`text_de`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=34 ;

--
-- Vypisuji data pro tabulku `vypecky_news`
--

INSERT INTO `vypecky_news` (`id_new`, `id_item`, `id_user`, `label_cs`, `text_cs`, `label_en`, `text_en`, `label_de`, `text_de`, `time`, `deleted`) VALUES
(7, 3, 3, 'Novinky (Jitrničky) na Výpečkách', '<p>Tak první novinka na <a href="http://www.vypecky.info">Výpečkách</a> je\r\nvlasně zavedení <strong>novinek</strong>, kde můžete psát krátké novinky.\r\nTak hodně zdaru! :-D (těch novinek je tu až moc :-D )</p>\r\n\r\n<!-- by Texy2! -->', NULL, NULL, NULL, NULL, 1210695435, 0),
(8, 3, 10, 'Pjekne', '<p>Holahola… Zahradní restaurace u Valentů v Hrachovci zahajuje zítra\r\nv 17:00 svůj letní provoz slavnostním naražením sudu Radegast 10°.\r\nNásledovat budou sudy Zubr 11° (17:05), Radegast 12° (17:07) a Kofola (17:10\r\nesli bude). Po slavnostním naražení se koná slavnostní vypití. Bronik\r\nletos připravil 10 vylepšení oproti loňsku a kdo je všechny objeví,\r\ndostane pivečko zadarmo:-)</p>\r\n', NULL, NULL, NULL, NULL, 1210887011, 0),
(9, 3, 4, 'Karamazovi', '\n<p>Žijte kulturně a zajděte si na nový Zelenkův film Karamazovi. Po dlouhé\ndobě zase jeden dobrý český film. Zelenka je holt pašák :-)</p>\n\n<!-- by Texy2! -->', NULL, NULL, NULL, NULL, 1211016870, 0),
(10, 3, 3, 'Upravený layout a je ještě LEPŠÍ!', 'Konečně jsem si našel trochu času a upravil layout výpeček. Teď by se\r\nměl korektně zobrazovat v FF a Opeře, jenom v IE zůstává pár chybiček.\r\nCelý mám (částečně) měnitelnou šířku takže potěším lidi co\r\nnepoužívají velké rozlišení.\r\n', 'Better layout', NULL, NULL, NULL, 1211050696, 0),
(11, 3, 4, 'Indiana Jones', '<p>S výpečky do kina. Zítra jdeme s Míšou a Synkem na nového Jonese, tak\r\nneváhejte a připojte se – the more the merrier :-)</p>\r\n\r\n<!-- by Texy2! -->', NULL, NULL, NULL, NULL, 1211467174, 0),
(12, 3, 4, 'Indiana Jones2', '<p>Tak na jonese se nakonec jde až v neděli. Včera byla noc kejklířů,\r\ndneska je ta muzejní. Obě dvě akce mají samozřejmě vyšší prioritu.\r\nDnes na pryglu začnou soutěžní ohňostroje, nenechte si ujit – see ya there.</p>\r\n\r\n<!-- by Texy2! -->', NULL, NULL, NULL, NULL, 1211620130, 0),
(13, 3, 4, 'Indiana Jones potřetí', '\n<p>Tak na Jonese se nakonec šlo až v pondělí. Kdo byl v něděli, tomu se\nonlouvám. Jinak to samozřejmě stálo za to. Smáli jsem se, báli i plakali.\nV závěru se nám sice Jones oženil, ale i to se dalo přežít. Tak kdo\nnebyl, šup šup do kina.</p>\n\n<!-- by Texy2! -->', NULL, NULL, NULL, NULL, 1211980229, 0),
(14, 3, 4, 'garden party', '<p>pro všechny, kdo jsou ve Valmezu: V Hrachovci na zahrádce u Broni se\r\nkoná party na oslavu narozenin jeho zeny (me sestrenky) Svatavy. Kdo pujdete kolem,\r\nnezapomente se stavit – ma tam byt i sele, ci co.</p>\r\n\r\n<!-- by Texy2! -->', NULL, NULL, NULL, NULL, 1212250241, 0),
(16, 3, 10, 'Sittin&#039; at a bar', '<p>Kdo by neměl chuť se tu a tam ožrat do němoty…? Zde je takový malý\r\ntrenažer pro výpitky jak dosáhnout dobré opice a nenadělat příliš\r\nostudy… Vysoce návykový shledávám především song… <a\r\nhref="http://www.tinymania.com/play/sittinatabar/">Odkaz</a></p>\r\n\r\n<!-- by Texy2! -->', NULL, NULL, NULL, NULL, 1212482056, 0),
(33, 3, 3, 'Další novinka', 'ggfd gdfg fdg df', NULL, NULL, NULL, NULL, 1237116725, 1),
(19, 3, 3, 'Novinka v češtině s popisem', 'česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky ', 'English news with label', 'english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english ', NULL, NULL, 1218921753, 0),
(20, 3, 3, 'label', '&lt;b&gt;text&lt;/b&gt;', NULL, NULL, NULL, NULL, 1218975043, 1),
(21, 3, 3, 'cs label nový pěkný', 'fdsafsdafasfd', NULL, NULL, NULL, NULL, 1218979456, 1),
(22, 3, 3, 'Popis novinky', 'pokusný text', 'dsadasda', 'English news', NULL, NULL, 1222705858, 0),
(23, 3, 3, 'Zcela nová novinka a uprava', 'Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky ', 'Realy new News', 'News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text ', NULL, NULL, 1222705915, 0),
(24, 3, 3, 'Nová novinka', '&lt;b&gt;tučně&lt;/b&gt;', NULL, NULL, NULL, NULL, 1222705983, 1),
(25, 3, 3, 'pes', 'tak tohle je novinka o psu', NULL, NULL, NULL, NULL, 1222706254, 1),
(26, 3, 3, '&lt;input /&gt;jakubmatas@gmail.com', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus eu ligula. Maecenas tristique, turpis ac interdum feugiat, ligula nibh mattis erat, eu mattis felis lacus vel leo. Morbi vehicula sapien vitae lectus. Aliquam sit amet ipsum. Quisque sit amet neque. Sed ornare orci eget orci. Aenean scelerisque. Vivamus mauris magna, adipiscing eget, imperdiet eget, placerat non, quam. Praesent eget enim vitae pede rutrum auctor. Maecenas dictum purus. Nunc convallis, nulla id consectetur lacinia', NULL, NULL, NULL, NULL, 1232204814, 1),
(27, 3, 3, '&lt;input /&gt;jakubmatas@gmail.com', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus eu ligula. Maecenas tristique, turpis ac interdum feugiat, ligula nibh mattis erat, eu mattis felis lacus vel leo. Morbi vehicula sapien vitae lectus. Aliquam sit amet ipsum. Quisque sit amet neque. Sed ornare orci eget orci. Aenean scelerisque. Vivamus mauris magna, adipiscing eget, imperdiet eget, placerat non, quam. Praesent eget enim vitae pede rutrum auctor. Maecenas dictum purus. Nunc convallis, nulla id consectetur lacinia', NULL, NULL, NULL, NULL, 1232204880, 1),
(28, 3, 3, '&lt;input /&gt;jakubmatas@gmail.com', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus eu ligula. Maecenas tristique, turpis ac interdum feugiat, ligula nibh mattis erat, eu mattis felis lacus vel leo. Morbi vehicula sapien vitae lectus. Aliquam sit amet ipsum. Quisque sit amet neque. Sed ornare orci eget orci. Aenean scelerisque. Vivamus mauris magna, adipiscing eget, imperdiet eget, placerat non, quam. Praesent eget enim vitae pede rutrum auctor. Maecenas dictum purus. Nunc convallis, nulla id consectetur lacinia', NULL, NULL, NULL, NULL, 1232205108, 1),
(29, 3, 3, 'jakubmatas_gmail_com', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus eu ligula. Maecenas tristique, turpis ac interdum feugiat, ligula nibh mattis erat, eu mattis felis lacus vel leo. Morbi vehicula sapien vitae lectus. Aliquam sit amet ipsum. Quisque sit amet neque. Sed ornare orci eget orci. Aenean scelerisque. Vivamus mauris magna, adipiscing eget, imperdiet eget, placerat non, quam. Praesent eget enim vitae pede rutrum auctor. Maecenas dictum purus. Nunc convallis, nulla id consectetur lacinia', NULL, NULL, NULL, NULL, 1232205120, 0),
(30, 3, 3, 'Upravený popis novinky', 'Lorem Ipsum je demonstrativní výplňový text používaný v tiskařském a knihařském průmyslu. Lorem Ipsum je považováno za standard v této oblasti už od začátku 16. století, kdy dnes neznámý tiskař vzal kusy textu a na jejich základě vytvořil speciální vzorovou knihu. Jeho odkaz nevydržel pouze pět století, on přežil i nástup elektronické sazby v podstatě beze změny. Nejvíce popularizováno bylo Lorem Ipsum v šedesátých letech 20. století, kdy byly vydávány speciální vzorníky s jeho pasážemi a pozděj', NULL, NULL, NULL, NULL, 1232206214, 0),
(31, 3, 3, 'Nový popis novinky', 'Lorem Ipsum je demonstrativní výplňový text používaný v tiskařském a knihařském průmyslu. Lorem Ipsum je považováno za standard v této oblasti už od začátku 16. století, kdy dnes neznámý tiskař vzal kusy textu a na jejich základě vytvořil speciální vzorovou knihu. Jeho odkaz nevydržel pouze pět století, on přežil i nástup elektronické sazby v podstatě beze změny. Nejvíce popularizováno bylo Lorem Ipsum v šedesátých letech 20. století, kdy byly vydávány speciální vzorníky s jeho pasážemi a pozděj', NULL, NULL, NULL, NULL, 1232220144, 0),
(32, 3, 3, 'Nový popis novinky', 'Lorem Ipsum je demonstrativní výplňový text používaný v tiskařském a knihařském průmyslu. Lorem Ipsum je považováno za standard v této oblasti už od začátku 16. století, kdy dnes neznámý tiskař vzal kusy textu a na jejich základě vytvořil speciální vzorovou knihu. Jeho odkaz nevydržel pouze pět století, on přežil i nástup elektronické sazby v podstatě beze změny. Nejvíce popularizováno bylo Lorem Ipsum v šedesátých letech 20. století, kdy byly vydávány speciální vzorníky s jeho pasážemi a pozděj', NULL, NULL, NULL, NULL, 1232220169, 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_panels`
--

CREATE TABLE IF NOT EXISTS `vypecky_panels` (
  `id_panel` smallint(3) NOT NULL auto_increment,
  `priority` smallint(2) NOT NULL default '0',
  `label` varchar(30) NOT NULL,
  `id_item` smallint(5) unsigned default NULL,
  `position` enum('left','right') NOT NULL default 'left',
  `enable` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id_panel`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Vypisuji data pro tabulku `vypecky_panels`
--

INSERT INTO `vypecky_panels` (`id_panel`, `priority`, `label`, `id_item`, `position`, `enable`) VALUES
(1, 0, 'Nejnovější tlačenka', 1, 'right', 0),
(2, 4, 'Aktuální komiks', 6, 'right', 0),
(3, 5, 'Fotogalerie', 4, 'right', 0),
(4, 10, 'Výpečky', 2, 'left', 0),
(5, 5, 'Účet', 17, 'left', 0),
(6, 0, 'Jitrničky', 5, 'left', 0),
(7, 0, 'Sponzoři', 18, 'right', 0);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=29 ;

--
-- Vypisuji data pro tabulku `vypecky_photogalery_galeries`
--

INSERT INTO `vypecky_photogalery_galeries` (`id_galery`, `id_item`, `label_cs`, `text_cs`, `label_en`, `text_en`, `label_de`, `text_de`, `time_add`, `time_edit`) VALUES
(15, 4, 'nová galerie 3', '      $galeryM = new GaleryDetailModel();      $galeryM = new GaleryDetailModel();      $galeryM = new GaleryDetailModel();      $galeryM = new GaleryDetailModel();      $galeryM = new GaleryDetailModel();      $galeryM = new GaleryDetailModel();      $galeryM = new GaleryDetailModel();      $galeryM = new GaleryDetailModel();      $galeryM = new GaleryDetailModel();      $galeryM = new GaleryDetailModel();      $galeryM = new GaleryDetailModel();', NULL, NULL, NULL, NULL, 1096668000, 1234282478),
(14, 4, 'nová galerie upravena 2', 'testetse t', 'label en', NULL, NULL, NULL, 1128204000, 1234280877),
(16, 4, 'pokus', NULL, NULL, NULL, NULL, NULL, 1234282510, NULL),
(17, 4, 'hgk hjkghj hj', NULL, NULL, NULL, NULL, NULL, 1254434400, NULL),
(18, 4, 'nová galerie upravena čassssss', NULL, NULL, NULL, NULL, NULL, 1254434400, NULL),
(24, 4, 'nová galerie 2', NULL, NULL, NULL, NULL, NULL, 1241215200, NULL),
(20, 4, 'nová galerie upravena čassssss', NULL, NULL, NULL, NULL, NULL, 1001973600, NULL),
(23, 4, 'nová galerie upravena 2', NULL, NULL, NULL, NULL, NULL, 1254434400, NULL),
(22, 4, 'posledni', NULL, NULL, NULL, NULL, NULL, 915145200, NULL),
(25, 4, 'posledni', NULL, NULL, NULL, NULL, NULL, 1233442800, 1234287009);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_photogalery_photos`
--

CREATE TABLE IF NOT EXISTS `vypecky_photogalery_photos` (
  `id_photo` smallint(5) unsigned NOT NULL auto_increment,
  `id_galery` smallint(5) unsigned NOT NULL,
  `label_cs` varchar(500) default NULL,
  `label_en` varchar(500) default NULL,
  `label_de` varchar(500) default NULL,
  `file` varchar(200) NOT NULL,
  `time_add` int(11) default NULL,
  PRIMARY KEY  (`id_photo`),
  KEY `id_galery` (`id_galery`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=59 ;

--
-- Vypisuji data pro tabulku `vypecky_photogalery_photos`
--

INSERT INTO `vypecky_photogalery_photos` (`id_photo`, `id_galery`, `label_cs`, `label_en`, `label_de`, `file`, `time_add`) VALUES
(30, 15, NULL, NULL, NULL, 'obraz3.jpg', 1234282464),
(17, 14, NULL, NULL, NULL, 'obraz-hradu-muran182611.jpg', 1234139965),
(18, 14, NULL, NULL, NULL, 'obraz.jpg', 1234139965),
(19, 14, NULL, NULL, NULL, 'obraz013.jpg', 1234139965),
(20, 14, NULL, NULL, NULL, 'obraz-021.jpg', 1234139965),
(21, 14, NULL, NULL, NULL, 'obraz-uplnek-mysli2.jpeg', 1234139966),
(22, 14, NULL, NULL, NULL, 'obraz1.jpg', 1234205009),
(23, 14, NULL, NULL, NULL, 'obraz014.jpg', 1234205009),
(24, 14, NULL, NULL, NULL, 'obraz-uplnek-mysli3.jpeg', 1234205029),
(25, 14, NULL, NULL, NULL, 'obraz015.jpg', 1234205030),
(26, 14, NULL, NULL, NULL, 'obraz2.jpg', 1234205030),
(27, 14, NULL, NULL, NULL, 'obraz-uplnek-mysli4.jpeg', 1234205033),
(28, 14, NULL, NULL, NULL, 'obraz016.jpg', 1234205033),
(31, 15, NULL, NULL, NULL, 'obraz011.jpg', 1234282464),
(32, 15, NULL, NULL, NULL, 'obraz-uplnek-mysli5.jpeg', 1234282465),
(33, 16, NULL, NULL, NULL, 'obraz-uplnek-mysli6.jpeg', 1234282510),
(34, 17, NULL, NULL, NULL, 'obraz-uplnek-mysli7.jpeg', 1234282579),
(35, 18, NULL, NULL, NULL, 'obraz-022.jpg', 1234282675),
(37, 20, NULL, NULL, NULL, 'obraz-024.jpg', 1234282695),
(40, 23, NULL, NULL, NULL, 'obraz-uplnek-mysli8.jpeg', 1234282785),
(39, 22, NULL, NULL, NULL, 'obraz4.jpg', 1234282748),
(41, 24, NULL, NULL, NULL, 'obraz-023.jpg', 1234282799),
(42, 25, NULL, NULL, NULL, 'obraz017.jpg', 1234282814),
(43, 26, NULL, NULL, NULL, 'obraz5.jpg', 1234286125),
(44, 26, NULL, NULL, NULL, 'obraz6.jpg', 1234286125),
(45, 26, NULL, NULL, NULL, 'obraz7.jpg', 1234286126),
(46, 27, NULL, NULL, NULL, 'obraz018.jpg', 1234286189),
(47, 27, NULL, NULL, NULL, 'obraz8.jpg', 1234286189),
(48, 27, NULL, NULL, NULL, 'obraz9.jpg', 1234286190),
(49, 27, NULL, NULL, NULL, 'obraz10.jpg', 1234286190),
(50, 28, NULL, NULL, NULL, 'obraz-uplnek-mysli9.jpeg', 1234286239),
(51, 28, NULL, NULL, NULL, 'obraz-uplnek-mysli10.jpeg', 1234286239),
(52, 28, NULL, NULL, NULL, 'obraz-uplnek-mysli11.jpeg', 1234286239),
(53, 28, NULL, NULL, NULL, 'obraz-uplnek-mysli12.jpeg', 1234286240),
(54, 16, NULL, NULL, NULL, 'obraz11.jpg', 1234286736),
(55, 16, NULL, NULL, NULL, 'obraz-uplnek-mysli13.jpeg', 1234286761),
(56, 16, NULL, NULL, NULL, 'obraz-uplnek-mysli14.jpeg', 1234286761),
(57, 16, NULL, NULL, NULL, 'obraz019.jpg', 1234286761),
(58, 16, NULL, NULL, NULL, 'obraz0110.jpg', 1234286762);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_sections`
--

CREATE TABLE IF NOT EXISTS `vypecky_sections` (
  `id_section` smallint(3) NOT NULL auto_increment,
  `label_cs` varchar(50) default NULL,
  `alt_cs` varchar(200) default NULL,
  `label_en` varchar(50) default NULL,
  `alt_en` varchar(200) default NULL,
  `label_de` varchar(50) default NULL,
  `alt_de` varchar(200) default NULL,
  `priority` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`id_section`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Vypisuji data pro tabulku `vypecky_sections`
--

INSERT INTO `vypecky_sections` (`id_section`, `label_cs`, `alt_cs`, `label_en`, `alt_en`, `label_de`, `alt_de`, `priority`) VALUES
(1, 'section 1', NULL, NULL, NULL, NULL, NULL, 0),
(2, 'section 2', NULL, NULL, NULL, NULL, NULL, 0),
(3, 'section 3', NULL, NULL, NULL, NULL, NULL, 0),
(4, 'section 4', NULL, NULL, NULL, NULL, NULL, 0),
(5, 'účet', NULL, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_texts`
--

CREATE TABLE IF NOT EXISTS `vypecky_texts` (
  `id_text` smallint(4) unsigned NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `text_cs` mediumtext,
  `changed_time` int(11) default NULL,
  `text_en` mediumtext,
  `text_de` mediumtext,
  PRIMARY KEY  (`id_text`),
  UNIQUE KEY `id_article` (`id_item`),
  FULLTEXT KEY `text_cs` (`text_cs`),
  FULLTEXT KEY `text_en` (`text_en`),
  FULLTEXT KEY `text_de` (`text_de`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Vypisuji data pro tabulku `vypecky_texts`
--

INSERT INTO `vypecky_texts` (`id_text`, `id_item`, `text_cs`, `changed_time`, `text_en`, `text_de`) VALUES
(9, 2, '<p>Chtěl bych zajít do restaurace, kina či divadla a pak najít čerpací stanici, obchod s elektronikou, a to právě v Příbrami, kde se aktuálně nacházím. Podobný problém může řešit řada z nás. Nacházíte-li se v místě, kde to dobře znáte, budete nejspíše vědět, kde všechna tato místa naleznete. Pokud však vycestujete do jiného, vám neznámého města, pak tento problém nastat může. Naštěstí nemusí, pokud znáte Lokola.cz.</p>\r\n<p> </p>\r\n<p>Lokola.cz je <a class="bbt_w" href="http://ad2.billboard.cz/extra/takeit/takeit_redirect.bb?idpool=7529766&amp;idserver=32210&amp;idsekce=1&amp;idpozice=1&amp;typbanneru=32&amp;idadword=1035&amp;idproduct=1960034&amp;idclient=26443384" target="_blank">nový</a> a nadějný projekt s prvky Web 2.0 aplikací sloužící k vyhledávání služeb typu restaurace, obchody, kina, bankomaty, nemocnice apod., v závislosti na poloze. Výsledkem je zobrazená mapa (používá mapy Google) s vyznačenými nejbližšími místy k vámi zadané lokalitě. Lokola.cz je přístupná jak z počítače, tak ze všech mobilních zařízení – Android, Java, Mobilní Web, Windows <a class="bbt_w" href="http://ad2.billboard.cz/extra/takeit/takeit_redirect.bb?idpool=9241638&amp;idserver=32210&amp;idsekce=1&amp;idpozice=1&amp;typbanneru=32&amp;idadword=3&amp;idproduct=2640292&amp;idclient=28503309" target="_blank">Mobile</a>, iPhone, BlackBerry apod.</p>\r\n<p> </p>\r\n<p>Vyhledávat přitom můžete už pouhým jedním kliknutím. Do pole "co" napíšete službu (např. restaurace) a do pole "kde" potom místo (např. Příbram). Projekt následně najde nejbližší výsledky u zadané adresy.</p>\r\n<p> </p>\r\n<p>Vyhledávat lze však i firmy a produkty. Právě díky mobilnímu přístupu je služba předurčena pro používání na cestách. Máte-li v zařízení i GPS, pak již stačí jen zadat, co hledáte, a nemusíte ani vědět, kde vlastně jste. Lokola.cz nalezne požadované nejblíže vašemu <a class="bbt_w" href="http://ad2.billboard.cz/extra/takeit/takeit_redirect.bb?idpool=9614131&amp;idserver=32210&amp;idsekce=1&amp;idpozice=1&amp;typbanneru=32&amp;idadword=2629&amp;idproduct=2421997&amp;idclient=27103854" target="_blank">místu</a>.</p>\r\n<p> </p>\r\n<p>Pro vyhledávání se používá interní databáze. Jestliže se vám tedy stane, že se nedostaví výsledky, které byste očekávali (přehlédne nějakou službu poblíž místa, některé specifické nemá zadané), pak je to pouze o tom, aby provozovatelé anebo vy kýženou informaci doplnili. Ano, i vy jako uživatel můžete pomáhat službu dále zkvalitňovat. Stačí využít odkaz Add business a postupně ve čtyřech krocích bod přidat.</p>', 1237817308, NULL, NULL),
(8, 1, '<p>Nový EVF ultrazoom Sony DSC-HX1 je dobře vybavený fotoaparát: disponuje snímačem Exmor CMOS, objektivem Sony G a umí natočit video v HD kvalitě. Model umožňuje až 20x optické přiblížení a je vybaven velkým výklopným LCD displejem. Představíme vám také ultrazoom X3 společnosti General Electric.</p>\r\n<p> </p>\r\n<p>U modelu <strong><a class="bbt_w" href="http://ad2.billboard.cz/extra/takeit/takeit_redirect.bb?idpool=9617752&amp;idserver=32210&amp;idsekce=1&amp;idpozice=1&amp;typbanneru=32&amp;idadword=121&amp;idproduct=953796&amp;idclient=45243042" target="_blank">Sony</a> DSC-HX1</strong> se podíváme nejprve na jeho optickou soustavu: firemní objektiv G je <strong>20x</strong> optický zoom. S modelem pořídíte i širokoúhlé záběry, protože nejkratší ohnisko je již na <strong>28 milimetrech</strong> (v přepočtu na kinofilm). Nepřehlédněte také například zajímavý režim "Panoramatický záběr", kdy se automaticky "poslepuje" sada snímků pořízená <a class="bbt_w" href="http://ad2.billboard.cz/extra/takeit/takeit_redirect.bb?idpool=8584767&amp;idserver=32210&amp;idsekce=1&amp;idpozice=1&amp;typbanneru=32&amp;idadword=1818&amp;idproduct=2584740&amp;idclient=25107232" target="_blank">velkou</a> <a class="bbt_w" href="http://ad2.billboard.cz/extra/takeit/takeit_redirect.bb?idpool=9767188&amp;idserver=32210&amp;idsekce=1&amp;idpozice=1&amp;typbanneru=32&amp;idadword=1434&amp;idproduct=3604435&amp;idclient=73182681" target="_blank">rychlostí</a> pro mimořádně široké snímky krajiny. Světelnost objektivu je na solidní úrovni: F2,8 - F5,2. Nechybí optický <strong>stabilizátor obrazu</strong>.</p>\r\n<p> </p>\r\n<p>Nově koncipovaný snímač Exmor CMOS (typu 1/2,4") zvyšuje citlivost a měl by potlačovat šum obrazu. Čip obsahuje cca <strong>9,1 milionů</strong> efektivně využitelných obrazových buněk. Citlivost čipu se pohybuje od ISO 80 do maximálních <strong>ISO 3200</strong>. Ozvučené video se ukládá ve formátu MPEG-4, frekvence 30 snímků za sekundu je dostupná i v maximální velikosti <strong>1 440 x 1 080 bodů</strong>.</p>\r\n<p> </p>\r\n<p>Co se týká funkčního vybavení této novinky, tak model DSC-HX1 umožňuje <strong>manuálně</strong> nastavit clonu a <a class="bbt_w" href="http://ad2.billboard.cz/extra/takeit/takeit_redirect.bb?idpool=9721347&amp;idserver=32210&amp;idsekce=1&amp;idpozice=1&amp;typbanneru=32&amp;idadword=2759&amp;idproduct=2516938&amp;idclient=28178424" target="_blank">čas</a>. Funkce Smile Shutter automaticky zahájí fotografování, jakmile se objekt usměje: nyní ještě snazší používání díky nastavitelné úrovni úsměvu. Praktická je také funkce Rozpoznání obličeje s volitelnou prioritou dospělí/děti, vysokorychlostní sledování objektu pro zřetelnější portréty. Zahrnuje funkci Detekce obličeje a pohybu a Paměť zvoleného obličeje. Inteligentní rozpoznávání scény zase usnadňuje fotografování výběrem nejlepšího nastavení pro obtížné fotografické podmínky, jako jsou portréty za soumraku a scény se zadním nasvícením.</p>\r\n<p> </p>\r\n<p>Pro některé uživatele bude novinka <a class="bbt_w" href="http://ad2.billboard.cz/extra/takeit/takeit_redirect.bb?idpool=8331595&amp;idserver=32210&amp;idsekce=1&amp;idpozice=1&amp;typbanneru=32&amp;idadword=20521&amp;idproduct=2516875&amp;idclient=27315819" target="_blank">atraktivní</a> i díky velkému (<strong>třípalcovému</strong>) barevnému LCD displeji, který je <strong>výklopný</strong>. Nechybí výstup HD pro sledování videa a fotografií na HD televizoru. Funkce Photomusic (prezentace v kvalitě HD) nabízí zábavné zobrazení fotografií a videoklipů HD s efekty a hudbou, a to na displeji LCD nebo připojeném televizoru.</p>', 1237816857, NULL, NULL),
(10, 5, '<p>Pod názvem Atomoví vyzvědači studené války vydává dlouholetý redaktor Mladé fronty a MF Dnes a spolupracovník Technet.cz <a href="http://technet.idnes.cz/novinari.asp?idnov=828">Karel Pacner</a> v nakladatelství Epocha koncem dubna <a class="bbt_w" href="http://ad2.billboard.cz/extra/takeit/takeit_redirect.bb?idpool=9581611&amp;idserver=32210&amp;idsekce=1&amp;idpozice=1&amp;typbanneru=32&amp;idadword=348&amp;idproduct=3371919&amp;idclient=25326473" target="_blank">knihu</a> o všech těchto peripetiích po roce 1945. Exkluzivně na Technetu si budete moci postupně přečíst pět ukázek.</p>\r\n<p>soustavu optickou</p>\r\n<h3 class="tit not4bbtext">První nukleární pokus</h3>\r\n<p>Pozorovací bod, který byl nejblíž sovětské atomové střelnici, měli na americkém konzulátu v Urumči-Tihwa v severozápadní Číně poblíž sovětských hranic. Douglas Mackierman z americké výzvědné služby CIA, podplukovník meteorologické služby v záloze, který vystupoval jako vicekonzul, tam na jaře 1949 instaloval zvukové detektory.</p>\r\n<p> </p>\r\n<p>Třebaže kvůli čínské občanské válce nařídil Washington v létě osazenstvu konzulátu, aby se stáhlo, zpravodajec mohl zůstat. Jeho <a class="bbt_w" href="http://ad2.billboard.cz/extra/takeit/takeit_redirect.bb?idpool=8584333&amp;idserver=32210&amp;idsekce=1&amp;idpozice=1&amp;typbanneru=32&amp;idadword=38&amp;idproduct=2198623&amp;idclient=15052001" target="_blank">práce</a> byla jedinečná – našel tam několik "bílých Rusů", jak se říkalo lidem, kteří už dávno uprchli před bolševiky, a ti mu teď ochotně pomáhali. Dokonce tajně pronikali až do oblasti Semipalatinska, kde Sověti budovali atomový polygon.</p>\r\n<p> </p>\r\n<p>V neděli 29. srpna 1949 Mackierman kontroloval detektory. Nejspíš ukázaly, že ráno uskutečnili Sověti první nukleární pokus. Zašifrovanou depeši okamžitě odeslal rádiem do USA. Avšak v centrále výzvědné služby v Langley u Washingtonu se na ni z neznámých důvodů nejméně měsíc nepodívali.</p>\r\n<p> </p>\r\n<p>První zprávu o této události tedy nedodal Mackierman, nýbrž meteorologická služba letectva z Aljašky. "Tu noc, co se bomba montovala, měl patrně ,bílé Rusy‘ i v Semipalatinsku," domnívá se fotoreportér Thomas Laird, který se mnoho let pohyboval po Číně a znal se s ním. "Je možné, že jeho agenti instalovali detektor zvuku několik mil od Semipalatinska."</p>\r\n<p> </p>\r\n<p>Americká monitorovací síť, kterou vybudoval generál Albert F. Hegenberger, nefungovala ve svých počátcích spolehlivě, to je přirozené. I proto se tím tajná služba raději nechlubí. Mnohem lepší výsledky vykazovalo letectvo. V pátek 3. září 1949 se vracel Boeing-29, označovaný jako WB-29, který patřil k 375. průzkumné meteorologické peruti, z letu nad Pacifikem.</p>\r\n<p> </p>\r\n<p>Přistál na Eielsonově letecké základně u města Fairbanks na Aljašce. Jeho posádka vedená poručíkem 1. třídy Robertem C. Johnsonem strávila ve vzduchu přes třináct hodin – dolétla k japonským břehům a zpátky. Byl to rutinní průzkumný let na zjišťování zvýšené radioaktivity v rámci operace Blueboy, která byla zahájena před rokem 12. května.</p>\r\n<p> </p>\r\n<p>V pondělí odvezl kurýrní letoun filtrační papíry do speciální laboratoře Tracerlab v Berkeley v Kalifornii. Američtí odborníci vyvinuli poměrně jednoduchou metodu na zjišťování přítomnosti radioaktivity v ovzduší. Přes filtrační papír se nasává venkovní vzduch a tím se zachycují jemné částečky prachu včetně radioaktivních částic z atomových výbuchů.</p>\r\n<p> </p>\r\n<h3 class="tit not4bbtext">Pod šifrou Ramona</h3>\r\n<p>Po svržení atomových bomb na Hirošimu a Nagasaki v srpnu 1945, vyvinutých v rámci projektu ukrytého pod názvem Manhattan, si Američané a Britové uvědomovali, že tuhle zbraň budou chtít vytvořit také Sověti. Proto se snažili získat informace o stavu jejich atomových výzkumů. V USA vedli tuto specializovanou špionáž pod krycím označením Ramona.</p>\r\n<p> </p>\r\n<p>Zdroje v terénu však neměli, protože Sovětský svaz byl policejní stát, kde jeden člověk hlídal druhého. Situaci proto odhadovali na základě nepřímých údajů. Zpočátku vycházeli především z výpovědí německých specialistů, kteří odjeli v létě 1945 pracovat do Sovětského svazu a občas se vraceli domů, a ze zpráv o <a class="bbt_w" href="http://ad2.billboard.cz/extra/takeit/takeit_redirect.bb?idpool=9293634&amp;idserver=32210&amp;idsekce=1&amp;idpozice=1&amp;typbanneru=32&amp;idadword=14145&amp;idproduct=2922580&amp;idclient=25586769" target="_blank">těžbě</a> uranu v Československu a východním Německu.</p>\r\n<p> </p>\r\n<p>Avšak tyhle údaje nebyly vždy spolehlivé. Západní Spojenci předpokládali, že Sověti se pustili s plnou vervou do vývoje atomových zbraní až po skončení války. A podle toho, kolik let trvalo jejich vědcům, než v tomto úsilí překonali spoustu nástrah a našli správnou <a class="bbt_w" href="http://ad2.billboard.cz/extra/takeit/takeit_redirect.bb?idpool=9618964&amp;idserver=32210&amp;idsekce=1&amp;idpozice=1&amp;typbanneru=32&amp;idadword=1934&amp;idproduct=3475780&amp;idclient=49622536" target="_blank">cestu</a>, odhadovali termín úspěchu Moskvy.</p>', 1237892966, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_userfiles`
--

CREATE TABLE IF NOT EXISTS `vypecky_userfiles` (
  `id_file` smallint(6) NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `id_article` smallint(6) NOT NULL,
  `id_user` smallint(5) unsigned NOT NULL default '1',
  `file` varchar(50) NOT NULL,
  `size` int(11) default NULL,
  `time` int(10) unsigned default NULL,
  PRIMARY KEY  (`id_file`),
  KEY `id_category` (`id_item`,`id_article`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;

--
-- Vypisuji data pro tabulku `vypecky_userfiles`
--

INSERT INTO `vypecky_userfiles` (`id_file`, `id_item`, `id_article`, `id_user`, `file`, `size`, `time`) VALUES
(31, 1, 1, 3, 'obraz-02.jpg', 55552, 1237142860);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_userimages`
--

CREATE TABLE IF NOT EXISTS `vypecky_userimages` (
  `id_file` int(4) NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `id_article` smallint(3) NOT NULL,
  `id_user` smallint(5) unsigned NOT NULL default '1',
  `file` varchar(50) NOT NULL,
  `width` smallint(6) default NULL,
  `height` smallint(6) default NULL,
  `size` int(11) default NULL,
  `time` int(11) default NULL,
  PRIMARY KEY  (`id_file`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=53 ;

--
-- Vypisuji data pro tabulku `vypecky_userimages`
--


-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_users`
--

CREATE TABLE IF NOT EXISTS `vypecky_users` (
  `id_user` smallint(5) unsigned NOT NULL auto_increment COMMENT 'ID uzivatele',
  `username` varchar(20) NOT NULL COMMENT 'Uzivatelske jmeno',
  `password` varchar(40) default NULL COMMENT 'Heslo',
  `id_group` smallint(3) unsigned default '3',
  `name` varchar(30) NOT NULL,
  `surname` varchar(30) NOT NULL,
  `mail` varchar(50) NOT NULL,
  `note` varchar(500) default NULL,
  `blocked` tinyint(1) NOT NULL default '0',
  `foto_file` varchar(30) default NULL,
  `deleted` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id_user`,`username`),
  KEY `id_group` (`id_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Vypisuji data pro tabulku `vypecky_users`
--

INSERT INTO `vypecky_users` (`id_user`, `username`, `password`, `id_group`, `name`, `surname`, `mail`, `note`, `blocked`, `foto_file`, `deleted`) VALUES
(1, 'admin', '084e0343a0486ff05530df6c705c8bb4', 1, 'Jakub', 'Matas', 'jakubmatas@gmail.com', 'administrátor', 0, NULL, 0),
(2, 'guest', 'guest', 2, 'host', 'host', '', 'host systému', 0, NULL, 0),
(3, 'cuba', '084e0343a0486ff05530df6c705c8bb4', 1, 'Jakub', 'Matas', 'jakubmatas@gmail.com', 'Normální uživatel', 0, 'cuba1.jpg', 0),
(4, 'delamancha', 'delamancha', 4, 'Jakub', 'Vémola', 'j.vemola@gmail.com', 'Jack', 0, 'jack.jpg', 0),
(13, 'slávka', 'SK1Tl7jq', 3, 'Iva', 'Korgerová', 'j.vemola@gmail.com', '', 0, NULL, 0),
(9, 'drobek', 'drobek', 3, 'Pavlík', 'Rybecký', 'ppavelrybecky@tiscali.cz', '', 0, '33-kubaakuba-dvojnik.jpg', 0),
(10, 'jeni013', 'jenicek8', 3, 'Honza', 'Liebel', 'honza.liebel@centrum.cz', '', 0, 'krtecek.jpg', 0),
(11, 'BSBVB', 'oligo', 3, 'Johnie', 'BSBVB', 'drimalt@seznam.cz', 'drimalt@sezman.cz\r\n - když já už sem si zvykl ten mejl dávat dycky dvakrát..', 0, NULL, 0),
(12, 'arivederci', 'h3d27GYQ', 3, 'Kateřina', 'Pardubová', 'katerina.pardubova@gmail.com', '', 0, NULL, 0),
(14, 'Šalvěj', 'dwXgzUFs', 3, 'Pavel', 'Daněk', 'paveldanek@seznam.cz', '', 0, NULL, 0),
(15, 'Šajtr', 'ville', 3, 'Pavel', 'Schreier', 'PavSch@seznam.cz', '', 0, NULL, 0),
(16, 'Zdenda benda', 'cepaj8or', 3, 'Zdenda', 'Kozák', 'zdenda.kozak@seznam.cz', '', 0, NULL, 0),
(17, 'usual_moron', 'usual_moron', 3, 'Michal', 'Čarnický', '', NULL, 0, 'images1.jpg', 0),
(19, 'Mikimyš', 'q4SMvs2f', 3, 'Kateřina', 'Novotná', 'katerinati@seznam.cz', 'Zdravím Výpečky! Lužánecká rulez!', 0, NULL, 0);
